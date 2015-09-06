---
title: Features
---

## Link to another wiki page {#link}

| Syntax                         | Output                       |
|:-------------------------------|:-----------------------------|
| `[Features](features)`         | [Features](features)         |
| `[Anchor link](features#link)` | [Anchor link](features#link) |

## Redirect old page to new one {#redirect}

If you rename a page, this URI change. Hum… How I can redirect users on my new page?

You must use the meta data to specify the target page with `redirect` attribute:

```
---
redirect: features
---
```

Old links works always: `[Feature](feature)` display [Feature](feature)

## Specify id attribute on header {#header-id}

If you need to link your pages with anchor, you must use ID attribute `## Section title {#section-anchor}`.

## Include image {#image}

| Description                      | Syntax                                                  | Output                                                |
|----------------------------------|---------------------------------------------------------|-------------------------------------------------------|
| Original image                   | `![Cannelle](photos/cannelle.jpg)`                      | ![Cannelle](photos/cannelle.jpg)                      |
| Image resized (width specified)  | `![Cannelle resized 1](photos/cannelle.jpg?size=200)`   | ![Cannelle resized 1](photos/cannelle.jpg?size=200)   |
| Image resized (height specified) | `![Cannelle resized 2](photos/cannelle.jpg?size=x100)`  | ![Cannelle resized 2](photos/cannelle.jpg?size=x100)  |
| Image resized and cropped        | `![Cannelle cropped](photos/cannelle.jpg?size=200x100)` | ![Cannelle cropped](photos/cannelle.jpg?size=200x100) |
| Image without link               | `![Cannelle without link](photos/cannelle.jpg?link=no)`  | ![Cannelle without link](photos/cannelle.jpg?link=no)  |
| Image resized without link       | `![Cannelle resized without link](photos/cannelle.jpg?size=x100&link=no)` | ![Cannelle resized without link](photos/cannelle.jpg?size=x100&link=no) |

**Note**: To resize and crop an image, the [GD extension](http://php.net/manual/en/book.image.php) is required. If GD is not available, the original image will be returned.

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
