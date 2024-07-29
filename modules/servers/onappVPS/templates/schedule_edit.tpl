<a href="clientarea.php?action=productdetails&id={$id}&modop=custom&a=management&page=disk" class="btn btn-small"><i class="icon-arrow-left"></i> {$lang.back}</a>
<h2 class="set_main_header">{$lang.header_edit_label}</h2>
{if $msg_error or $msg_success}
    <div class="alert {if $msg_error}alert-danger{else}alert-success{/if}">
        <p></p><li>{if $msg_error}{$msg_error}{else}{$msg_success}{/if}</li><p></p>
    </div>
{/if}
    <p>{$lang.description_edit}</p>
    <form action="" method="post">
        <table class="table table-striped">
            <tr>
                <td>{$lang.target}</td>
                <td><a href="clientarea.php?action=productdetails&id={$id}&modop=custom&a=management&page=disk">{$lang.disk}#{$data.schedule.target_id}</a></td>
            </tr>
            <tr>
                <td>{$lang.duration}</td>
                <td><input type="text" name="edit[duration]" value="{$data.schedule.duration}" /></td>
            </tr>
            <tr>
                <td>{$lang.period}</td>
                <td>
                    <select name="edit[period]">
                       <option value="days"   {if $data.schedule.period =='days'}selected{/if}>{$lang.days}</option>
                       <option value="weeks"  {if $data.schedule.period =='weeks'}selected{/if}>{$lang.weeks}</option>
                       <option value="months" {if $data.schedule.period =='months'}selected{/if}>{$lang.months}</option>
                       <option value="years"  {if $data.schedule.period =='years'}selected{/if}>{$lang.years}</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>{$lang.enabled}</td>
                <td><input type="checkbox" name="edit[status]" value="enabled" {if $data.schedule.status =='enabled'}checked{/if}/></td>
            </tr>
        </table>
        <input type="hidden" name="doAction" value="save" />
        <input type="hidden" name="disk_id" value="{$disk_id}" />
        <input type="hidden" name="schedule_id" value="{$data.schedule.id}" />
        <input type="submit" class="btn btn-success" value="{$lang.save}" />  
   </form>

       
