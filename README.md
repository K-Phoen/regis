Regis ![PHP7 ready](https://img.shields.io/badge/PHP7-ready-green.svg) [![Build Status](https://travis-ci.org/K-Phoen/regis.svg?branch=master)](https://travis-ci.org/K-Phoen/regis) [![Coverage Status](https://coveralls.io/repos/github/K-Phoen/regis/badge.svg?branch=master)](https://coveralls.io/github/K-Phoen/regis?branch=master)
=====

Let Regis inspect your pull requests for style violations and other boring
details… you should be the one to do the real code review!

Regis is like your personal (and **self-hosted**) assistant, let him to the
tedious work and focus on what's important. He will **monitor** a configured set
of **repositories**, **analyse their pull requests** and directly **comment in
the code** when style violations or errors are found.

Installation
------------

Regis needs **PHP >= 7.0**.

Once the project is cloned, its dependencies can be installed using
[Composer](https://getcomposer.org/):

```
php composer.phar install
```

You will be asked to configure a few parameters like the configuration options
to use to connect to Redis, Rabbit MQ, etc.

If you use Docker, setting up these services will be easier. A `docker-compose.yml`
file is provided and can be launched using:

```
docker-compose -f docker/docker-compose.yml up
```

Regis is now accessible at http://localhost:8080/app_dev.php

### Tests

Run `make tests`

### Private repositories

In order to be able to inspect private repositories, Regis needs its own SSH
keys. They are usually stored in `./var/ssh`.
You can then declare a key to GitHub using the following command:

```
./bin/console regis:deploy-key:add --owner=K-Phoen --repository=regis-test --public-key=./var/ssh/id_rsa_test_regis.pub
```

License
-------

This project is under the [MIT](LICENSE) license.
