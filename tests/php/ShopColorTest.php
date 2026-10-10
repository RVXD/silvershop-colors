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
}
