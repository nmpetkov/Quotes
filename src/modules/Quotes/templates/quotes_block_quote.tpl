{if $quote.error}
{$quote.error|safehtml}
{else}
<q>{$quote.quote|safehtml}</q>
{if !empty($quote.author)}<span>{$quote.author|safehtml}</span>{/if}
{/if}
