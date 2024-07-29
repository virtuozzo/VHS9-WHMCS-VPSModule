{**
 * @author Maciej Husak <maciej@modulesgarden.com>
 *}
<form action="{$console.url}/console_remote/{$console.remote_access_session.remote_key}" method="get" id="console"> 
    <p>{$lang.please_wait}</p>
</form>     
<script type="text/javascript">
    {literal}
    jQuery(document).ready(function()
    {
       jQuery("#console").submit();
    });
    {/literal}
</script>
