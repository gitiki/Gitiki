---
title: Code Highlight
---

This extension add syntax highlighting on code blocks.

## How to install?

With composer you must run this command `composer require gitiki/code-highlight`.

After register the ServiceProvider to Gitiki:

```php
// index.php
$app = new Gitiki\Gitiki();
$app->register(new Gitiki\CodeHighlight\CodeHighlightServiceProvider());

$app->run();
```

## How to use?

In your markdown file you start a code block with the language name:

    ```php
    class HelloWorld
    {
    }
    ```

To see the list of languages supported, you must refer to git repository: https://github.com/gitiki/code-highlight/tree/master/src/Resources/highlightjs/languages

## How to change style?

The extension have `style` option to set the style name:

```php
$app->register(new Gitiki\CodeHighlight\CodeHighlightServiceProvider());
$app['code_highlight'] = [
    'style' => 'tomorrow', // default style
];
```

To see the list of styles, you must refer to git repository: https://github.com/gitiki/code-highlight/tree/master/src/Resources/highlightjs/styles
