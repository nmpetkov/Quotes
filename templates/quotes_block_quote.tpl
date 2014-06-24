{checkpermission component='Quotes::' instance='::' level='ACCESS_EDIT' assign='authedit'}
<q>{$quote.quote|safehtml}</q>
{if $authedit}<a href="{modurl modname='Quotes' type='admin' func='modify' qid=$quote.qid delcache=true}">Edit</a>{/if}
{if !empty($quote.author)}<span class="balloon_color_{$ballooncolor}">{$quote.author|safehtml}</span>{/if}
