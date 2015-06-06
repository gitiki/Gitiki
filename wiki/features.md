## Link to another wiki page {#link}

| Syntax                          | Output                         |
|:--------------------------------|:-------------------------------|
| `[[features]]`                  | [[features]]                   |
| `[[features|other label]]`      | [[features\|other label]]      |
| `[[features#link|anchor link]]` | [[features#link\|anchor link]] |

## Redirect old page to new one {#redirect}

If you rename a page, this URI change. Hum… How I can redirect users on my new page?

You must use the meta file to specify the redirect page:

~~~ yaml
# feature.meta
title: Feature
redirect: features
~~~

Old links works always: [[feature]]

## Specify id attribute on header {#header-id}

If you need to link your pages with anchor, you must use ID attribute `## Section title {#section-anchor}`.

After you can add `#section-anchor` on your link like that `[[page-name#section-anchor]]`.
