---
title: Features
---

## Link to another wiki page {#link}

| Syntax                             | Output                           |
|------------------------------------|----------------------------------|
| `[Image](image.md)`              | [Image](image.md)              |
| `[Anchor link](image.md#resize)` | [Anchor link](image.md#resize) |

## Redirect old page to new one {#redirect}

If you rename a page, this URI change. Hum… How I can redirect users on my new page?

You must use the meta data to specify the target page with `redirect` attribute:

```
---
redirect: /feature/
---
```

Old links works always: `[features](/features.md)` display [features](/features.md)

## Specify id attribute on header {#header-id}

If you need to link your pages with anchor, you must use ID attribute `## Section title {#section-anchor}`.

## Include image {#image}

A specific [image page](image.md) has been created for this part.

## Highlight code {#highlight}

To highlight code, Gitiki use the [highligh.js](https://highlightjs.org) library.
You must set the language name after the started code block.

Example for *json* syntax:

    ```json
    {
        "user": {
            "username": "foobar",
            "fistname": "foo",
            "lastname": "bar"
        }
    }
    ```

Output:

```json
{
    "user": {
        "username": "foobar",
        "fistname": "foo",
        "lastname": "bar"
    }
}
```
