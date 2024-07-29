<link rel="stylesheet" type="text/css" href="{$dir}/css/style.css" />
<div>
    {if $formAddIp}
        <a href="clientarea.php?action=productdetails&id={$id}&modop=custom&a=management&page=manageip" class="btn btn-small"><i class="icon-arrow-left"></i> {$lang.back}</a>
        <h2 class="header_label">{$lang.add_ip_header}</h2>
        {if $msg_error or $msg_success}
            <div class="alert {if $msg_error}alert-danger{else}alert-success{/if}">
                <p></p><li>{if $msg_error}{$msg_error}{else}{$msg_success}{/if}</li><p></p>
            </div>
        {/if}
        <form action="" method="post">
            <fieldset>
                <table class='table'>
                    <tr>
                        <th colspan="2">{$lang.identification}</th>
                    </tr>
                    <tr><td>{$lang.network_interface}</td>
                        <td>
                            <select name="ip[network_interface_id]">
                                {foreach from=$interfaces item="entry"}
                                    <option value="{$entry.network_interface.id}">{$entry.network_interface.label}</option>
                                {/foreach}
                            </select>
                        </td>
                    <tr><td>{$lang.ip_addresses}</td><td><input  name="ip[used_ip]"  type="number" required  min="1" max="100" />
                </table>
            </fieldset>
            <button type="submit" class="btn btn-success" name="doAction" value="add_ip_address">{$lang.submit}</button>
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
                    <th>{$lang.ip_address}</th>
                    <th>{$lang.netmask}</th>
                    <th>{$lang.gateway}</th>
                    <th>{$lang.physical_network}</th>
                    <th style="width:80px;">{$lang.action}</th>
                </tr>
            </thead>
            <tbody>    
              
                {foreach from=$ip_addresses item="entry"}
                    <tr>
                        <td>{$entry.interface}</td>
                        <td>{$entry.ip_address_join.ip_address.address}</td>
                        <td>{$entry.ip_address_join.ip_address.prefix}</td>
                        <td>{$entry.ip_address_join.ip_address.gateway}</td>
                        <td>{$entry.ip_address_join.ip_address.network_label}</td>
                        <td><a href="#" onclick="removeInterface({$entry.ip_address_join.id}, this);
                                        return false;" class="btn"><img src="{$dir}/img/delete.png" alt="{$lang.delete}" />
                        </a>
                        <img src="{$dir}/img/loadingsml.gif" class="mg-loading" style="display:none;" />
                        
                        </td> 
                    </tr>
                {foreachelse}
                    <tr>
                        <td colspan="6" class="td_center">{$lang.nothing_label}</td>
                    </tr>
                {/foreach}
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6">
                        {if $ip_addresses|@count>0}<input type="hidden" name="do" value="rebuildNetwork" /><input type="submit" value="{$lang.rebuild_network}" class="btn btn-success" />{/if}
                        {*if !$isFederation*} <button onclick="window.location = 'clientarea.php?action=productdetails&id={$id}&modop=custom&a=management&page=manageip&doAction=add_ip_address';
                                     return false;" class="btn btn-success">{$lang.add_ip}</button>
                         {*/if*}
                    </td>
                </tr>
            </tfoot>
          </table>
    </form>
    <script type="text/javascript">
    {literal}
        function removeInterface(id, el) {
            jQuery(el).closest('tr').find(".mg-loading").show();
            jQuery.post(window.location, {doAction: 'removeIP', ip_id: id}, function(data) {
                jQuery(el).closest('tr').find(".mg-loading").hide();
                if (data == 'success') {
                    jQuery(el).closest('tr').replaceWith("<tr id='successmsg'><td colspan='6' style='background:#dff0d8;color:#468847;text-align:center;'>{/literal}{$lang.ip_deleted}{literal}</td></tr>").delay(5000).slideUp(300);
                    setTimeout(function() {
                        jQuery("#successmsg").remove();
                    }, 5000);
                } else {
                    jQuery(el).closest('tr').after("<tr id='errorsmsg'><td colspan='6' style='background:#f2dede;color:#b94a48;text-align:center;'>" + data + "</td></tr>").delay(5000);
                }

            });
        }
    {/literal}
</script>        
    {/if}
</div>            
  