{*  $Id: quotes_admin_modifyconfig.tpl 358 2009-11-11 13:46:21Z herr.vorragend $  *}
{gt text='Settings' assign='templatetitle'}

{include file='quotes_admin_menu.tpl'}

<div class="z-admincontainer">
    <div class="z-adminpageicon">{img modname='core' src='configure.gif' set='icons/large' alt=$templatetitle}</div>

    <h2>{$templatetitle}</h2>

    <form class="z-form" action="{modurl modname='Quotes' type='admin' func='updateconfig'}" method="post" enctype="application/x-www-form-urlencoded">
        <div>
            <input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
            <fieldset>
                <div class="z-formrow">
                    <label for="quotes_enablecategorization">{gt text='Enable categorization'}</label>
                    <input id="quotes_enablecategorization" type="checkbox" name="enablecategorization"{if $enablecategorization} checked="checked"{/if} />
                </div>
                <div class="z-formrow">
                    <label for="quotes_itemsperpage">{gt text='Items per page'}</label>
                    <input id="quotes_itemsperpage" type="text" name="itemsperpage" size="3" value="{$itemsperpage|pnvarprepfordisplay}" />
                </div>
            </fieldset>
            <div class="z-formbuttons">
                {button src='button_ok.gif' set='icons/small' __alt='Save' __title='Save'}
                <a href="{modurl modname='Quotes' type='admin' func='view'}">{img modname='core' src='button_cancel.gif' set='icons/small' __alt='Cancel' __title='Cancel'}</a>
            </div>
        </div>
    </form>
</div>
