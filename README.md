# JSON Merge Patch for PHP

[![Build Status](https://secure.travis-ci.org/raphaelstolt/json-merge-patch.png)](http://travis-ci.org/raphaelstolt/json-merge-patch) [![Version](http://img.shields.io/packagist/v/stolt/json-merge-patch.svg?style=flat)](https://packagist.org/packages/stolt/json-merge-patch) [![PHP Version](http://img.shields.io/badge/php-5.4+-ff69b4.svg)](https://packagist.org/packages/stolt/json-merge-patch)

This is an(other) implementation of [JSON Merge Patch](https://tools.ietf.org/html/rfc7396) written in PHP. For a PHP 5.3 compatible version please use the implementation by [@clue](https://github.com/clue/php-json-merge-patch).

### Installation via Composer
``` bash
$ composer require stolt/json-merge-patch
```

### Usage

Now you can use JSON Merge Patch for PHP via the available Composer **autoload file**.

### Apply a patch
```php
<?php require_once 'vendor/autoload.php';

use Rs\Json\Merge\Patch;

$targetDocument = json_decode('{"title":"Goodbye!","author":{"givenName":"John","familyName":"Doe"},"tags":["example","sample"],"content":"This will be unchanged"}');

$patchDocument = json_decode('{"title":"Hello!","phoneNumber":"+01-123-456-7890","author":{"familyName":null},"tags":["example"]}');

$patchedDocument = (new Patch())->apply(
    $targetDocument,
    $patchDocument
); // '{"title":"Hello!","author":{"givenName":"John"},"tags":["example"],"content":"This will be unchanged","phoneNumber":"+01-123-456-7890"}'
```

### Generate a patch document
```php
<?php require_once 'vendor/autoload.php';

use Rs\Json\Merge\Patch;

$sourceDocument = json_decode('{"a":"b","b":"c"}');
$targetDocument = json_decode('{"b":"c"}');

$generatedPatchDocument = (new Patch())->generate(
    $sourceDocument,
    $targetDocument
); // '{"a":null}'
```

### Merge patch documents
```php
<?php require_once 'vendor/autoload.php';

use Rs\Json\Merge\Patch;

$patchDocument1 = json_decode('{"a":"b"}');
$patchDocument2 = json_decode('{"b":"c"}');

$mergedPatchDocument = (new Patch())->merge(
    $patchDocument1,
    $patchDocument2
); // '{"a":"b","b":"c"}'
```

### Running tests
``` bash
$ composer test
```
### License
This library is licensed under the MIT license. Please see [LICENSE](LICENSE.md) for more information.

### Changelog
Please see [CHANGELOG](CHANGELOG.md) for more information.

### Contributing
Please see [CONTRIBUTING](CONTRIBUTING.md) for more details.
