---
title: Git
---

Git extension add history on pages.

## How to install?

The Git extension is in Gitiki library. You should register the extension:

```
# .gitiki.yml
extensions:
    Gitiki\Git\GitExtension:
        git_dir: path/to/.git         # default the path to current wiki dir
        wiki_dir: path/to/wiki/in/git # default empty value
        git_binary: /path/to/git      # default git
        perl_binary: /path/to/perl    # default perl
```

## Highlight diff

If the path to Perl binary is nonnull, the diffs are highlighted.

You can disable this feature to set the path to perl binary with a null value:

```
Gitiki\Git\GitExtension:
    perl_binary: ~
```
