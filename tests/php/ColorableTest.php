<?php

namespace SilverShop\Colors\Tests;

use SilverShop\Colors\ShopColor;
use SilverShop\Model\Variation\Variation;
use SilverShop\Page\Product;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\ListboxField;

class ColorableTest extends SapphireTest
{
    protected $usesDatabase = true;

    protected static bool $use_draft_site = true;

    public function testProductCarriesTheColoursTag(): void
    {
        $red = ShopColor::create(['Value' => 'Red', 'Hex' => 'ff0000']);
        $red->write();

        $product = Product::create(['Title' => 'Tee', 'BasePrice' => 10]);
        $product->write();
        $product->Colours()->add($red);

        $this->assertSame([$red->ID], $product->Colours()->column('ID'), 'product-level Colours many_many persists');

        $field = $product->getCMSFields()->dataFieldByName('Colours');
        $this->assertInstanceOf(ListboxField::class, $field, 'Colours is a multi-select, not the scaffolded grid');
    }

    public function testVariationHasNoColoursRelation(): void
    {
        // A variation's colour is its "Shirt Colour" attribute value, not a separate tag relation.
        $this->assertFalse(
            Variation::singleton()->hasMethod('Colours'),
            'the Colours tag is on products only, not variations'
        );
    }
}
