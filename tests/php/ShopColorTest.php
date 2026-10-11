<?php

namespace SilverShop\Colors\Tests;

use SilverShop\Colors\ShopColor;
use SilverStripe\Dev\SapphireTest;

class ShopColorTest extends SapphireTest
{
    protected $usesDatabase = true;

    public function testSwatchNormalisesHex(): void
    {
        $this->assertSame('#c0392b', ShopColor::create(['Value' => 'Red', 'Hex' => 'c0392b'])->getSwatch());
        $this->assertSame('#2980b9', ShopColor::create(['Value' => 'Blue', 'Hex' => '#2980b9'])->getSwatch());
        $this->assertNull(ShopColor::create(['Value' => 'Unset'])->getSwatch());
    }

    public function testSwatchIndicatorRendersColourOrNothing(): void
    {
        $indicator = (string) ShopColor::create(['Value' => 'Red', 'Hex' => 'ff0000'])->getSwatchIndicator();
        $this->assertStringContainsString('background:#ff0000', $indicator, 'admin list swatch carries the colour');

        $this->assertSame('', (string) ShopColor::create(['Value' => 'Unset'])->getSwatchIndicator(), 'no swatch without a hex');
    }
}
