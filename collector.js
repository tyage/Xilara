const http = require('http');
const httpProxy = require('http-proxy');
const sqlite3 = require('sqlite3');

const webServer = `http://localhost:3000`
const proxy = httpProxy.createServer();

const tableName = 'responses';
const db = new sqlite3.Database('collector.db');


db.serialize(() => {
  db.get('select count(*) from sqlite_master where type="table" and name=$name', {
    $name: tableName
  }, (err, res) => {
    if (0 == res['count(*)']) {
      db.exec(`
        create table ${tableName} (url text, url_hash string, html_hash string, created_at datetime);
        create index url_index on ${tableName}(url_hash);
        create index html_index on ${tableName}(html_hash);
      `);
    }
  });
});

const saveResponse = (url, html) => {
  db.get(`select from ${tableName} where url_hash = ? and html_hash = ?`, [ url_hash, html_hash ], (err, res) => {
    db.prepare(`insert into ${tableName} `)
  });
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
    console.log(data);
  });

  proxy.web(req, res, { target: `${webServer}${req.url}` });
}).listen(8000);
