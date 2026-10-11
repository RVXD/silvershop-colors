<?php

declare(strict_types=1);

namespace SilverShop\Colors;

use SilverStripe\Core\Extension;
use SilverStripe\View\Requirements;

/**
 * Loads the CMS-only colour-swatch decorator (a small vanilla-JS enhancement — no React build) plus a
 * `window.ShopColorMap` ({ attributeValueId: '#hex' }), so the admin shows a swatch next to each colour in the
 * product "Colours" listbox and the variation "Shirt Colour" attribute dropdown. Applied to LeftAndMain.
 *
 * The JS/CSS live as plain files under client/ and are inlined (rather than exposed) so the module needs no
 * vendor-expose step; the decorator's MutationObserver persists across the CMS's pjax navigation.
 *
 * @extends Extension<\SilverStripe\Admin\LeftAndMain>
 */
class ColorCmsExtension extends Extension
{
    /**
     * @return array<int,string> AttributeValue id => '#hex' swatch, for every ShopColor that has a swatch.
     */
    public function getColorMap(): array
    {
        $map = [];
        foreach (ShopColor::get() as $color) {
            $swatch = $color->getSwatch();
            if ($swatch) {
                $map[$color->ID] = $swatch;
            }
        }

        return $map;
    }

    protected function onAfterInit(): void
    {
        $map = $this->getColorMap();
        if (!$map) {
            return;
        }

        $client = dirname(__DIR__) . '/client';
        Requirements::customScript('window.ShopColorMap=' . json_encode($map) . ';', 'shop-color-map');
        Requirements::customCSS((string) file_get_contents($client . '/colorswatches.css'), 'shop-color-swatches');
        Requirements::customScript((string) file_get_contents($client . '/colorswatches.js'), 'shop-color-swatches');
    }
}
