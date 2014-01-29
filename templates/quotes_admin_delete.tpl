{adminheader}
<div class="z-admin-content-pagetitle">
    {icon type="delete" size="small"}
    <h3>{gt text='Delete quote'}</h3>
</div>

<p class="z-warningmsg">{gt text='Do you really want to delete this Quote?'}</p>
<form class="z-form" action="{modurl modname='Quotes' type='admin' func='delete'}" method="post" enctype="application/x-www-form-urlencoded">
	<div>
		<input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
		<input type="hidden" name="confirmation" value="1" />
		<input type="hidden" name="qid" value="{$qid|safetext}" />
		<div class="z-formbuttons">
			{button src='button_ok.png' set='icons/small' __alt='Confirm deletion?' __title='Confirm deletion?'}
			<a href="{modurl modname='Quotes' type='admin' func='view'}">{img modname='core' src='button_cancel.png' set='icons/small' __alt='Cancel' __title='Cancel'}</a>
		</div>
	</div>
{notifydisplayhooks eventname='quotes.ui_hooks.items.form_delete' id=$qid}
</form>
{adminfooter}