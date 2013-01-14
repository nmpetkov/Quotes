{if $quote.error}
{$quote.error|safehtml}
{else}
<q>{$quote.quote|safehtml}</q>
{if !empty($quote.author)}<span class="balloon_color_{$ballooncolor}">{$quote.author|safehtml}</span>{/if}
{/if}
{if $quote.qid gt 0}
{notifydisplayhooks eventname='quotes.ui_hooks.items.display_view' id=$quote.qid}
{/if}
