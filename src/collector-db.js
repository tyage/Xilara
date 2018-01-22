import sqlite3 from 'sqlite3';
import crypto from 'crypto';
import fs from 'fs';
import zlib from 'zlib';

const tableName = 'responses';
const db = new sqlite3.Database('htmls.db');
const fileDir = 'htmls';

const sha256Hex = (data) => {
  const hash = crypto.createHash('sha256');
  hash.update(data);
  return hash.digest('hex');
};

export const initDB = () => {
  return new Promise((resolve, reject) => {
    db.serialize(() => {
      db.get('select count(*) from sqlite_master where type="table" and name=$name', {
        $name: tableName
      }, (err, res) => {
        if (0 < res['count(*)']) {
          resolve();
          return;
        }

        db.exec(`
          create table ${tableName} (request text, request_hash string, html_hash string, created_at datetime);
          create index request_index on ${tableName}(request_hash);
          create index html_index on ${tableName}(html_hash);
        `, (err, res) => {
          resolve();
        });
      });
    });
  });
};

const isResponseExists = (request, html) => {
  const requestHash = sha256Hex(request);
  const htmlHash = sha256Hex(html);
  return new Promise((resolve, reject) => {
    // save to db
    db.get(`select count(*) from ${tableName} where request_hash = $request_hash and html_hash = $html_hash`, {
      $request_hash: requestHash,
      $html_hash: htmlHash
    }, (err, res) => {
      resolve(0 < res['count(*)']);
    });
  });
};
const saveResponseToDB = (request, html) => {
  const requestHash = sha256Hex(request);
  const htmlHash = sha256Hex(html);
    console.log(request)
  return new Promise((resolve, reject) => {
    const stmt = db.prepare(`insert into ${tableName} (request, request_hash, html_hash, created_at) values ($request, $request_hash, $html_hash, datetime('now'))`);
    stmt.run({
      $request: request,
      $request_hash: requestHash,
      $html_hash: htmlHash
    }, () => {
      resolve();
    });
  });
};
export const saveResponse = async (method, url, html) => {
  const request = [ method, url ].join(' ');

  const responseExists = await isResponseExists(request, html);
  if (responseExists) {
    return;
  }

  await saveResponseToDB(request, html);

  try {
    fs.writeFileSync(`${fileDir}/${htmlHash}`, html, { flag: 'wx' });
  } catch(e) {
    // if file already exists, error happens
  }
};


export const getHTMLs = (method, url) => {
  const request = [method, url].join(' ');
  const requestHash = sha256Hex(request);

  return new Promise((resolve, reject) => {
    db.all(`select html_hash from ${tableName} where request_hash = $request_hash`, {
      $request_hash: requestHash
    }, (err, rows) => {
      const htmlHashes = rows.map(res => res.html_hash);
      const uniqueHTMLHashes = [...new Set(htmlHashes)];
      const htmls = uniqueHTMLHashes.map(hash => fs.readFileSync(`${fileDir}/${hash}`));
      resolve(htmls);
    });
  });
};
