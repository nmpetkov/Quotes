{checkpermission component='Quotes::' instance='::' level='ACCESS_EDIT' assign='authedit'}
{if isset($quote.quote)}
<q>{$quote.quote|safehtml}</q>
{if $authedit}<a href="{modurl modname='Quotes' type='admin' func='modify' qid=$quote.qid delcache=true}">Edit</a>&nbsp;{/if}
{if isset($enablefacebookshare) && $enablefacebookshare}<a href="{modurl modname='Quotes' type='user' func='display' qid=$quote.qid}" title="{gt text='View and share'}"><img src="modules/Quotes/images/facebook_icon_small.png" alt="" style="display: inline;" /></a>{/if}
{if !empty($quote.author)}<span class="balloon_color_{$ballooncolor}">{$quote.author|safehtml}</span>{/if}
{/if}
