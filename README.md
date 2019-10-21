# docker-cakephp
CakePHP 3.8 Articles tutorial with Docker

## Install cakePHP deps
`cd ~/docker-cakephp/cakephp && composer self-update && composer install`

Rename `config/app.default.php` into `config/app.php` and add the following `Datasource`

```
'host' => '172.17.0.2',
            'username' => 'myapp',
            'password' => 'myapp',
            'database' => 'myapp'
```

## Run containers
`cd ~/docker-cakephp/docker && docker-composer up`

