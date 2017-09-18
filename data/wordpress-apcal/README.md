```sh
$ git lfs install
$ unzip appointment-calendar.2.7.4.zip
$ docker-compose up
```

Activate plugin and create new post with body: `[APCAL]`

(TODO: automate this)

```sh
$ ./poc.sh localhost:8000
```

Visit `/wp-admin/admin.php?page=appointment-calendar` and get alert.
