Regis ![PHP7 ready](https://img.shields.io/badge/PHP7-ready-green.svg) [![Build Status](https://travis-ci.org/K-Phoen/regis.svg?branch=master)](https://travis-ci.org/K-Phoen/regis)
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
php composer.phar intall
```

You will be asked to configure a few parameters like the configuration options
to use to connect to Redis, Rabbit MQ, etc.

If you use Docker, setting up these services will be easier. A `docker-compose.yml`
file is provided and can be launched using:

```
docker-compose -f docker/docker-compose.yml up
```

Configuration
-------------

The repositories to inspect are configured in the `app/config/bundles/regis_webhooks.yml` file.
Regis expects a list of tuples repository/secret following the schema:

```yaml
regis_webhooks:
  # …

  repositories:
    -
      # for the repository https://github.com/K-Phoen/foo
      identifier: K-Phoen/foo
      # a shared secret that will be used to secure the communications between Regis and GitHub
      secret: '%test_repo_secret%'
```

Once a repository has been configured, the following command automatically
configures a webhook used to make GitHub call Regis whenever an interesting event
occurs in the repository:

```
./bin/console regis:webhooks:create --owner=K-Phoen --repository=foo --url=http://my.public.regis.host
```

License
-------

This project is under the [MIT](LICENSE) license.
