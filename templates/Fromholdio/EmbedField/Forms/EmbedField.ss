<a class='embed-thumbnail <% if not $Thumbnail %>empty<% end_if %>' target='_blank'>
    <img src='$Thumbnail.URL' id='{$ID}_Thumbnail' title='$Thumbnail.Title' alt='' />
</a>

<div class="fieldholder-small">
    <div class="js-object-detail">
        $EmbedObject.DetailsForField
    </div>
    $SourceURLField
    <em id='{$ID}_message' class='embedfield-message'></em>
    <button
        type="button"
        value="Add url"
        class="action btn <% if $ThumbnailURL %>btn-outline-primary<% else %>btn-primary<% end_if %> cms-content-addpage-button tool-button <% if $ThumbnailURL %>font-icon-tick<% else %>font-icon-rocket<% end_if %>"
        data-icon="add"
        role="button"
        aria-disabled="false"
        disabled="false">
        <% if $ThumbnailURL %>
            <span>Update URL</span>
        <% else %>
            <span>Add URL</span>
        <% end_if %>
    </button>
</div>
<div class='clear'></div>
