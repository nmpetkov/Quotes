{adminheader}
<div class="z-admin-content-pagetitle">
    {icon type="new" size="small"}
    <h3>{gt text='Create new quote'}</h3>
</div>

<form class="z-form" action="{modurl modname='Quotes' type='admin' func='create'}" method="post" enctype="application/x-www-form-urlencoded">
    <input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
    <fieldset class="z-linear">
        <div class="z-formrow">
            <label for="quote_quote">{gt text='Quote Text'}</label>
            <textarea id="quote_quote" rows="8" name="quote[quote]" cols="50"></textarea>
        </div>
    </fieldset>
    <fieldset>
        <div class="z-formrow">
            <label for="quotes_author">{gt text='Author'}</label>
            <input id="quotes_author" type="text" name="quote[author]" size="30" maxlength="128" />
        </div>
        {if $enablecategorization}
        <div class="z-formrow">
            <label>{gt text='Categories'}</label>
            {gt text='Choose a category' assign='lblDef'}
            {nocache}
            {foreach from=$catregistry key='property' item='category'}
            <div class="z-formnote">{selector_category category=$category name="quote[__CATEGORIES__][$property]" field='id' selectedValue='0' defaultValue='0' defaultText=$lblDef}</div>
            {/foreach}
            {/nocache}
        </div>
        {/if}
        <div class="z-formrow">
            <label for="quote_status">{gt text='Status'}</label>
            <select name="quote[status]" id="quote_status">
                <option label="{gt text="Active"}" value="1"{if $status eq 1} selected="selected"{/if}>{gt text="Active"}</option>
                <option label="{gt text="Inactive"}" value="0"{if $status eq 0} selected="selected"{/if}>{gt text="Inactive"}</option>
            </select>
        </div>
    </fieldset>

    {notifydisplayhooks eventname='quotes.ui_hooks.items.form_edit' id=null}

    <div class="z-buttonrow z-buttons z-center">
        {button src='button_ok.png' set='icons/small' __alt='Create' __title='Create'}
        <a href="{modurl modname='Quotes' type='admin' func='view'}">{img modname='core' src='button_cancel.png' set='icons/small' __alt='Cancel' __title='Cancel'}</a>
    </div>
</form>
{adminfooter}