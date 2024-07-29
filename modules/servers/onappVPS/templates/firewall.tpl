<link rel="stylesheet" type="text/css" href="{$dir}/css/style.css" />
<div>
    <a href="clientarea.php?action=productdetails&id={$id}" class="btn btn-small"><i class="icon-arrow-left"></i> {$lang.back}</a>
    <h2 class="set_main_header">{$lang.main_header}</h2>
    {if $msg_error or $msg_success}
        <div class="alert {if $msg_error}alert-danger{else}alert-success{/if}">
            <p></p><li>{if $msg_error}{$msg_error}{else}{$msg_success}{/if}</li><p></p>
        </div>
    {/if}
    {if $block_form!=1}
    <form action="" method="post">
         <table class="table firewall_rules table-striped">
            <thead>
                <tr>
                    <th colspan="8">{$lang.list_firewall_rule}</th>
                </tr>
                <tr>
                    <th><input type="checkbox" id="selectAll" /></th>
                    <th></th>
                    <th>{$lang.interface}</th>
                    <th>{$lang.command}</th>
                    <th>{$lang.source_address}</th>
                    <th>{$lang.destination_port}</th>
                    <th>{$lang.protocol}</th>
                    <th>{$lang.actions}</th>
                </tr>
            </thead>
            <tbody>    
                {foreach from=$rules item="entry" name=foo}
                    <tr>
                        {if $entry.firewall_rule.id==$smarty.get.edit}
                            <td class="small"></td>
                            <td width="80"></td>
                            <td>
                                <select name="editrule[interface]" class='interface'>
                                    {foreach from=$interfaces item="e"}
                                        <option value="{$e.network_interface.id}" {if $entry.firewall_rule.interface_label==$e.network_interface.label}selected{/if}>{$e.network_interface.label}</option>
                                    {/foreach}
                                </select>
                            </td>
                            <td>
                                <select name="editrule[command]" class='command'>
                                    <option {if $entry.firewall_rule.command=="ACCEPT"}selected{/if}>ACCEPT</option>
                                    <option {if $entry.firewall_rule.command=="DROP"}selected{/if}>DROP</option>
                                </select>
                            </td>
                            <td><input name="editrule[address]" class='address' type="text" value="{$entry.firewall_rule.address}" /></td>
                            <td><input name="editrule[port]" class='port' type="text" value="{$entry.firewall_rule.port}" {if $entry.firewall_rule.protocol=="ICMP"}disabled{/if} /></td>
                            <td>
                                <select name="editrule[protocol]" class='protocol'>
                                    <option {if $entry.firewall_rule.protocol=="TCP"}selected{/if}>TCP</option>
                                    <option {if $entry.firewall_rule.protocol=="UDP"}selected{/if}>UDP</option>
                                    <option {if $entry.firewall_rule.protocol=="ICMP"}selected{/if}>ICMP</option>
                                </select>
                            </td>
                            <td><a href="#" onclick="saveRule({$entry.firewall_rule.id},this);return false;" class="btn"><img src="{$dir}/img/save.png" alt="{$lang.save}" /></a></td>
                        {else}
                        <td class="small"><input type="checkbox" name="rule_id[]" value="{$entry.firewall_rule.id}" class="selectAll" /></td>
                        <td><a href="clientarea.php?action=productdetails&id={$id}&modop=custom&a=management&page=firewall&rule={$entry.firewall_rule.id}&do=pos&pos=down" class="btn" {if $smarty.foreach.foo.last}disabled{/if} ><img src="{$dir}/img/arrow_down.png"/></a><a href="clientarea.php?action=productdetails&id={$id}&modop=custom&a=management&page=firewall&rule={$entry.firewall_rule.id}&do=pos&pos=up" class="btn" {if $smarty.foreach.foo.first}disabled{/if}><img src="{$dir}/img/arrow_up.png" /></a></td>
                        <td>{$entry.firewall_rule.interface_label}</td>
                        <td>{$entry.firewall_rule.command}</td>
                        <td>{$entry.firewall_rule.address}</td>
                        <td>{$entry.firewall_rule.port}</td>
                        <td>{$entry.firewall_rule.protocol}</td>
                        <td><a href="clientarea.php?action=productdetails&id={$id}&modop=custom&a=management&page=firewall&edit={$entry.firewall_rule.id}" class="btn"><img src="{$dir}/img/edit.png" alt="{$lang.edit}" /></a> <a href="#" onclick="removeRule({$entry.firewall_rule.id},this);return false;" class="btn"><img src="{$dir}/img/delete.png" alt="{$lang.delete}" /></a></td>
                        {/if}
                    </tr>
                {foreachelse}
                    <tr>
                        <td colspan="8" class="td_center">{$lang.nothing_label}</td>
                    </tr>
                {/foreach}
            </tbody>
            <tfoot>
                {if $rules}
                    <tr>
                        <td colspan="8"><input type="hidden" name="do" value="removeSelected" /><input onclick="return confirm('{$lang.confirm2}');" type="submit" value="{$lang.delete_selected}" class="btn btn-danger" /></td>
                    </tr>
                {/if}
            </tfoot>
      </table>      
    </form>
    
    <form action="" method="post">
        <input type="hidden" name="do" value="addRule" />
        <table class="table firewall_rules ">
            <thead>
                <tr>
                    <th colspan="5" class="title">{$lang.add_firewall_rule}</th>
                </tr>
                <tr>
                    <th>{$lang.interface}</th>
                    <th>{$lang.command}</th>
                    <th>{$lang.source_address}</th>
                    <th>{$lang.destination_port}</th>
                    <th>{$lang.protocol}</th>
                </tr>
            </thead>
            <tbody>     
                <tr>
                    <td>
                        <select name="rule[interface]">
                            {foreach from=$interfaces item="entry"}
                                <option value="{$entry.network_interface.id}">{$entry.network_interface.label}</option>
                            {foreachelse}
                                <option value="">---</option>
                            {/foreach}
                        </select>
                    </td>
                    <td>
                        <select name="rule[command]">
                            <option>ACCEPT</option>
                            <option>DROP</option>
                        </select>
                    </td>
                    <td>
                        <input name="rule[address]" type="text" value="" />
                    </td>
                    <td>
                        <input name="rule[port]" type="text" value="" />
                    </td>
                    <td>
                        <select name="rule[protocol]">
                            <option>TCP</option>
                            <option>UDP</option>
                            <option>ICMP</option>
                        </select>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5"><input type="submit" value="{$lang.add_rule}" class="btn btn-success" /></td>
                </tr>
            </tfoot>
        </table>
    </form>   
                
                
    <form action="" method="post">
        <input type="hidden" name="do" value="defaultRule" />
        <table class="table firewall_rules">
            <thead>
                <tr>
                    <th colspan="5" class="title">{$lang.default_firewall_rule}</th>
                </tr>
                <tr>
                    <th>{$lang.interface}</th>
                    <th>{$lang.command}</th>
                </tr>
            </thead>
            <tbody> 
                {foreach from=$interfaces item="entry"}
                <tr>
                    <td>
                      {$entry.network_interface.label}
                    </td>
                    <td>
                        <select name="defaultRule[{$entry.network_interface.id}][command]">
                            <option {if $entry.network_interface.default_firewall_rule=='ACCEPT'}selected{/if}>ACCEPT</option>
                            <option {if $entry.network_interface.default_firewall_rule=='DROP'}selected{/if}>DROP</option>
                        </select>
                    </td>
                </tr>
                {/foreach}
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5"><input type="submit" value="{$lang.save_default_rule}" class="btn btn-success" /> <button name="do" value="apply" class="btn btn-info" style="float:right">{$lang.apply}</button></td>
                </tr>
            </tfoot>
        </table>
    </form>                   
</div>

<script type="text/javascript">
{literal}
    var save = true;
    function removeRule(id,el){
        if(!confirm("{/literal}{$lang.confirm}{literal}"))
            return;
        jQuery.post(window.location,{do:'removeRule',rule:id},function(data){
            if(data=='success'){
                jQuery(el).closest('tr').replaceWith("<tr id='successmsg'><td colspan='8' style='background:#dff0d8;color:#468847;text-align:center;'>{/literal}{$lang.rule_deleted}{literal}</td></tr>").delay(5000).slideUp(300);
                setTimeout(function(){
                   jQuery("#successmsg").remove(); 
                },5000);
            } else {
                jQuery(el).closest('tr').after("<tr id='errorsmsg'><td colspan='8' style='background:#f2dede;color:#b94a48;text-align:center;'>"+data+"</td></tr>").delay(5000);
            }
                
        });
    }
    
    function saveRule(id,el){
        if(save === true){
            save          = false;
            var tr        = jQuery(el).closest('tr');
            var data      = new Array;
            data['interface'] = jQuery(tr).find('.interface').val();
            data['command']   = jQuery(tr).find('.command').val();
            data['address']   = jQuery(tr).find('.address').val();
            data['port']      = jQuery(tr).find('.port').val();
            data['protocol']  = jQuery(tr).find('.protocol').val();

            jQuery.post('clientarea.php?action=productdetails&id={/literal}{$id}{literal}&modop=custom&a=management&page=firewall',{do:'saveRule',rule:id,interface:data['interface'],command:data['command'],address:data['address'],port:data['port'],protocol:data['protocol']},function(response){
                        if(response=='success'){
                            window.location.href='clientarea.php?action=productdetails&id={/literal}{$id}{literal}&modop=custom&a=management&page=firewall';
                        } else {
                            if(jQuery(el).closest('tr').next().hasClass('errorsmsg'))
                                jQuery(el).closest('tr').next().replaceWith("<tr class='errorsmsg'><td colspan='8' style='background:#f2dede;color:#b94a48;text-align:center;'>"+response+"</td></tr>").delay(5000);
                            else
                                jQuery(el).closest('tr').after("<tr class='errorsmsg'><td colspan='8' style='background:#f2dede;color:#b94a48;text-align:center;'>"+response+"</td></tr>").delay(5000);
                        }
            save = true;            
            });
        }
        return false;
    }
    
   jQuery(document).ready(function(){
        jQuery("select[name='editrule[protocol]']").change(function(){
            if(jQuery(this).val()=='ICMP'){
                jQuery("input[name='editrule[port]']").attr('disabled','disabled');
                jQuery("input[name='editrule[port]']").attr('value','');
            }else
                jQuery("input[name='editrule[port]']").attr('disabled',false);
        });  
        
        jQuery("select[name='rule[protocol]']").change(function(){
            if(jQuery(this).val()=='ICMP'){
                jQuery("input[name='rule[port]']").attr('disabled','disabled');
                jQuery("input[name='rule[port]']").attr('value','');
            }else
                jQuery("input[name='rule[port]']").attr('disabled',false);
        });  
        
        jQuery("#selectAll").change(function(){
               jQuery(".selectAll").prop("checked",this.checked);
        });        
        
        
   });
{/literal}
</script>
{/if}