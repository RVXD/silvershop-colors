<?php

namespace SilverShop\Colors\Tests;

use SilverShop\Colors\ColorableExtension;
use SilverShop\Colors\ShopColor;
use SilverShop\Model\Variation\Variation;
use SilverShop\Page\Product;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\ListboxField;

class VariationColorColumnTest extends SapphireTest
{
    protected $usesDatabase = true;

    protected static bool $use_draft_site = true;

    public function testAddsInlineColoursColumn(): void
    {
        $fields = [];
        (new ColorableExtension())->updateVariationEditableColumns($fields);

        $this->assertArrayHasKey('Colours', $fields, 'an inline Colours column is contributed');
        $field = call_user_func($fields['Colours']['callback'], null, 'Colours', null);
        $this->assertInstanceOf(ListboxField::class, $field);
    }

    public function testListboxSaveIntoPersistsVariationColours(): void
    {
        // This is exactly what GridFieldEditableColumns does per row: build the field, then saveInto the record.
        $red = ShopColor::create(['Value' => 'Red']);
        $red->write();
        $blue = ShopColor::create(['Value' => 'Blue']);
        $blue->write();

        $product = Product::create(['Title' => 'Tee', 'BasePrice' => 10]);
        $product->write();
        $variation = Variation::create(['ProductID' => $product->ID]);
        $variation->write();

        $field = ListboxField::create('Colours', '', ShopColor::get()->map('ID', 'Value')->toArray());
        $field->setValue([$red->ID, $blue->ID]);
        $field->saveInto($variation);

        $ids = array_map('intval', Variation::get()->byID($variation->ID)->Colours()->sort('ID')->column('ID'));
        $this->assertSame([(int) $red->ID, (int) $blue->ID], $ids, 'inline save persists the many-many colours');
    }
}
