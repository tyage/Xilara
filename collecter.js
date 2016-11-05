const http = require('http'),
  httpProxy = require('http-proxy');

const proxy = httpProxy.createServer();
const webServer = `http://localhost:8000`

http.createServer((req, res) => {
  console.log(`Receiving reverse proxy request for: ${req.url}`);

  proxy.web(req, res, { target: `${webServer}${req.url}` });
}).listen(1234);
