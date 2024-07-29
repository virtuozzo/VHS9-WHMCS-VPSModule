<link rel="stylesheet" type="text/css" href="{$dir}/css/style.css" />
<div>
    <a href="clientarea.php?action=productdetails&id={$id}" class="btn btn-small"><i class="icon-arrow-left"></i> {$lang.back}</a>
    <h2 class="set_main_header">{$lang.main_header}</h2>
    {if $msg_error or $msg_success}
        <div class="alert {if $msg_error}alert-danger{else}alert-success{/if}">
            <p></p><li>{if $msg_error}{$msg_error}{else}{$msg_success}{/if}</li><p></p>
        </div>
    {/if}
    {if $allow_incremental_backups}
        <form action="" method="post" class="action-form pull-right">
            <input type="hidden" name="do" value="createBackupIncremental" />
            <input type="hidden" name="vm_id" value="{$params.customfields.vmid}" />
            <button class="btn btn-success" style="float: right;margin-top: 10px;">{$lang.add_backup}</button>
        </form>
    {/if}
    <a href="clientarea.php?action=productdetails&id={$id}&modop=custom&a=management&page=schedule" class="btn btn-primary pull-right" style="margin-top: 10px; margin-right: 65px;">{$lang.schedules}</a>
    
    
         <table class="table table-striped backups-list">
            <thead>
                <tr>
                    <th>{$lang.datetime}</th>
                    <th>{$lang.disk}</th>
                    <th>{$lang.status}</th>
                    <th>{$lang.backup_size}</th>
                    <th>{$lang.backup_type}</th>
                    <th>{$lang.backup_server}</th>
                    <th>{$lang.backup_note}</th>
                    <th>{$lang.actions}</th>
                </tr>
            </thead>
            <tbody>    
                {foreach from=$backups item="entry"}
                    <tr>
                        <td>{$entry.backup.created_at|date_format:"%d-%m-%Y %H:%M:%S"}</td>
                        <td>#{$entry.backup.disk_id}</td>
                        <td>{if $entry.backup.built==1}{$lang.built}{else}{$lang.running}{/if}</td>
                        <td>{if $entry.backup.backup_size>0}{$entry.backup.backup_size/1024|string_format:"%.2f"} {$lang.MB}{else}{$lang.not_built}{/if}</td>
                        <td>{if $entry.backup.backup_type|strpos:"normal" !== false}{$lang.backup_normal}{else}{$lang.backup_incremental}{/if}</td>
                        <td>{$entry.backup.server_label}</td>
                        <td>{$entry.backup.note}</td>
                        <td>
                            <form action="" method="post" class="action-form">
                                <input type="hidden" name="do" value="restoreBackup" />
                                <input type="hidden" name="backup_id" value="{$entry.backup.id}" />
                                <button class="btn" onclick="return confirm('{$lang.confirm_restore}');">
                                    <img src="{$dir}/img/restore.png" alt="{$lang.restore}" title="{$lang.restore}" /> 
                                </button>
                            </form>
                            <form action="" method="post" class="action-form">   
                                <input type="hidden" name="do" value="removeBackup" />
                                <input type="hidden" name="backup_id" value="{$entry.backup.id}" />
                                <button class="btn" onclick="return confirm('{$lang.confirm_delete}');">
                                    <img src="{$dir}/img/delete.png" alt="{$lang.delete}" title="{$lang.delete}" /> 
                                </button>
                            </form>     
                               
                            {*<form action="" method="post" class="action-form"> 
                                <input type="hidden" name="do" value="create_templateBackup" />
                                <input type="hidden" name="backup_id" value="{$entry.backup.id}" />
                                <a href="#" class="btn" onclick="jQuery(this).closest('form').submit();return false;"  title="{$lang.create_template}" />
                                    <img src="{$dir}/img/create_template.png" alt="{$lang.create_template}" />
                                </a>
                            </form>   *} 
                                
                        </td>
                    </tr>
                {foreachelse}
                    <tr>
                        <td colspan="8" class="td_center">{$lang.nothing_label}</td>
                    </tr>
                {/foreach}
            </tbody>
          </table>
</div>            
