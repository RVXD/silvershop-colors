<?php

namespace SilverShop\Colors\Tests;

use SilverShop\Colors\ColorFacet;
use SilverShop\Colors\ShopColor;
use SilverShop\Model\Variation\Variation;
use SilverShop\Page\Product;
use SilverShop\Search\Query\SearchQuery;
use SilverStripe\Dev\SapphireTest;

class ColorFacetTest extends SapphireTest
{
    protected $usesDatabase = true;

    protected static bool $use_draft_site = true;

    private ShopColor $red;
    private ShopColor $blue;
    private ShopColor $green;

    private function seed(): void
    {
        $this->red = ShopColor::create(['Value' => 'Red', 'Hex' => 'ff0000']);
        $this->red->write();
        $this->blue = ShopColor::create(['Value' => 'Blue', 'Hex' => '0000ff']);
        $this->blue->write();
        $this->green = ShopColor::create(['Value' => 'Green', 'Hex' => '00ff00']);
        $this->green->write();

        // Product tagged Red directly (product-level / multi-colour case).
        $mug = Product::create(['Title' => 'Red Mug', 'BasePrice' => 10]);
        $mug->write();
        $mug->Colours()->add($this->red);

        // Product whose variation carries Blue as its colour attribute value.
        $shirt = Product::create(['Title' => 'Blue-variation Shirt', 'BasePrice' => 20]);
        $shirt->write();
        $v = Variation::create(['ProductID' => $shirt->ID]);
        $v->write();
        $v->AttributeValues()->add($this->blue);

        // Product with a second colour variation (ShopColor used as a variation attribute value).
        $tee = Product::create(['Title' => 'Green-attr Tee', 'BasePrice' => 30]);
        $tee->write();
        $gv = Variation::create(['ProductID' => $tee->ID]);
        $gv->write();
        $gv->AttributeValues()->add($this->green);
    }

    public function testMatchesProductTagAndColourVariation(): void
    {
        $this->seed();
        $facet = new ColorFacet();

        $this->assertSame(1, $facet->apply(Product::get(), [(string) $this->red->ID])->count(), 'direct product tag');
        $this->assertSame(1, $facet->apply(Product::get(), [(string) $this->blue->ID])->count(), 'colour variation');
        $this->assertSame(1, $facet->apply(Product::get(), [(string) $this->green->ID])->count(), 'colour variation');
        $this->assertSame(
            2,
            $facet->apply(Product::get(), [(string) $this->red->ID, (string) $this->green->ID])->count(),
            'multiple colours OR-ed'
        );
    }

    public function testOptionsCarrySwatchCountAndActive(): void
    {
        $this->seed();

        $options = (new ColorFacet())->getOptions(
            Product::get(),
            new SearchQuery(filters: ['color' => [(string) $this->red->ID]])
        );
        $byValue = [];
        foreach ($options as $option) {
            $byValue[$option['value']] = $option;
        }

        $this->assertSame('#ff0000', $byValue[(string) $this->red->ID]['swatch'], 'option carries the hex swatch');
        $this->assertSame(1, $byValue[(string) $this->red->ID]['count']);
        $this->assertTrue($byValue[(string) $this->red->ID]['active']);
    }

    public function testUnavailableWithoutColors(): void
    {
        $this->assertFalse((new ColorFacet())->isAvailable(), 'no colours defined → facet dormant');
    }
}
