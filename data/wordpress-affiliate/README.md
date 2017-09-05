```sh
$ git clone https://github.com/AffiliateWP/AffiliateWP.git -b 2.0.9 --depth=1
$ docker-compose up
$ docker exec -it wordpressaffiliate_wordpress_1 bash -c "echo \"define('WP_HOME', 'http://localhost:8000');define('WP_SITEURL','http://localhost:8000');\" >> /var/www/html/wp-config.php"
```

Activate plugin.

```sh
$ ./poc.sh localhost 8000
```

Visit `/wp-admin/index.php?page=cpd_metaboxes` and check Referer"
