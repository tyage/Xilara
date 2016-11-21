const http = require('http');
const httpProxy = require('http-proxy');
const sqlite3 = require('sqlite3');
const crypto = require('crypto');
const fs = require('fs');

const webServer = process.env.APP_URL;
const proxy = httpProxy.createServer();

const tableName = 'responses';
const db = new sqlite3.Database('collector.db');
const fileDir = 'files'

const sha256Hex = (data) => {
  const hash = crypto.createHash('sha256');
  hash.update(data);
  return hash.digest('hex');
};

db.serialize(() => {
  db.get('select count(*) from sqlite_master where type="table" and name=$name', {
    $name: tableName
  }, (err, res) => {
    if (0 < res['count(*)']) {
      return;
    }

    db.exec(`
      create table ${tableName} (request text, request_hash string, html_hash string, created_at datetime);
      create index request_index on ${tableName}(request_hash);
      create index html_index on ${tableName}(html_hash);
    `);
  });
});

const saveResponse = (method, url, html) => {
  const request = [method, url].join(' ');
  const requestHash = sha256Hex(request);
  const htmlHash = sha256Hex(html);

  // save to db
  db.get(`select count(*) from ${tableName} where request_hash = $request_hash and html_hash = $html_hash`, {
    $request_hash: requestHash,
    $html_hash: htmlHash
  }, (err, res) => {
    if (0 < res['count(*)']) {
      return;
    }

    const stmt = db.prepare(`insert into ${tableName} (request, request_hash, html_hash, created_at) values ($request, $request_hash, $html_hash, datetime('now'))`);
    stmt.run({
      $request: request,
      $request_hash: requestHash,
      $html_hash: htmlHash
    });
  });

  // save to file
  fs.writeFile(`${fileDir}/${htmlHash}`, html, { flag: 'wx' }, err => {
    // file already exists if error happens
  });
};

http.createServer((req, res) => {
  console.log(`Receiving reverse proxy request for: ${req.url}`);

  res.on('pipe', (proxyRes) => {
    let data = '';
    proxyRes.on('data', (chunk) => {
      data += chunk;
    });
    proxyRes.on('end', () => {
      const contentType = proxyRes.headers['content-type'];
      if (contentType === undefined || !(contentType.includes('text/html'))) {
        return;
      }

      saveResponse(req.method, req.url, data);
    });
  });

  proxy.web(req, res, { target: webServer });
}).listen(8000);
