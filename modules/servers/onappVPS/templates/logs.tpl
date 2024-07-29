<link rel="stylesheet" type="text/css" href="{$dir}/css/style.css" />
<script type="text/javascript" src="http://{$hostname}/assets/highcharts/highcharts.js"></script>
<div>
    <a href="clientarea.php?action=productdetails&id={$id}" class="btn btn-small"><i class="icon-arrow-left"></i> {$lang.back}</a>    
    <h2 class="set_main_header">{$lang.main_header}</h2>
    {if $msg_error or $msg_success}
        <div class="alert {if $msg_error}alert-danger{else}alert-success{/if}">
            <p></p><li>{if $msg_error}{$msg_error}{else}{$msg_success}{/if}</li><p></p>
        </div>
    {/if}
    <table class="table table-bordered">
        <thead>
            <tr><th>{$lang.date}</th><th>{$lang.action}</th><th>{$lang.status}</th></tr>
            {foreach from=$logs item="entry"}
                <tr>
                    <td>{$entry.transaction.created_at|date_format:"%Y-%m-%d %H:%M"}</td>
                    <td class="capitalize">{$entry.transaction.action|replace:'_':' '}</td>
                    <td class="{if $entry.transaction.status=='complete'}bg_green{elseif $entry.transaction.status='running'}bg_blue{/if} status">{$entry.transaction.status}</td>
                </tr>
            {/foreach}
        </thead>
    </table>
</div>
   