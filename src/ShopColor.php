<?php

declare(strict_types=1);

namespace SilverShop\Colors;

use SilverShop\Model\Variation\AttributeValue;
use SilverStripe\Forms\FieldList;

/**
 * A colour with a hex swatch. Extends {@link AttributeValue}, so one palette of ShopColors serves both as variation
 * attribute values (colour *variations*) and as the targets of the direct {@link ColorableExtension::Colours} tags —
 * giving a single "Red" shared by variations, tags, the PDP and the colour facet.
 *
 * @property string $Hex
 */
class ShopColor extends AttributeValue
{
    private static string $table_name = 'SilverShop_ShopColor';

    private static string $singular_name = 'Colour';

    private static string $plural_name = 'Colours';

    private static array $db = [
        'Hex' => 'Varchar(7)',
    ];

    private static array $summary_fields = [
        'Value' => 'Name',
        'Hex' => 'Hex',
    ];

    public function getCMSFields(): FieldList
    {
        $fields = parent::getCMSFields();

        $hex = $fields->dataFieldByName('Hex');
        if ($hex) {
            // Native HTML5 colour picker for a handy swatch choice.
            $hex->setAttribute('type', 'color')
                ->setDescription(_t(self::class . '.HexDesc', 'Swatch colour shown in pickers and the colour filter.'));
        }

        return $fields;
    }

    /**
     * A CSS colour for a swatch chip (normalised with a leading #), or null when unset.
     */
    public function getSwatch(): ?string
    {
        $hex = trim((string) $this->Hex);
        if ($hex === '') {
            return null;
        }

        return str_starts_with($hex, '#') ? $hex : '#' . $hex;
    }
}
