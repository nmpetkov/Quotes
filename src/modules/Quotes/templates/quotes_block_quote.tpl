{capture assign='templatestyles'}
<style type="text/css">
    .z-bid-{{$bid}} q {
        display: block;
    }
    .z-bid-{{$bid}} span {
        background: transparent url('{{$baseurl}}modules/Quotes/images/bg_quote_grey.gif') no-repeat scroll left 3px;
        display: block;
        padding: 15px 0 0 17px;
    }
</style>
{/capture}
{pageaddvar name='header' value=$templatestyles}

{if $quote.error}
{$quote.error|safehtml}
{else}
<q>{$quote.quote|safehtml}</q>
{if !empty($quote.author)}<span>{$quote.author|safehtml}</span>{/if}
{/if}
