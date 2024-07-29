<link rel="stylesheet" type="text/css" href="{$dir}/css/style.css" />
<div>
    {if $step =='addDisk'}
        {include file="$main_dir/templates/disk_add.tpl"}
    {else}
        <a href="clientarea.php?action=productdetails&id={$id}" class="btn btn-small"><i class="icon-arrow-left"></i> {$lang.back}</a>
        <h2 class="set_main_header">{$lang.main_header}</h2>
        {if $msg_error or $msg_success}
            <div class="alert {if $msg_error}alert-danger{else}alert-success{/if}">
                <p></p><li>{if $msg_error}{$msg_error}{else}{$msg_success}{/if}</li><p></p>
            </div>
        {/if}
        <table class="table table-striped table-disk">
            <thead>
                <tr>
                    <th>{$lang.disk}</th>
                    <th>{$lang.label}</th>
                    <th>{$lang.size}</th>
                    <th>{$lang.data_store}</th>
                    <th>{$lang.type}</th>
                    <th>{$lang.built}</th>
                    <th>{$lang.backups}</th>
                    <th>{$lang.autobackups}</th>
                    <th>{$lang.actions}</th>
                </tr>
            </thead>
            <tbody>    
                {foreach from=$disks item="entry"}
                    <tr>
                        <td>#{$entry.disk.id}</td>
                        <td>{$entry.disk.label}</td>
                        <td>{$entry.disk.disk_size}{$lang.GB}</td>
                        <td>{$entry.disk.data_store_label}</td>
                        <td>{if $entry.disk.is_swap !=1}{$lang.standard}{else}{$lang.swap}{/if}{if $entry.disk.primary==1}<br />{$lang.primary}{/if}</td>
                        <td>{if $entry.disk.built==1}{$lang.yes}{else}{$lang.no}{/if}</td>
                        <td>{$entry.disk.count_backups}</td>
                        <td>{if $entry.disk.has_autobackups==1}{$lang.yes}{else}{$lang.no}{/if}</td>
                        <td style="text-align:center;">

                            <form action="" method="post" class="action-form" style="display: inline;margin-right: 5px;">   
                                <input type="hidden" name="disk_id" value="{$entry.disk.id}" />
                                <button class="btn mg-delete" name="do" value="deleteDisk" title="{$lang.delete_disk}" data-id="{$entry.disk.id}"  style="background:  transparent;" />
                                <img src="{$dir}/img/delete.png" alt="{$lang.delete_disk}" />
                                </button>
                                {if $entry.disk.is_swap !=1 && !$isFederation && !$allow_incremental_backups}   
                                    <button class="btn" name="do" value="createBackup" title="{$lang.create_backup}" style="background:  transparent;"/>
                                    <img src="{$dir}/img/create_backup.png" alt="{$lang.create_backup}" />
                                    </button>
                                {/if}
                            </form>
                            {if $entry.disk.is_swap !=1 && !$isFederation}    
                                <form action="clientarea.php?action=productdetails&id={$id}&modop=custom&a=management&page=schedule" method="post" style="display: inline;" class="action-form">   
                                    <input type="hidden" name="disk_id" value="{$entry.disk.id}" />
                                    <button class="btn" id="schedule"  title="{$lang.schedule}" style="background:  transparent;" />
                                    <img src="{$dir}/img/calendar.png" alt="{$lang.schedule}" />
                                    </button>
                                </form>    
                            {/if}
                        </td>
                    </tr>
                {foreachelse}
                    <tr>
                        <td colspan="9" class="td_center">{$lang.nothing_label}</td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
        <a href="clientarea.php?action=productdetails&id={$id}&&modop=custom&a=management&page=disk&do=addDisk" title="{$lang.create_disk}" class="btn btn-success">{$lang.create_disk}</a>
    {/if}

    <form  action="" method="post">
        <div class="modal fade bs-example-modal-lg" id="mg-modal-disk-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title">{$lang.delete_disk}<strong data-modal-var="name"></strong></h4>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="disk_id" id="mg-delete-id" value="" />
                        
                        
                        <div class="modal-alerts">
                            <div style="display:none;" data-prototype="error">
                                <div class="note note-danger">
                                    <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only"></span></button>
                                    <strong></strong>
                                    <a style="display:none;" class="errorID" href=""></a>
                                </div>
                            </div>
                            <div style="display:none;" data-prototype="success">
                                <div class="note note-success">
                                    <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only"></span></button>
                                    <strong></strong>
                                </div>
                            </div>
                        </div>
                        <div style="margin: 30px; text-align: center;">
                            <div> {$lang.confirm_delete}</div>
                        </div>
                        <div  style="margin: 30px;">
                            <div class="form-group">
                                <label></label>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox"  value="1" name="delete[force_reboot]" id="mg-force-reboot">{$lang.force_reboot} 
                                    </label>
                                </div>
                            </div>
                            <div class="form-group mg-force-reboot-content" style="display:none;">
                                <label>{$lang.shutdown_type}</label>
                                <select name="delete[shutdown_type]" class="form-control">
                                    <option value="hard">{$lang.power_off}</option>
                                    <option value="graceful">{$lang.gracefully}</option>
                                </select>
                            </div>
                            <div class="form-group mg-force-reboot-content" style="display:none;">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox"  value="1" name="delete[required_startup]">{$lang.startup}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger"  name="do" value="deleteDisk" >{$lang.delete}</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">{$lang.close}</button>
                    </div>
                </div>
            </div>
        </div>
    </form> 

</div>            
{literal}
    <script type="text/javascript">
        $(document).ready(function () {

            $(".mg-delete").click(function (e) {
                e.preventDefault();
                $("#mg-delete-id").val($(this).attr("data-id"));
                $('#mg-modal-disk-delete').modal();
            });

            $("#mg-force-reboot").click(function (e) {
                $(this).is(":checked") ? $(".mg-force-reboot-content").show() : $(".mg-force-reboot-content").hide();
            });
        });
    </script>
{/literal}