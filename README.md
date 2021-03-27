# PHP Client for PowerLink CRM

It's php wrapper for [PowerLink CRM Api](https://github.com/powerlink/Rest-API), that uses [Guzzle HTTP Framework](https://github.com/guzzle/guzzle).

## Install

```bash
composer install stelzer/php-powerlink
```

## Usage

```php
<?php

require_once "vendor/autoload.php";

use \PowerLink\PowerLink as API;

$payload = array('your payload');
$token_id = '<YOUR TOKEN ID>';
$client = new API($token_id);

$client->create();
```

## Methods

### Create

**Params**

- object_type — `string`
- params — `array`

```php
$object_type = 'crmorder';
$params = array('your object');
$client->create($object_type, $params);
```

### Update

**Params**

- object_type — `string`
- object_id — `int`
- params — `array`

```php
$object_type = 'crmorder';
$object_id = 1;
$params = array('your object');
$client->update($object_type, $object_id, $params);
```

### Delete

**Params**

- object_type — `string`
- object_id — `int`

```php
$client->delete($object_type, $object_id);
```

### Query

```php
use \PowerLink\PowerLink as API;
use \PowerLink\Query;

$token_id = '<YOUR TOKEN ID>';
$client = new API($token_id);

$query = new Query();
$query->setQuery(array(
    array('name', '=', '10'), 'AND', array('second_field', '>=', 20)
));

$query->setPageNumger(2);
$query->setPageSize(20);
$query->setFields(array('first_field', 'second_field'));
$query->setOrderBy('third_field', 'asc');

$client->query($query);
```
