# SilverShop Colors

Product colours for [SilverShop](https://github.com/silvershop/silvershop-core) (Silverstripe CMS 6): a hex-swatch
colour palette, **multi-colour tags that describe a product**, hex colour values for **colour variations**, colour
swatches on the product page, and a **colour facet with swatches** for the storefront search.

> Supersedes the upstream `silvershop/coloredvariations`: it covers the same ground (hex colour values usable as
> variation attributes, swatch display) **and** adds product-level multi-colour tags + a search facet. Use this
> instead — not both. (It intentionally drops coloredvariations' per-colour product images: colour *variations*
> already carry their own images via core variation images / `silvershop/product-gallery`.)

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
  colours serves **both** as variation attribute values (colour *variations*) **and** as product-level colour tags —
  one shared "Red" across variations, tags, the PDP and the facet. Manage the palette under **Colours** in the CMS.
- **A product carries a `Colours` tag that describes it** — the colour(s) the item is made of, e.g. a red shirt with
  blue sleeves. Set them in the product form; usually one, tick several for a multi-colour item. (This is independent
  of variations — it is about what the product *is*, not what it is sold *in*.)
- **A variation's colour is a colour variation**, i.e. its "Shirt Colour" attribute. Define a colour attribute type
  whose values are `ShopColor`s, and each variation picks its colour through the normal attribute dropdown in the
  variations grid — no separate relation. A product sold *in* several colours uses these colour variations as usual.
- **Product page:** `<% include ProductColors %>` renders the product's colour tags as swatches.

## Colour facet (with `silvershop/search`)

When `silvershop/search` is installed, a **Colour facet** appears in search / category / brand navigation, rendered as
**swatch chips**. A product matches a colour when **it carries that colour as a product tag, or any of its variations
has it as a colour attribute value** — both resolve to one "Red" filter. Registered automatically, guarded, when both
modules are present.

## Roadmap

- **Colour families** (map shades like Crimson/Scarlet to a filterable "Red") — a later slice.
- **Migration task** from `silvershop/coloredvariations` for existing installs.

## License

BSD-3-Clause. See [LICENSE](LICENSE).
