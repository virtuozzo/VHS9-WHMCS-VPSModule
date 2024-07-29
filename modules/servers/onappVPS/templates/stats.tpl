<link rel="stylesheet" type="text/css" href="{$dir}/css/style.css" />
<script type="text/javascript" src="{$dir}/js/highcharts.js"></script>
<div>
    <a href="clientarea.php?action=productdetails&id={$id}" class="btn btn-small"><i class="icon-arrow-left"></i> {$lang.back}</a>    
    <h2 class="set_main_header">{$lang.main_header}</h2>
    {if $msg_error or $msg_success}
        <div class="alert {if $msg_error}alert-danger{else}alert-success{/if}">
            <p></p><li>{if $msg_error}{$msg_error}{else}{$msg_success}{/if}</li><p></p>
        </div>
    {/if}
    {$chart|replace:'minWidth':'width'}
</div>
   