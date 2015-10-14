---
title: Installation
---

## Docker

To use easaly Gitiki, you can use our official Docker container: https://hub.docker.com/r/gitiki/gitiki/

You must share your wiki directory in container:

```bash
$ docker run --detach --name "some-gitiki" --volume "/your/wiki/path:/srv/wiki" --publish "1234:80" gitiki/gitiki
```

And go to http://localhost:1234!

## Composer

With composer, you must download the [Gitiki library from packagist][packagist]:

```bash
$ composer create-project --prefer-dist "gitiki/gitiki" "gitiki" "1.0.x-dev"
```

After, it is necessary to create your frontend controller:

```php
<?php // index.php

require_once __DIR__.'/../gitiki/vendor/autoload.php';

$app = new Gitiki\Gitiki(__DIR__.'/wiki');
$app->run();
```

**Do not forget to install [Gitiki extensions][extensions].**


[packagist]: https://packagist.org/packages/gitiki/gitiki
[extensions]: /extension/index.md
