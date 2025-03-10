<a href="$URL" class="embed-thumbnail <% if not $ThumbnailURL %>empty<% end_if %>" target="_blank">
    <img src="$ThumbnailURL" alt="">
</a>
<div class="embed-object-detail">
    <strong>Type:</strong> $TypeLabel<% if $ProviderName %> - <% if $ProviderURL %><a href="$ProviderURL" target="_blank">$ProviderName</a><% else %>$ProviderName<% end_if %><% end_if %>
    <% if $Title %>
        <br><strong>Title:</strong> <% if $SourceURL %><a href="$SourceURL" target="_blank">$Title</a><% else %>$Title<% end_if %>
    <% else_if $SourceURL %>
        <br><strong>Source:</strong> <a href="$SourceURL" target="_blank">$SourceURL</a>
    <% end_if %>
    <% if $AspectRatioLabel %>
        <br><strong>Ratio:</strong> $AspectRatioLabel ({$Width}px x {$Height}px)
    <% end_if %>
</div>
