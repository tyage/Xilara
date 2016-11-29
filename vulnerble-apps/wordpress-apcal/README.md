```sh
$ docker-compose up
```

Activate plugin and create new post with body: `[APCAL]`

(TODO: automate this)

```sh
$ ./poc.sh URL_OF_NEW_POST
```

Visit `/wp-admin/admin.php?page=appointment-calendar` and get alert.
