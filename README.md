Git PHP Wrapper
=======

[![Build Status](https://travis-ci.org/scottrobertson/php-git.png?branch=master)](https://travis-ci.org/scottrobertson/php-git)

This is a very simple PHP wrapper for Git. It contains a limited set of functionality right now, and things will be added as I (and others) need them.


## Example
```php
<?php
require_once __DIR__ . '/vendor/autoload.php';

$repo = new \ScottRobertson\Git\Repository(
    new \ScottRobertson\Git\Command(
        '/tmp/data/php-git'
    ),
    'https://github.com/scottrobertson/php-git.git'
);

print_r($repo->getCommits());

```
