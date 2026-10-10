<?php

declare(strict_types=1);

namespace SilverShop\Colors;

use SilverStripe\Admin\ModelAdmin;

/**
 * CMS admin for the colour palette — create/edit {@link ShopColor}s (name + hex swatch) in one place, independent of
 * any product's variation attributes, so colours can be tagged on buyables even without colour variations.
 */
class ColorAdmin extends ModelAdmin
{
    private static string $url_segment = 'shop-colours';

    private static string $menu_title = 'Colours';

    private static array $managed_models = [
        ShopColor::class,
    ];
}
