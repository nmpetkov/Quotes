{*  $Id: quotes_block_quote_modify.tpl 2010-07-19 nikp $  *}
<div class="z-formrow">
    <label for="block_hourfrom">{gt text="From hour" domain='module_quotes'}</label>
    <select id="block_hourfrom" name="hourfrom" style="width:70px">
        <option value="-1"{if $hourfrom eq -1} selected="selected"{/if}>{gt text="Not set" domain='module_quotes'}</option>
		{section name='hour' loop=$hours}
        <option value="{$hours[hour]}"{if $hourfrom eq $hours[hour]} selected="selected"{/if}>{$hours[hour]}</option>
		{/section}
    </select>
    <label for="block_hourto">{gt text="To hour" domain='module_quotes'}</label>
    <select id="block_hourto" name="hourto" style="width:70px">
        <option value="-1"{if $hourto eq -1} selected="selected"{/if}>{gt text="Not set" domain='module_quotes'}</option>
		{section name='hour' loop=$hours}
        <option value="{$hours[hour]}"{if $hourto eq $hours[hour]} selected="selected"{/if}>{$hours[hour]}</option>
		{/section}
    </select>
</div>
<div class="z-formrow">
    <label for="block_wdayfrom">{gt text="From day of the week" domain='module_quotes'}</label>
    <select id="block_wdayfrom" name="wdayfrom" style="width:70px">
        <option value="-1"{if $wdayfrom eq -1} selected="selected"{/if}>{gt text="Not set" domain='module_quotes'}</option>
		{section name='wday' loop=$wdays}
        <option value="{$wdays[wday]}"{if $wdayfrom eq $wdays[wday]} selected="selected"{/if}>{$wdays[wday]}</option>
		{/section}
    </select>
    <label for="block_wdayto">{gt text="To day of the week" domain='module_quotes'}</label>
    <select id="block_wdayto" name="wdayto" style="width:70px">
        <option value="-1"{if $wdayto eq -1} selected="selected"{/if}>{gt text="Not set" domain='module_quotes'}</option>
		{section name='wday' loop=$wdays}
        <option value="{$wdays[wday]}"{if $wdayto eq $wdays[wday]} selected="selected"{/if}>{$wdays[wday]}</option>
		{/section}
    </select>
</div>
<div class="z-formrow">
    <label for="block_mdayfrom">{gt text="From day of the month" domain='module_quotes'}</label>
    <select id="block_mdayfrom" name="mdayfrom" style="width:70px">
        <option value="-1"{if $mdayfrom eq -1} selected="selected"{/if}>{gt text="Not set" domain='module_quotes'}</option>
		{section name='mday' loop=$mdays}
        <option value="{$mdays[mday]}"{if $mdayfrom eq $mdays[mday]} selected="selected"{/if}>{$mdays[mday]}</option>
		{/section}
    </select>
    <label for="block_mdayto">{gt text="To day of the month" domain='module_quotes'}</label>
    <select id="block_mdayto" name="mdayto" style="width:70px">
        <option value="-1"{if $mdayto eq -1} selected="selected"{/if}>{gt text="Not set" domain='module_quotes'}</option>
		{section name='mday' loop=$mdays}
        <option value="{$mdays[mday]}"{if $mdayto eq $mdays[mday]} selected="selected"{/if}>{$mdays[mday]}</option>
		{/section}
    </select>
</div>
<div class="z-formrow">
    <label for="block_monfrom">{gt text="From month" domain='module_quotes'}</label>
    <select id="block_monfrom" name="monfrom" style="width:70px">
        <option value="-1"{if $monfrom eq -1} selected="selected"{/if}>{gt text="Not set" domain='module_quotes'}</option>
		{section name='month' loop=$months}
        <option value="{$months[month]}"{if $monfrom eq $months[month]} selected="selected"{/if}>{$months[month]}</option>
		{/section}
    </select>
    <label for="block_monto">{gt text="To month" domain='module_quotes'}</label>
    <select id="block_monto" name="monto" style="width:70px">
        <option value="-1"{if $monto eq -1} selected="selected"{/if}>{gt text="Not set" domain='module_quotes'}</option>
		{section name='month' loop=$months}
        <option value="{$months[month]}"{if $monto eq $months[month]} selected="selected"{/if}>{$months[month]}</option>
		{/section}
    </select>
</div>
{if $enablecategorization}
<div class="z-formrow">
    <label>{gt text='Choose categories' domain='module_quotes'}</label>
    {nocache}
    {foreach from=$catregistry key='prop' item='cat'}
    {array_field_isset assign='selectedValue' array=$category field=$prop returnValue=1}
    <div class="z-formnote">
        {selector_category category=$cat name="category[$prop]" multipleSize=5 selectedValue=$selectedValue}
    </div>
    {/foreach}
    {/nocache}
</div>
{/if}
<div class="z-formrow">
    <label for="blocks_cache_time">{gt text="Cache time (enter positive number in seconds to enable cache)" domain='module_quotes'}</label>
    <input id="blocks_cache_time" type="text" name="cache_time" size="10" maxlength="50" value="{$cache_time|pnvarprepfordisplay}" />
</div>
<div class="z-formrow">
    <label for="blocks_cache_dir">{gt text="Cache directory name (within Zikula Temp directory)" domain='module_quotes'}</label>
    <input id="blocks_cache_dir" type="text" name="cache_dir" size="30" maxlength="255" value="{$cache_dir|pnvarprepfordisplay}" />
</div>
