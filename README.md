NzoElkBundle
============

[![Build Status](https://travis-ci.org/nayzo/NzoElkBundle.svg?branch=master)](https://travis-ci.org/nayzo/NzoElkBundle)
[![Latest Stable Version](https://poser.pugx.org/nzo/elk-bundle/v/stable)](https://packagist.org/packages/nzo/elk-bundle)

The **NzoElkBundle** is a Symfony Bundle used to manage the logs with the **ELK** stack (Elasticsearch, Logstash, Kibana).

##### Compatible with **Symfony >= 4.4**


Installation
------------

### Through Composer:

```
$ composer require nzo/elk-bundle
```

### Register the bundle in config/bundles.php (without Flex):

``` php
// config/bundles.php

return [
    // ...
    Nzo\ElkBundle\NzoElkBundle::class => ['all' => true],
];
```

### Configure your application's config.yml:

``` yml
# config/packages/nzo_elk.yaml
nzo_elk:
    app_name: '%env(ELK_APP_NAME)%'
    app_environment: '%env(ELK_APP_ENVIRONMENT)%'

    log_encryptor:                               # Optional
        secret_key: '%env(ELK_LOG_SECRET)%'      # Required
        fields:                                  # Required 
            - email
            - username
            - ...


# .env
ELK_APP_NAME=app
ELK_APP_ENVIRONMENT=local            
```

Usage
-----

##### Using the JSON formatter

In the definition of the handlers, simply add the `nzo.elk.monolog.formatter` formatter.

Example :

```yaml

api_errors:
    type: stream
    path: '%kernel.logs_dir%/%kernel.environment%.elk_api_errors.log'
    level: errors
    channels: ['api']
    formatter: nzo.elk.monolog.formatter

```

Encrypt Logs
------------

This bundle offer a secure way to encrypt sensitive data sent in the logs.

To do so, You must enable and set the **log_encryptor** configuration and the **secret_key**.

In the **fields** configuration you must add the logs context fields that you want to be encrypted.
These fields must be shared in the ELK stack in order to enable the decryption for them.

##### Setup:
```php
// config/bundles.php

return [
    // ...
    Nzo\UrlEncryptorBundle\NzoUrlEncryptorBundle::class => ['all' => true],
];
```

##### Configuration:

```yaml
# config/packages/nzo_elk.yaml
nzo_elk:
    # ...
    log_encryptor:
        secret_key: '%env(ELK_LOG_SECRET)%'
        fields:
            - email
            - username
            - location.address.code



# .env
ELK_LOG_SECRET=SOME_SECRET
```

##### Usage:

```php
public function log()
{
    $context = [
        'name' => 'Wolverine',
        'username' => 'test',
        'email' => 'test@example.fr',
        'location' => [
            'address' => [
                'code' => '75000',
                'city' => 'Paris',
                'country' => 'France'
            ]
        ]
    ];

    $this->logger->error('Error', $context);
}

// The log output will be like:
[
    'name' => 'Wolverine',
    'username' => 'FbEtXzIRop0FFK31MdC+McgbWybD...',
    'email' => 'DNXDcuQDn7LbwlgLKnAgPsn...',
    'location' => [
        'address' => [
            'code' => 'FnzOIHjMZDzDmSSC...',
            'city' => 'pdjKJBDfd2Khdfkhbfk....',
            'country' => 'France'
        ]
    ]
]
```

License
-------

This bundle is under the MIT license. See the complete license in the bundle:

See [LICENSE](https://github.com/nayzo/NzoElkBundle/tree/master/LICENSE)
