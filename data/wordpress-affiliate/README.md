## Startup

```sh
$ git clone https://github.com/AffiliateWP/AffiliateWP.git -b 2.0.9 --depth=1
$ docker-compose up
$ docker exec -it wordpressaffiliate_wordpress_1 bash -c "echo \"define('WP_HOME', 'http://localhost:8000');define('WP_SITEURL','http://localhost:8000');\" >> /var/www/html/wp-config.php"
```

## Preparation

1. Activate plugin.
  - Note: `admin` is valid for Affiliate field
2. Add Referrals.

## PoC

```sh
$ node ./poc.js localhost 8000
```
