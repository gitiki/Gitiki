---
title: Gitiki
---

This project is still in development.

## Presentation

Gitiki is an [Open Source PHP wiki engine][github] from [markdown](#markdown) files and a Git repository (or not).

### Why markdown? {#markdown}

[Markdown][] is a simple syntax to structure an information and easy to learn.
It is a natural choice to build a wiki!

## Features

* [History with Git](/extension/git.md)
* [Link wiki pages](/feature/index.md#link)
* [Specify id attributes on header block](/feature/index.md#header-id)
* [Include image](/feature/image.md)
* Table of Contents

## Extensions

Gitiki can be extended with [extensions](/extension/index.md).

## TODO

* Use HTTP Cache (expiration / validation)
* (Optional) Search with Elasticsearch
* Responsive interface

## About

This project is enhanced by [Silex][], [Symfony2 Yaml][yaml] and [Parsedown][].

[github]: https://github.com/gitiki/Gitiki/
[markdown]: http://daringfireball.net/projects/markdown/syntax
[silex]: http://silex.sensiolabs.org
[yaml]: http://symfony.com/doc/current/components/yaml/index.html
[parsedown]: http://parsedown.org
