<a href="clientarea.php?action=productdetails&id={$id}&modop=custom&a=management&page=disk" class="btn btn-small"><i class="icon-arrow-left"></i> {$lang.back}</a>
<h2 class="set_main_header">{$lang.header_add_label}</h2>
{if $msg_error or $msg_success}
    <div class="alert {if $msg_error}alert-danger{else}alert-success{/if}">
        <p></p><li>{if $msg_error}{$msg_error}{else}{$msg_success}{/if}</li><p></p>
    </div>
{/if}
<form action="clientarea.php?action=productdetails&id={$id}&modop=custom&a=management&page=disk" method="post">
     <table class="table table-striped">
         <tr>
             <td>{$lang.label}</td>
             <td><input type="text" name="add[label]" /></td>
         </tr>
         <tr>
             <td>{$lang.data_store}</td>
             <td>
                    <select name="add[data_store]" {if $stores|@count==0}disabled{/if}>
                    {foreach from=$stores item="value" key="k"}
                        <option value="{$k}">{$value}</option>
                    {/foreach}
                    </select>
             </td>
         </tr>
         <tr>
             <td>{$lang.size}</td>
             <td><input type="text" name="add[size]" requried=""/> {$lang.GB}</td>
         </tr>
         <tr>
             <td>{$lang.swap_space}</td>
             <td><input type="checkbox" name="add[is_swap]" value="1" /></td>
         </tr>
         <tr>
             <td>{$lang.require_format}</td>
             <td><input type="checkbox" name="add[require_format_disk]" value="1" checked="" /></td>
         </tr>
         <tr>
             <td>{$lang.add_to_linux}</td>
             <td><input type="checkbox" name="add[add_to_linux_fstab]" value="1" disabled /></td>
         </tr>
         <tr>
             <td>{$lang.mount_point}</td>
             <td><input type="text" name="add[mount_point]" disabled /></td>
         </tr>
         <tr>
             <td>{$lang.file_system}</td>
             <td> <select  name="add[file_system]"><option value="ext3">ext3</option><option value="ext4">ext4</option></select></td>
         </tr>
     </table>
     <input type="hidden" name="do" value="saveDisk" />
     <input type="submit" class="btn btn-success" value="{$lang.add_disk}" />        
</form>        
<script type="text/javascript">
{literal}
    jQuery(document).ready(function(){
         jQuery("input[name='add[is_swap]']").change(function(){
             if(jQuery(this).is(":checked")){
                 jQuery("input[name='add[require_format_disk]'],input[name='add[add_to_linux_fstab]'],input[name='add[mount_point]']").attr('disabled',true);
             } else
                 jQuery("input[name='add[require_format_disk]'],input[name='add[add_to_linux_fstab]'],input[name='add[mount_point]']").attr('disabled',false);
         });  
         jQuery("input[name='add[is_swap]']").trigger('change');
         jQuery("input[name='add[require_format_disk]']").change(function(){
             if(jQuery(this).is(":checked")){
                 jQuery("input[name='add[is_swap]']").attr('disabled',true);
                 jQuery("input[name='add[add_to_linux_fstab]'],input[name='add[mount_point]']").attr('disabled',false);
             } else{
                 jQuery("input[name='add[add_to_linux_fstab]'],input[name='add[mount_point]']").attr('disabled',true);
                 jQuery("input[name='add[is_swap]']").attr('disabled',false);
             }
             
         })
    });
{/literal}
</script>