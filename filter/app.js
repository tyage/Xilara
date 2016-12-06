const htmlparser = require('htmlparser2');
const http = require('http');
const httpProxy = require('http-proxy');

const { initDB, saveResponse, getHTMLs } = require('./collector');
const { createModel } = require('./normalize.js');

const webServer = process.env.APP_URL;
const proxy = httpProxy.createServer();

initDB();

const parseHTML = (html) => {
  return new Promise((resolve, reject) => {
    const handler = new htmlparser.DomHandler((error, dom) => resolve(dom));
    const parser = new htmlparser.Parser(handler);
    parser.write(html);
    parser.done();
  });
};

const filterData = (req, data) => {
  saveResponse(req.method, req.url, data).then(() => {
    return getHTMLs(req.method, req.url);
  }).then(htmls => {
    return Promise.all(htmls.map(html => parseHTML(html)));
  }).then(parsedHTMLs => {
    return createModel(parsedHTMLs);
  }).then(model => {
    console.log(model);
  }).catch(error => {
    console.log(error);
  });
};

http.createServer((req, res) => {
  console.log(`Receiving reverse proxy request for: ${req.url}`);

  res.on('pipe', (proxyRes) => {
    const contentType = proxyRes.headers['content-type'];
    if (contentType === undefined || !(contentType.includes('text/html'))) {
      return;
    }

    let data = Buffer.alloc(0);
    proxyRes.on('data', (chunk) => {
      data = Buffer.concat([data, chunk]);
    });
    proxyRes.on('end', () => {
      const isGzipped = /gzip/.test(proxyRes.headers['content-encoding']);
      if (isGzipped) {
        data = zlib.gunzipSync(data);
      }

      filterData(req, data);
    });
  });

  proxy.web(req, res, { target: webServer });
}).listen(8000);
