---
title: Redirector
---

If you rename a page, its URI change. It can be interesting to redirect your users to new page.

Also, you can keep links to an old page which redirect to an other.

## How to install?

With composer you must run this command `composer require gitiki/redirector`.

After, register the extension to Gitiki:

```
// .gitiki.yml
extensions:
    Gitiki\Redirector\RedirectorExtension: ~
```

If you move a page, its URI change. Hum… How I can redirect users on my new page?

## How to use?

You must use the meta data to specify the target page with `redirect` attribute:

    ---
    redirect: /features/index.md
    ---
