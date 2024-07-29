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
         <table class="table publications table-striped">
            <thead>
                <tr>
                    <th colspan="7">{$lang.list_publication}</th>
                </tr>
                <tr>
                    <th><input type="checkbox" id="selectAll" /></th>
                    <th>{$lang.created_at}</th>
                    <th>{$lang.is_built}</th>
                    <th>{$lang.port}</th>
                    <th>{$lang.protocol}</th>
                    <th>{$lang.updated_at}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>    
                {foreach from=$rules item="entry"}
                    <tr>
                        <td class="small"><input type="checkbox" name="rule_id[]" value="{$entry.publication.id}" class="selectAll" /></td>
                        <td>{$entry.publication.created_at}</td>
                        <td>{$entry.publication.is_built}</td>
                        <td>{$entry.publication.port}</td>
                        <td>{$entry.publication.protocol}</td>
                        <td>{$entry.publication.updated_at}</td>
                        <td><a href="clientarea.php?action=productdetails&id={$id}&modop=custom&a=management&page=firewall&edit={$entry.publication.id}" class="btn"><img src="{$dir}/img/edit.png" alt="{$lang.edit}" /></a> <a href="#" onclick="removeRule({$entry.publication.id},this);return false;" class="btn"><img src="{$dir}/img/delete.png" alt="{$lang.delete}" /></a></td>
                    </tr>
                {foreachelse}
                    <tr>
                        <td colspan="7" class="td_center">{$lang.nothing_label}</td>
                    </tr>
                {/foreach}
            </tbody>
            <tfoot>
                {if $rules}
                    <tr>
                        <td colspan="7"><input type="hidden" name="do" value="removeSelected" /><input type="submit" value="{$lang.delete_selected}" class="btn btn-danger" /></td>
                    </tr>
                {/if}
            </tfoot>
      </table>      
    </form>
    
    <form action="" method="post">
        <input type="hidden" name="do" value="addRule" />
        <table class="table publications ">
            <thead>
                <tr>
                    <th colspan="3" class="title">{$lang.add_publication}</th>
                </tr>
                <tr>
                    <th>{$lang.port}</th>
                    <th>{$lang.protocol}</th>
                    <th>{$lang.network}</th>
                </tr>
            </thead>
            <tbody>    
                <tr>
                    <td><input type="text" name="rule[port]" /></td>
                    <td><select name="rule[protocol]">
                            <option>TCP</option>
                            <option>UDP</option>
                            <option>ICMP</option>
                        </select></td>
                    <td>
                        <select name="rule[network]">
                            {foreach from=$interfaces item="entry"}
                                <option value="{$entry.network_interface.id}">{$entry.network_interface.label}</option>
                            {foreachelse}
                                <option value="">---</option>
                            {/foreach}
                        </select>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3"><input type="submit" value="{$lang.add_rule}" class="btn btn-success" /></td>
                </tr>
            </tfoot>
        </table>
    </form>                  
</div>

<script type="text/javascript">
{literal}
    var save = true;
    function removeRule(id,el){
        jQuery.post(window.location,{do:'removeRule',rule:id},function(data){
            if(data=='success'){
                jQuery(el).closest('tr').replaceWith("<tr id='successmsg'><td colspan='7' style='background:#dff0d8;color:#468847;text-align:center;'>{/literal}{$lang.rule_deleted}{literal}</td></tr>").delay(5000).slideUp(300);
                setTimeout(function(){
                   jQuery("#successmsg").remove(); 
                },5000);
            } else {
                jQuery(el).closest('tr').after("<tr id='errorsmsg'><td colspan='7' style='background:#f2dede;color:#b94a48;text-align:center;'>"+data+"</td></tr>").delay(5000);
            }
                
        });
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
               if (this.checked) 
                   jQuery(".selectAll").attr("checked",true);
               else
                   jQuery(".selectAll").attr("checked",false);
        });        
   });
{/literal}
</script>
{/if}