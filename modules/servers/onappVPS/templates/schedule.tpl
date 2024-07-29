<link rel="stylesheet" type="text/css" href="{$dir}/css/style.css" />
<div>
{if $step == 'add'}
    {include file="$main_dir/templates/schedule_add.tpl"}
{elseif $step == 'edit'}
    {include file="$main_dir/templates/schedule_edit.tpl"}    
{else}
    <a href="clientarea.php?action=productdetails&id={$id}" class="btn btn-small"><i class="icon-arrow-left"></i> {$lang.back}</a>
    <h2 class="set_main_header">{$lang.main_header}</h2>
    {if $msg_error or $msg_success}
        <div class="alert {if $msg_error}alert-danger{else}alert-success{/if}">
            <p></p><li>{if $msg_error}{$msg_error}{else}{$msg_success}{/if}</li><p></p>
        </div>
    {/if}
         <table class="table table-striped schedules">
            <thead>
                <tr>
                    <th>{$lang.date}</th>
                    <th>{$lang.target}</th>
                    <th>{$lang.action}</th>
                    <th>{$lang.duration}</th>
                    <th>{$lang.period}</th>
                    <th>{$lang.nextstart}</th>
                    <th>{$lang.status}</th>
                    <th>{$lang.actions}</th>
                </tr>
            </thead>
            <tbody>    
                {foreach from=$schedules item="entry"}
                    <tr>
                        <td>{$entry.schedule.created_at|date_format:"%d-%m-%Y %H:%M:%S"}</td>
                        <td>{$entry.schedule.target_type}</td>
                        <td>{$entry.schedule.action}</td>
                        <td>{$entry.schedule.duration}</td>
                        <td>{$entry.schedule.period}</td>
                        <td>{$entry.schedule.start_at|date_format:"%d-%m-%Y %H:%M:%S"}</td>
                        <td>{$entry.schedule.status}</td>
                        <td>
                            <form action="" method="post">
                                <input type="hidden" name="schedule_id" value="{$entry.schedule.id}" />
                                <input type="hidden" name="disk_id" value="{$disk_id}" />
                                <button name="doAction" value="edit" class="btn btn-info btn-mini"><i class="icon-edit"></i> {$lang.edit}</button>
                                <button name="doAction" value="delete" class="btn btn-danger btn-mini"><i class="icon-remove"></i> {$lang.delete}</button>
                            </form>
                        </td>
                    </tr>
                {foreachelse}
                    <tr>
                        <td colspan="8" class="td_center">{$lang.empty}</td>
                    </tr>
                {/foreach}
            </tbody>
          </table>
          <form action="" method="post">
              <input type="hidden" name="doAction" value="new" />
              <input type="hidden" name="disk_id" value="{$disk_id}" />
              <input type="submit"  class="btn btn-success" value="{$lang.new_schedule}" />  
          </form>
{/if}
</div>            
