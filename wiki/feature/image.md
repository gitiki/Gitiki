---
title: Image
---

You can use image media in your pages with the markdown syntax: `![Alt text](path/to/image.jpg)`

For many reasons you would want to resize image, or add a direct link to the original image or not and this page explain the syntax.

**The path to an image can be relative (from the current page path: `../photos/cannelle.jpg`) or absolute (from the base path of wiki: `/photos/cannelle.jpg`).**

Original image
--------------

To include the original image, you must use the markdown syntax:

Absolute style: `![Cannelle](photos/cannelle.jpg)` or relative: `![Cannelle](../photos/cannelle.jpg)`

![Cannelle](../photos/cannelle.jpg)

Resize image {#resize}
------------

To resize an image, the [GD extension](http://php.net/manual/en/book.image.php) is required. If GD is not available, the original image will be returned.

To resize an image, you must add the `size` GET HTTP parameter.

### Resize by width

Absolute style: `![Cannelle resized by width](/photos/cannelle.jpg?size=200)` or relative: `![Cannelle resized by width](../photos/cannelle.jpg?size=200)`

![Cannelle resized by width](../photos/cannelle.jpg?size=200)

### Resize by height

Absolute style: `![Cannelle resized by height](/photos/cannelle.jpg?size=x100)` or relative: `![Cannelle resized by height](../photos/cannelle.jpg?size=x100)`

![Cannelle resized by height](../photos/cannelle.jpg?size=x100)

### Crop

Absolute style: `![Cannelle cropped](/photos/cannelle.jpg?size=200x100)` or relative: `![Cannelle cropped](../photos/cannelle.jpg?size=200x100)`

![Cannelle cropped](../photos/cannelle.jpg?size=200x100)

## Link image

By default, when you add an image, a link is added to go on specific image page (display image informations).

You can modify this behavior to remove link or add a direct link to image with `link` GET HTTP parameter. This parameter can be used with `size` parameter.

### Direct link

Absolute style: `![Cannelle without link](/photos/cannelle.jpg?link=direct&size=200)` or relative: `![Cannelle without link](../photos/cannelle.jpg?link=direct&size=200)`

![Cannelle without link](../photos/cannelle.jpg?link=direct&size=200)

### No link

Absolute style: `![Cannelle without link](/photos/cannelle.jpg?link=no&size=200)` or relative: `![Cannelle without link](../photos/cannelle.jpg?link=no&size=200)`

![Cannelle without link](../photos/cannelle.jpg?link=no&size=200)
