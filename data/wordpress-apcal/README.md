```sh
$ git lfs install
$ docker-compose up
```

Activate plugin and create new post with body: `[APCAL]`

(TODO: automate this)

```sh
$ ./poc.sh localhost:8000
```

Visit `/wp-admin/admin.php?page=appointment-calendar` and get alert.
