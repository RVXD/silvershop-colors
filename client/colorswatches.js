/**
 * CMS-only colour-swatch decorator (vanilla JS, no React build).
 *
 * Renders a small swatch next to each colour in:
 *   1. the product "Colours" listbox — a jQuery Chosen widget (selected pills + the dropdown results), and
 *   2. the variation "Shirt Colour" attribute column — a native <select> in the Variations grid (shows the
 *      currently selected colour, updated on change).
 *
 * Colours come from window.ShopColorMap ({ attributeValueId: '#hex' }), emitted by ColorCmsExtension. Keys are
 * AttributeValue ids, which are unique across the shared attribute-value table, so non-colour options (sizes etc.)
 * are simply absent from the map and never get a swatch.
 *
 * A MutationObserver re-runs the decorator as the CMS swaps panels in (pjax) and rebuilds grid rows, so it keeps
 * working across navigation without re-loading.
 */
(function () {
    'use strict';

    var SWATCH_CLASS = 'shop-color-swatch';

    function hexFor(id) {
        var map = window.ShopColorMap || {};
        return (id !== null && id !== undefined && map[id]) ? map[id] : null;
    }

    function makeSwatch(hex) {
        var span = document.createElement('span');
        span.className = SWATCH_CLASS;
        span.style.background = hex;
        span.setAttribute('aria-hidden', 'true');
        return span;
    }

    function hexForLabel(label, select) {
        label = (label || '').trim();
        for (var i = 0; i < select.options.length; i++) {
            if (select.options[i].textContent.trim() === label) {
                return hexFor(select.options[i].value);
            }
        }
        return null;
    }

    /* ---- Native grid select ("Shirt Colour" column) ---- */

    function isColourSelect(select) {
        for (var i = 0; i < select.options.length; i++) {
            if (select.options[i].value && hexFor(select.options[i].value)) {
                return true;
            }
        }
        return false;
    }

    function updateNativeSwatch(select, swatch) {
        var hex = hexFor(select.value);
        if (hex) {
            swatch.style.background = hex;
            swatch.style.visibility = 'visible';
        } else {
            swatch.style.visibility = 'hidden';
        }
    }

    function decorateNativeSelect(select) {
        if (!isColourSelect(select)) {
            return;
        }
        var parent = select.parentNode;
        if (parent && parent.classList && parent.classList.contains('shop-color-select-wrap')) {
            updateNativeSwatch(select, parent.querySelector('.' + SWATCH_CLASS));
            return;
        }
        var wrap = document.createElement('span');
        wrap.className = 'shop-color-select-wrap';
        var swatch = makeSwatch('transparent');
        parent.insertBefore(wrap, select);
        wrap.appendChild(swatch);
        wrap.appendChild(select);
        select.addEventListener('change', function () {
            updateNativeSwatch(select, swatch);
        });
        updateNativeSwatch(select, swatch);
    }

    /* ---- Chosen listbox (product "Colours") ---- */

    function chosenSelectFor(container) {
        var id = container.id || '';
        if (id.slice(-7) === '_chosen') {
            var byId = document.getElementById(id.slice(0, -7));
            if (byId && byId.tagName === 'SELECT') {
                return byId;
            }
        }
        var prev = container.previousElementSibling;
        return (prev && prev.tagName === 'SELECT') ? prev : null;
    }

    function hexByOptionIndex(index, select) {
        if (index !== null && index !== undefined && index !== '' && select.options[index]) {
            return hexFor(select.options[index].value);
        }
        return null;
    }

    function decorateChosen(container) {
        var select = chosenSelectFor(container);
        if (!select || !isColourSelect(select)) {
            return;
        }

        // Selected pills
        var pills = container.querySelectorAll('.chosen-choices li.search-choice');
        Array.prototype.forEach.call(pills, function (li) {
            if (li.querySelector('.' + SWATCH_CLASS)) {
                return;
            }
            var close = li.querySelector('a.search-choice-close');
            var hex = hexByOptionIndex(close && close.getAttribute('data-option-array-index'), select);
            var label = li.querySelector('span');
            if (!hex && label) {
                hex = hexForLabel(label.textContent, select);
            }
            if (hex && label) {
                li.insertBefore(makeSwatch(hex), label);
            }
        });

        // Dropdown results
        var results = container.querySelectorAll('.chosen-results li.active-result');
        Array.prototype.forEach.call(results, function (li) {
            if (li.querySelector('.' + SWATCH_CLASS)) {
                return;
            }
            var hex = hexByOptionIndex(li.getAttribute('data-option-array-index'), select);
            if (!hex) {
                hex = hexForLabel(li.textContent, select);
            }
            if (hex) {
                li.insertBefore(makeSwatch(hex), li.firstChild);
            }
        });
    }

    /* ---- Runner ---- */

    function decorateAll() {
        if (!window.ShopColorMap) {
            return;
        }
        // Variation "Shirt Colour" attribute dropdowns in the grid (native selects). Scoped to the attribute
        // selects so other CMS dropdowns (Category, Brand, …) are never touched.
        var attrSelects = document.querySelectorAll('select[name*="VariationAttributes"]');
        Array.prototype.forEach.call(attrSelects, decorateNativeSelect);
        // Product "Colours" listbox (jQuery Chosen). Scoped to the Colours field's own chosen container so other
        // multi-selects (Additional Categories, Attributes, …) are never touched.
        var colourChosen = document.querySelectorAll('[id$="_Colours_chosen"]');
        Array.prototype.forEach.call(colourChosen, decorateChosen);
    }

    var scheduled = false;
    function schedule() {
        if (scheduled) {
            return;
        }
        scheduled = true;
        window.requestAnimationFrame(function () {
            scheduled = false;
            decorateAll();
        });
    }

    function start() {
        decorateAll();
        if (window.__shopColorObserver) {
            return;
        }
        window.__shopColorObserver = new MutationObserver(schedule);
        window.__shopColorObserver.observe(document.body, { childList: true, subtree: true });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start);
    } else {
        start();
    }
})();
