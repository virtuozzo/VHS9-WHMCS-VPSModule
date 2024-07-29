<link rel="stylesheet" type="text/css" href="{$dir}/css/style.css" />
<script type="text/javascript" src="{$dir}/js/quicksearch.js"></script>
<div>
    <a href="clientarea.php?action=productdetails&id={$id}&modop=custom&a=management" class="btn btn-small"><i class="icon-arrow-left"></i> {$lang.back}</a>
    <div>
    <input type="text" id="search" style="float:right;" class="input-medium search-query" placeholder="{$lang.search}"><h2 class="header_label">{$lang.main_header}</h2> 
    </div>
    {if $msg_error or $msg_success}
        <div class="alert {if $msg_error}alert-danger{else}alert-success{/if}">
            <p></p><li>{if $msg_error}{$msg_error}{else}{$msg_success}{/if}</li><p></p>
        </div>
    {/if}
    <div class="attention-alert alert alert-danger">
        <span class="icon-warning-sign"></span>
        <span>{$lang.attention}</span>
    </div>
    <form method="post" action="upgrade.php" id="onapp_add_form">
        <input type="hidden" name="type" value="configoptions" />
        <input type="hidden" name="vm_action" value="rebuild" />
        <input type="hidden" name="step" value="2" />
        <input type="hidden" name="id" value="{$id}" />
        {foreach from=$confdata item="entry" key="k"}
        <input type="hidden" name="configoption[{$k}]" value="{if $entry.qty > 0}{$entry.qty}{else}{$entry.option}{/if}" />
        {/foreach}
        <table class="table table-bordered table-striped table-template">
            <thead>
                <tr><th>{$lang.template}</th><th>{$lang.price}</th><th>{$lang.select}</th></tr>
            </thead>
            <tbody>
                {foreach from=$templates item="entry" key="k"}
                    <tr class="group"><th colspan="3">{$k}</th></tr>
                            {foreach from=$entry item="it" key="key"}
                        <tr class="{$k}_hide search-item "><td>{$it.label}</td><td>{$it.price}</td><td><input type="radio" name="configoption[{$confid}]" value="{$key}" /></tr>
                            {foreachelse}
                        <tr><td colspan="3">{$lang.template_unavailable}</td></tr>
                        {/foreach}
                    {foreachelse}
                    <tr><td colspan="3">{$lang.template_unavailable}</td></tr>
                    {/foreach}  
            </tbody>
        </table> 
        {if $templates|@count >0}
            <fieldset>
                <label>{$lang.startVsAfterRebuild} <input type="checkbox" value="1" name="required_startup">
                </label>
                <br/> <br/>
            </fieldset>
            <div class='rebuild-btn'><input type='submit' onclick="return confirm('{$lang.confirm_rebuild}');" class='btn rebuild-btn btn-success' value='{$lang.rebuild}' /></div>
        {/if}
    </form>    
</div>
<script type="text/javascript">
    {literal}
        jQuery(document).ready(function() {
            jQuery('input#search').quicksearch('table.table-template tbody tr.search-item');
        });
    {/literal} 
</script>