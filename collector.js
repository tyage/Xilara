const http = require('http');
const httpProxy = require('http-proxy');
const sqlite3 = require('sqlite3');
const crypto = require('crypto');
const fs = require('fs');

const webServer = `http://localhost:3000`
const proxy = httpProxy.createServer();

const tableName = 'responses';
const db = new sqlite3.Database('collector.db');

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
      create table ${tableName} (url text, url_hash string, html_hash string, created_at datetime);
      create index url_index on ${tableName}(url_hash);
      create index html_index on ${tableName}(html_hash);
    `);
  });
});

const saveResponse = (url, html) => {
  const urlHash = sha256Hex(url);
  const htmlHash = sha256Hex(html);

  // save to db
  db.get(`select count(*) from ${tableName} where url_hash = $url_hash and html_hash = $html_hash`, {
    $url_hash: urlHash,
    $html_hash: htmlHash
  }, (err, res) => {
    if (0 < res['count(*)']) {
      return;
    }

    const stmt = db.prepare(`insert into ${tableName} (url, url_hash, html_hash, created_at) values ($url, $url_hash, $html_hash, now())`);
    stmt.run({
      $url: url,
      $url_hash: urlHash,
      $html_hash: htmlHash
    });
  });

  // save to file
};

http.createServer((req, res) => {
  console.log(`Receiving reverse proxy request for: ${req.url}`);

  let data = '';
  proxy.on('proxyRes', (proxyRes) => {
    proxyRes.on('data', (chunk) => {
      data += chunk;
    });
  });
  proxy.on('end', () => {
    saveResponse(req.url, data);
  });

  proxy.web(req, res, { target: `${webServer}${req.url}` });
}).listen(8000);
