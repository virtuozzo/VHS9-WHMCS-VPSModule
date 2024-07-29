<a href="clientarea.php?action=productdetails&id={$id}&modop=custom&a=management&page=disk" class="btn btn-small"><i class="icon-arrow-left"></i> {$lang.back}</a>
<h2 class="set_main_header">{$lang.header_label}</h2>
{if $msg_error or $msg_success}
    <div class="alert {if $msg_error}alert-danger{else}alert-success{/if}">
        <p></p><li>{if $msg_error}{$msg_error}{else}{$msg_success}{/if}</li><p></p>
    </div>
{/if}
    <p>{$lang.description_add}</p>
    <form action="" method="post">
        <table class="table table-striped">
            <tr>
                <td>{$lang.duration}</td>
                <td><input type="text" name="add[duration]" value="" /></td>
            </tr>
            <tr>
                <td>{$lang.period}</td>
                <td>
                    <select name="add[period]">
                       <option value="days">{$lang.days}</option>
                       <option value="weeks">{$lang.weeks}</option>
                       <option value="months">{$lang.months}</option>
                       <option value="years">{$lang.years}</option>
                    </select>
                </td>
            </tr>
        </table>
        <input type="hidden" name="doAction" value="add" />
        <input type="hidden" name="disk_id" value="{$disk_id}" />
        <input type="submit" class="btn btn-success" value="{$lang.save}" />  
   </form>           
