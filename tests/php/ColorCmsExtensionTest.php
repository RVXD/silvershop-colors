<?php

namespace SilverShop\Colors\Tests;

use SilverShop\Colors\ColorCmsExtension;
use SilverShop\Colors\ShopColor;
use SilverStripe\Dev\SapphireTest;

class ColorCmsExtensionTest extends SapphireTest
{
    protected $usesDatabase = true;

    public function testColorMapIsHexKeyedByIdAndSkipsColoursWithoutHex(): void
    {
        $red = ShopColor::create(['Value' => 'Red', 'Hex' => 'ff0000']);
        $red->write();
        $plain = ShopColor::create(['Value' => 'Unset']); // no hex
        $plain->write();

        $map = (new ColorCmsExtension())->getColorMap();

        $this->assertSame('#ff0000', $map[$red->ID] ?? null, 'swatch keyed by AttributeValue id');
        $this->assertArrayNotHasKey($plain->ID, $map, 'colours without a hex are omitted');
    }
}
