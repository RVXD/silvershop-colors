<?php

declare(strict_types=1);

namespace SilverShop\Colors;

use SilverStripe\Core\Extension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\ListboxField;

/**
 * Product-level colours. Applied to Product only: a product carries a `Colours` many_many that *describes* it — the
 * colours it is made of (e.g. a red shirt with blue sleeves) — independent of variations.
 *
 * A *variation's* colour is its "Shirt Colour" attribute (a {@link ShopColor} value), edited through the existing
 * attribute dropdown in the variations grid — not a separate relation or column. (A live colour swatch on that
 * dropdown would be a client-side enhancement; see the module README.)
 *
 * @extends Extension<\SilverShop\Page\Product>
 */
class ColorableExtension extends Extension
{
    private static array $many_many = [
        'Colours' => ShopColor::class,
    ];

    public function updateCMSFields(FieldList $fields): void
    {
        $options = ShopColor::get()->map('ID', 'Value')->toArray();

        $field = ListboxField::create('Colours', _t(self::class . '.COLOURS', 'Colours'), $options)
            ->setDescription(_t(
                self::class . '.COLOURSDesc',
                'The colour(s) that describe this product — e.g. a red shirt with blue sleeves. '
                . 'For a product sold in different colours, add those as colour variations instead.'
            ));

        // Drop the auto-scaffolded Colours tab/grid and show a simple multi-select on the main tab, after Model.
        $fields->removeByName('Colours');
        if (!$fields->insertAfter('Model', $field, false)) {
            $fields->addFieldToTab('Root.Main', $field);
        }
    }
}
