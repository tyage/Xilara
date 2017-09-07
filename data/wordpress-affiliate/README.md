## Startup

```sh
$ git clone https://github.com/AffiliateWP/AffiliateWP.git -b 2.0.9 --depth=1
$ docker-compose up
$ docker exec -it wordpressaffiliate_wordpress_1 bash -c "echo \"define('WP_HOME', 'http://localhost:8000');define('WP_SITEURL','http://localhost:8000');\" >> /var/www/html/wp-config.php"
```

## Collect safe

Collect http://localhost:8000/wp-admin/admin.php?page=affiliate-wp-referrals

### Result

- [safe-1](datasets/safe-1.html): no referral
- [safe-2](datasets/safe-2.html): one referral
- [safe-3](datasets/safe-3.html): two referrals
- [safe-4](datasets/safe-4.html): three referrals
- [safe-5](datasets/safe-5.html): with filter
- [safe-6](datasets/safe-6.html): with another cookie

## Collect XSSed

### Preparation

1. Activate plugin.
  - Note: `admin` is valid for Affiliate field
2. Add Referrals.

### Run PoC

```sh
$ node ./poc.js localhost 8000
```
