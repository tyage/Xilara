import http from 'http';
import zlib from 'zlib';
import httpProxy from 'http-proxy';
import { initDB, saveResponse } from './collector-db';

const startServer = async () => {
  await initDB();

  const webServer = process.env.APP_URL;
  const proxy = httpProxy.createServer({
    target: webServer
  });
  proxy.listen(8000);

  proxy.on('proxyRes', (proxyRes, req, res) => {
    console.log(`Receiving reverse proxy request for: ${req.url}`);

    const contentType = proxyRes.headers['content-type'];
    if (contentType === undefined || !(contentType.includes('text/html'))) {
      return;
    }

    let data = Buffer.alloc(0);
    proxyRes.on('data', (chunk) => {
      data = Buffer.concat([data, chunk]);
    });
    proxyRes.on('end', async () => {
      const isGzipped = /gzip/.test(proxyRes.headers['content-encoding']);
      if (isGzipped) {
        data = zlib.gunzipSync(data);
      }

      await saveResponse(req.method, req.url, data);
    });
  });
};
startServer();
