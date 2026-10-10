# SilverShop Colors

Product colours for [SilverShop](https://github.com/silvershop/silvershop-core) (Silverstripe CMS 6): a hex-swatch
colour palette, **multi-colour tagging on any buyable** (product *or* variation), colour swatches on the product page,
and a **colour facet with swatches** for the storefront search.

> Supersedes the upstream `silvershop/coloredvariations`: it covers the same ground (hex colour values usable as
> variation attributes, swatch display) **and** adds direct multi-colour tags + a search facet. Use this instead — not
> both. (It intentionally drops coloredvariations' per-colour product images: colour *variations* already carry their
> own images via core variation images / `silvershop/product-gallery`.)

## Requirements

- Silverstripe CMS `^6.0`, SilverShop core `^6`
- `silvershop/search` `^1` *(optional)* — for the colour facet

## Installation

```bash
composer require silvershop/colors
```

Run `dev/build?flush=1`.

## How it works

- **`ShopColor`** is a colour value with a `Hex` swatch. It extends SilverShop's `AttributeValue`, so one palette of
  colours serves **both** as variation attribute values (colour *variations*) **and** as direct colour tags — one
  shared "Red" across variations, tags, the PDP and the facet. Manage the palette under **Colours** in the CMS.
- **Any buyable can be tagged with one or many colours.** A `Colours` relation is added to **both** Product and
  Variation (one `ColorableExtension` on both), so:
  - a product that simply *is* a colour (or several — a blue-and-red striped shirt) can say so without variations;
  - a variation can carry its own colour(s);
  - a product sold *in* several colours uses colour variations as usual.
  Set colours in the product form (and variation form) — usually one, tick several for multi-colour items.
- **Product page:** `<% include ProductColors %>` renders the buyable's colours as swatches.

## Colour facet (with `silvershop/search`)

When `silvershop/search` is installed, a **Colour facet** appears in search / category / brand navigation, rendered as
**swatch chips**. A product matches a colour when **it, or any of its variations, carries that colour** — whether as a
direct tag or a colour variation — so all three resolve to one "Red" filter. Registered automatically, guarded, when
both modules are present.

## Roadmap

- **Colour families** (map shades like Crimson/Scarlet to a filterable "Red") — a later slice.
- **Migration task** from `silvershop/coloredvariations` for existing installs.

## License

BSD-3-Clause. See [LICENSE](LICENSE).
