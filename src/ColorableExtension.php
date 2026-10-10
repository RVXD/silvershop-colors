<?php

declare(strict_types=1);

namespace SilverShop\Colors;

use SilverStripe\Core\Extension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\ListboxField;

/**
 * Makes any buyable colourable. Applied to BOTH Product and Variation (they share the Buyable contract — the same
 * single-extension-on-both pattern as silvershop/stock), it adds a `Colours` many_many so a product *or* a variation
 * can carry one or many colours (usually one; several for a multi-colour item like a striped shirt). Separate join
 * tables per owner are created automatically.
 *
 * @extends Extension<\SilverShop\Page\Product|\SilverShop\Model\Variation\Variation>
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
                'Colours this item comes in. Usually one; pick several for a multi-colour item (e.g. striped).'
            ));

        $fields->addFieldToTab('Root.Main', $field);
    }
}
