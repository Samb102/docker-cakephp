# docker-cakephp
CakePHP 3.8 Articles tutorial with Docker

## Install cakePHP deps
`cd ~/docker-cakephp/cakephp && composer self-update && composer install`

Rename `config/app.default.php` into `config/app.php` and configure the following `Datasource`

```
'Datasources' => [
            ...
            'host' => '172.17.0.2',
            'username' => 'myapp',
            'password' => 'myapp',
            'database' => 'myapp',
            ...
]   
```

## Run containers
`cd ~/docker-cakephp/docker && docker-composer up`

https://docs.docker.com/install/linux/docker-ce/ubuntu/