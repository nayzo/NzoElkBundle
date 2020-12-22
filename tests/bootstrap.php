<?php

if (!($loader = @include __DIR__ . '/../vendor/autoload.php')) {
    echo <<<'EOT'
Composer is not loaded:
$ curl -s https://getcomposer.org/installer | php
$ php composer.phar install --dev
$ phpunit
EOT;
    exit(1);
}
