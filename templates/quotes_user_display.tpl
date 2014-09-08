{checkpermission component='Quotes::' instance='::' level='ACCESS_EDIT' assign='authedit'}
<div class="quote_display">
{if $quote.error}
{$quote.error|safehtml}
{else}
{setmetatag name='description' value=$quote.quote|strip_tags|trim|truncate:500}
{pagesetvar name='title' value=$quote.author|strip_tags}
<q>{$quote.quote|safehtml}</q>
{if !empty($quote.author)}<span class="balloon_color_{$ballooncolor}">{$quote.author|safehtml}</span>{/if}
{if $authedit}<a href="{modurl modname='Quotes' type='admin' func='modify' qid=$quote.qid delcache=true}">Edit</a>{/if}
{/if}
</div>
{if $quote.qid gt 0}
{notifydisplayhooks eventname='quotes.ui_hooks.items.display_view' id=$quote.qid}
{/if}
