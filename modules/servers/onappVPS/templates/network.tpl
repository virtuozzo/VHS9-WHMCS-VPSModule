<link rel="stylesheet" type="text/css" href="{$dir}/css/style.css" />
<script type="text/javascript" src="{$dir}/js/highcharts.js"></script>
<div>
{if $graph==1}
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
    {elseif $form==1}
        <a href="clientarea.php?action=productdetails&id={$id}&modop=custom&a=management&page=network" class="btn btn-small"><i class="icon-arrow-left"></i> {$lang.back}</a>
         <h2 class="set_main_header">{if $edit==1}{$lang.main_header_edit}{else}{$lang.main_header_add}{/if}</h2>
        {if $msg_error or $msg_success}
            <div class="alert {if $msg_error}alert-danger{else}alert-success{/if}">
                <p></p><li>{if $msg_error}{$msg_error}{else}{$msg_success}{/if}</li><p></p>
            </div>
        {/if}
        <form action="" method="post">
            <fieldset>
               
                <table class='table'>
                    <tr><th colspan="2">{$lang.identification}</th></tr>
                    <tr><td>{$lang.label}</td><td><input  name="network_interface[label]"  type="text" {if $edit==1}value="{$interface.label}"{/if}/>
                    <tr><th colspan="2">{$lang.connectivity}</th></tr>
                    <tr><td>{$lang.physical_network}</td><td>{if $edit==1}{$interface.network_label}{else}<select name="network_interface[network]">{foreach from=$networks_select item="entry"}<option value="{$entry.network.network_join_id}">{$entry.network.label}</option>{/foreach}</select>{/if}</td></tr>
                    <tr><td>{$lang.port_speed}<br /><span style="font-size:11px;">{$lang.leave_unlimited}</span></td><td><input type="text" name="network_interface[rate_limit]" {if $billing_resources.ip_addresses.type=='disabled'}readonly{/if} value="{$interface.rate_limit}" /> {$lang.mbps}</td></tr>
                    <tr><td>{$lang.primary_interface}</td><td><input type="checkbox" name="network_interface[primary]" value="1" {if $interface.primary==true}checked{/if} /></td></tr>
                </table>
            </fieldset>
            <input type="submit" class="btn btn-success" value="{if $edit==1}{$lang.save}{else}{$lang.submit}{/if}" />
        </form>
    {else}    
        <a href="clientarea.php?action=productdetails&id={$id}" class="btn btn-small"><i class="icon-arrow-left"></i> {$lang.back}</a>
        <h2 class="set_main_header">{$lang.main_header}</h2>
        {if $msg_error or $msg_success}
            <div class="alert {if $msg_error}alert-danger{else}alert-success{/if}">
                <p></p><li>{if $msg_error}{$msg_error}{else}{$msg_success}{/if}</li><p></p>
            </div>
        {/if}
        <form action="" method="post">
            <table class="table ip_address table-striped">
                <thead>
                    <tr>
                        <th>{$lang.interface}</th>
                        <th>{$lang.network_join}</th>
                        <th>{$lang.port_speed}</th>
                        <th>{$lang.primary_interface}</th>
                        <th>{$lang.actions}</th>
                    </tr>
                </thead>
                <tbody>    
                    {foreach from=$networks item="entry"}
                        <tr>
                            <td>{$entry.network_interface.label}</td>
                            <td>{$entry.network_interface.network_label}</td>
                            <td>{if $entry.network_interface.rate_limit==0}{$lang.unlimited}{else}{$entry.network_interface.rate_limit} {$lang.mbps}{/if}</td>
                            <td>{if $entry.network_interface.primary==1}{$lang.yes}{else}{$lang.no}{/if}</td>
                            <td>
                                <a href="clientarea.php?action=productdetails&id={$id}&modop=custom&a=management&page=network&doAction=showgraph&network_id={$entry.network_interface.id}" class="btn"><img src="{$dir}/img/graphs.png" alt="{$lang.usage}" /></a>
                                {if !$isFederation}
                                <a href="clientarea.php?action=productdetails&id={$id}&modop=custom&a=management&page=network&doAction=edit&interface={$entry.network_interface.id}" class="btn"><img src="{$dir}/img/edit.png" alt="{$lang.edit}" /></a> 
                                <a href="#" onclick="removeInterface({$entry.network_interface.id}, this);
                                        return false;" class="btn"><img src="{$dir}/img/delete.png" alt="{$lang.delete}" /></a>
                                {/if}
                            </td>
                        </tr>
                    {foreachelse}
                        <tr>
                            <td colspan="5" class="td_center">{$lang.nothing_label}</td>
                        </tr>
                    {/foreach}
                </tbody>
                {if $billing_resources.ip_addresses.type!='disabled' && !$isFederation}
                <tfoot>
                    <tr>
                        <td colspan="5"><button onclick="window.location = 'clientarea.php?action=productdetails&id={$id}&modop=custom&a=management&page=network&doAction=add_interface';
                                        return false;" class="btn btn-success">{$lang.add_interface}</button></td>
                    </tr>
                </tfoot>
                {/if}
            </table>
        </form>
    {/if}
</div>            
<script type="text/javascript">
    {literal}
        function removeInterface(id, el) {
            if(!confirm("{/literal}{$lang.confirm}{literal}"))
                return;
            jQuery.post(window.location, {doAction: 'removeInterface', interface: id}, function(data) {
                if (data == 'success') {
                    jQuery(el).closest('tr').replaceWith("<tr id='successmsg'><td colspan='5' style='background:#dff0d8;color:#468847;text-align:center;'>{/literal}{$lang.interface_deleted}{literal}</td></tr>").delay(5000).slideUp(300);
                    setTimeout(function() {
                        jQuery("#successmsg").remove();
                    }, 5000);
                } else {
                    jQuery(el).closest('tr').after("<tr id='errorsmsg'><td colspan='5' style='background:#f2dede;color:#b94a48;text-align:center;'>" + data + "</td></tr>").delay(5000);
                }

            });
        }
    {/literal}
</script>