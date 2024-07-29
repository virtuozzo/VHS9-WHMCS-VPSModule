<link rel="stylesheet" type="text/css" href="{$dir}/css/style.css" />
<div>
  
        <a href="clientarea.php?action=productdetails&id={$id}" class="btn btn-small"><i class="icon-arrow-left"></i> {$lang.back}</a>
        <h2 class="set_main_header">{$lang.main_header}</h2>
        {if $msg_error or $msg_success}
            <div class="alert {if $msg_error}alert-danger{else}alert-success{/if}">
                <p></p><li>{if $msg_error}{$msg_error}{else}{$msg_success}{/if}</li><p></p>
            </div>
        {/if}
        <br/>
        <form action="" method="post">
              <input type="hidden" name="regenerate" value="1" />
            <table class="table ip_address table-striped">

                <tbody>  
                      <tr>
                            <td>{$lang.apiLogin}</td>
                            <td>{$userDetails.username}</td>
                       </tr>
                      <tr>
                            <td>{$lang.apiKey}</td>
                            <td>{$userDetails.password}</td>
                       <tr>     
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5"><button tyle="submit" class="btn btn-success">{$lang.regenerate}</button></td>
                    </tr>
                </tfoot>
            </table>
        </form>

</div>            