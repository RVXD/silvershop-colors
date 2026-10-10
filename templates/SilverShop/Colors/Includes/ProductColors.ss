<% if $Colours.Count %>
    <ul class="product-colors">
        <% loop $Colours %>
            <li class="product-colors__item">
                <% if $Swatch %><span class="product-colors__swatch" style="background:$Swatch.ATT" title="$Value.ATT"></span><% end_if %>
                <span class="product-colors__label">$Value</span>
            </li>
        <% end_loop %>
    </ul>
<% end_if %>
