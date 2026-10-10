<?php

declare(strict_types=1);

namespace SilverShop\Colors;

use SilverShop\Search\Facet\SearchFacet;
use SilverShop\Search\Query\SearchQuery;
use SilverStripe\ORM\DataList;

/**
 * Colour facet for silvershop/search, with swatch chips. Ships here and registers into the search FacetRegistry only
 * when silvershop/search is installed (see _config/search.yml). A product matches a colour when it, or any of its
 * variations, carries that colour — via the direct {@link ColorableExtension::Colours} tags (product and variation)
 * or a colour *variation* (`Variation.AttributeValues`). Since all three resolve to {@link ShopColor} ids, they merge
 * into one option per colour. Each option carries the colour's hex so the facet renders a swatch.
 */
class ColorFacet implements SearchFacet
{
    public function getName(): string
    {
        return 'color';
    }

    public function getTitle(): string
    {
        return _t(self::class . '.TITLE', 'Colour');
    }

    public function isAvailable(): bool
    {
        return ShopColor::get()->exists();
    }

    public function apply(DataList $products, array $selected): DataList
    {
        $ids = array_values(array_filter(array_map('intval', $selected)));
        if (!$ids) {
            return $products;
        }

        return $products->filterAny([
            'Colours.ID' => $ids,
            'Variations.Colours.ID' => $ids,
            'Variations.AttributeValues.ID' => $ids,
        ]);
    }

    public function getOptions(DataList $context, SearchQuery $query): array
    {
        $selected = array_map('strval', $query->selected($this->getName()));
        $options = [];

        foreach (ShopColor::get() as $color) {
            $count = (int) $this->apply($context, [$color->ID])->count();
            $value = (string) $color->ID;
            $active = in_array($value, $selected, true);
            if ($count === 0 && !$active) {
                continue;
            }
            $options[] = [
                'value' => $value,
                'label' => $color->Value,
                'count' => $count,
                'active' => $active,
                'swatch' => $color->getSwatch(),
            ];
        }

        return $options;
    }
}
