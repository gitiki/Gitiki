~~~
title: Features
~~~

## Link to another wiki page {#link}

| Syntax                         | Output                       |
|:-------------------------------|:-----------------------------|
| `[Features](features)`         | [Features](features)         |
| `[Anchor link](features#link)` | [Anchor link](features#link) |

## Redirect old page to new one {#redirect}

If you rename a page, this URI change. Hum… How I can redirect users on my new page?

You must use the meta data to specify the target page with `redirect` attribute:

``` md
~~~
redirect: features
~~~
```

Old links works always: `[Feature](feature)` display [Feature](feature)

## Specify id attribute on header {#header-id}

If you need to link your pages with anchor, you must use ID attribute `## Section title {#section-anchor}`.

## Include image {#image}

| Syntax                           | Output                         |
|:---------------------------------|:-------------------------------|
| `![Alt text](photos/avatar.jpg)` | ![Alt text](photos/avatar.jpg) |
