<link rel="stylesheet" type="text/css" href="{$dir}/css/style.css" />
<div style="overflow: auto">
    <h2 class="set_main_header">{$lang.main_header}</h2> 
        <div id="vm_alerts">
            {if $result}
                <div class="alert {if $result == 'success'}alert-success{else}alert-danger{/if}">
                    {$resultmsg}
                </div>
            {/if}
        </div>
    {if $vpsdata.id}
        <div id="serverstats">
            <table width="90%" class="table table-striped">
                <tr><td>{$lang.server_status}</td><td><span id="serverstatus"></span> <a href="#" onclick="doAction('details');return false;"><img src="{$dir}/img/refresh.png" alt="" /></a></td></tr>
                <tr><td>{$lang.label}</td><td class="vps_label">{$vpsdata.label}</td></tr>
                <tr><td>{$lang.booted}</td><td class="vps_booted">{if $vpsdata.booted==true}<span class="green">{$lang.yes}</span>{else}<span class="red">{$lang.no}</span>{/if}</td></tr>
                <tr><td>{$lang.built}</td><td class="vps_built">{if $vpsdata.built==true}<span class="green">{$lang.yes}</span>{else}<span class="red">{$lang.no}</span>{/if}</td></tr>
                <tr><td>{$lang.recovery_mode}</td><td class="vps_recovery">{if $vpsdata.recovery_mode==true}<span class="green">{$lang.yes}</span>{else}<span class="red">{$lang.no}</span>{/if}</td></tr>
                <tr><td>{$lang.password}</td><td>
                            <button class="btn btn-small" onclick="doAction('showPass');return false;" id="showPass">{$lang.show} </button> 
                            <input type='text' value='"+obj.initial_root_password+"'  id='onapp_root_password' style="display:none;" />
                            <button id='hidePass'  class='btn btn-small' style='vertical-align:top; display:none;' onclick="doAction('hidePass');return false;">{$lang.hide}</button>
                      <button  class='btn btn-small' id="showChangePass" onclick="doAction('changePassForm');return false;" style='vertical-align:top'>{$lang.change}</button>
                      <button  class='btn btn-small' id="changePass"  style='vertical-align:top; display:none;'>{$lang.change}</button>
                      </td></tr>
                <tr><td>{$lang.cpus}</td><td>{$vpsdata.cpus}</td></tr>
                {if $hide_cpu !=1}<tr><td>{$lang.shares}</td><td>{$vpsdata.cpu_shares}%</td></tr>{/if}
                <tr><td>{$lang.memory_size}</td><td>{$vpsdata.memory} {$lang.MB}</td></tr>
                <tr><td>{$lang.disk_size}</td><td>{$vpsdata.total_disk_size} {$lang.GB}</td></tr>
                <tr><td>{$lang.monthly_bandwidth_used}</td><td><span class="vps_bandwidth">{$vpsdata.monthly_bandwidth_used}</span> {$lang.GB}</td></tr>
                <tr><td>{$lang.ip}</td><td>{if $vpsdata.network_address}{$vpsdata.network_address}{elseif $vpsdata.ip_addresses}{foreach from=$vpsdata.ip_addresses item="entry"}{$entry.ip_address.address}<br />{/foreach}{/if}</td></tr>
                <tr><td>{$lang.template_image}</td><td class="vps_template">{$vpsdata.template_label}</td></tr> 
                <tr><td>{$lang.created_at}</td><td class="vps_created">{$vpsdata.created_at}</td></tr>
                <tr><td>{$lang.updated_at}</td><td class="vps_updated">{$vpsdata.updated_at}</td></tr>
                {if $vpsdata.domain}
                <tr><td>{$lang.Domain}</td><td>{$vpsdata.domain}</td></tr>
                {/if}
                <tr><td>{$lang.acceleration}</td><td id="mg-acceleration-status">{if $vpsdata.acceleration_allowed}{$lang.enabled}{else}{$lang.disabled}{/if}</td></tr>
            </table>
        </div>  

        <div id="rbuttons">
                <button class="btn" onclick="doAction('start');return false;" {if $vpsdata.locked==1}disabled{/if}><img class="manage_img" src="{$dir}/img/power_on.png"/> {$lang.start}</button>
                <button class="btn" onclick="doAction('stop');return false;" {if $vpsdata.locked==1}disabled{/if}><img class="manage_img" src="{$dir}/img/control_pause.png"/> {$lang.stop}</button>
                <button class="btn" onclick="doAction('shutdown');return false;" {if $vpsdata.locked==1}disabled{/if}><img class="manage_img" src="{$dir}/img/power_off.png"/> {$lang.shutdown}</button>
                <button class="btn" onclick="doAction('reboot');return false;" {if $vpsdata.locked==1}disabled{/if}><img class="manage_img" src="{$dir}/img/reboot.png"/> {$lang.reboot}</button>
                <button class="btn" onclick="doAction('recovery');return false;" {if $vpsdata.locked==1}disabled{/if}><img class="manage_img" src="{$dir}/img/recovery.png"/> {$lang.recovery}</button>
                <button class="btn" onclick="window.location='clientarea.php?action=productdetails&id={$id}&modop=custom&a=management&page=rebuild'; return false;" {if $vpsdata.locked==1}disabled{/if}><img class="manage_img" src="{$dir}/img/rebuild.png"/> {$lang.rebuild}</button>
                <button class="btn" onclick="doAction('unlock');return false;" {if $vpsdata.locked!=1}style="display:none;"{/if} id="unlock" ><img class="manage_img" src="{$dir}/img/unlock.png"/> {$lang.unlock}</button>
                <button class="btn" {if $vpsdata.locked==1}disabled{/if} onclick="window.open('clientarea.php?action=productdetails&id={$id}&modop=custom&a=management&page=console','','width=820,height=750'); return false;"><img class="manage_img" src="{$dir}/img/console.png"/> {$lang.console}</button>
                {if $disallow_action.accelerator!=1}
                <button class="btn {if $vpsdata.acceleration_allowed==1}hidden{/if}" onclick="doAction('acceleratorEnable');return false;" id="mg-accelerator-enable" ><i class="glyphicon glyphicon-flash"></i> {$lang.accelerator_on}</button>
                <button class="btn {if $vpsdata.acceleration_allowed!=1}hidden{/if}" onclick="doAction('acceleratorDisable');return false;" id="mg-accelerator-disable"><i class="glyphicon glyphicon-flash"></i> {$lang.accelerator_off}</button>
                {/if}
        </div>
        <h3 class="header_label">{$lang.additionals}</h3>
        <div id='nbuttons'>
                {if $disallow_action.firewall!=1}
                    <button class="btn" onclick="window.location='clientarea.php?action=productdetails&id={$id}&modop=custom&a=management&page=firewall'; return false;"><img class="manage_img" src="{$dir}/img/firewall.png"/> {$lang.firewall_manage}</button>
                {/if}
                {if $disallow_action.ip!=1}
                    <button class="btn" onclick="window.location='clientarea.php?action=productdetails&id={$id}&modop=custom&a=management&page=manageip'; return false;"><img class="manage_img" src="{$dir}/img/network.png"/> {$lang.ip_manage}</button>
                {/if}
                {if $disallow_action.network!=1}
                    <button class="btn" onclick="window.location='clientarea.php?action=productdetails&id={$id}&modop=custom&a=management&page=network'; return false;"><img class="manage_img" src="{$dir}/img/network2.png"/> {$lang.network}</button>
                {/if}
                {if $disallow_action.stats!=1}
                    <button class="btn" onclick="window.location='clientarea.php?action=productdetails&id={$id}&modop=custom&a=management&page=stats'; return false;"><img class="manage_img" src="{$dir}/img/graphs.png"/> {$lang.graphs}</button>
                {/if}
                {if $disallow_action.disk!=1}
                    <button class="btn" onclick="window.location='clientarea.php?action=productdetails&id={$id}&modop=custom&a=management&page=disk'; return false;"><img class="manage_img" src="{$dir}/img/disk.png"/> {$lang.disk_manage}</button>
                {/if}
                {if $disallow_action.backups!=1}
                    <button class="btn" onclick="window.location='clientarea.php?action=productdetails&id={$id}&modop=custom&a=management&page=backups'; return false;"><img class="manage_img" src="{$dir}/img/backup.png"/> {$lang.backups}</button>
                {/if}
                {if $disallow_action.autoscalling!=1 && !$isFederation}
                    <button class="btn" onclick="window.location='clientarea.php?action=productdetails&id={$id}&modop=custom&a=management&page=autoscalling'; return false;"><img class="manage_img" src="{$dir}/img/resize.png"/> {$lang.autoscalling}</button>
                {/if}
                {if $disallow_action.api_info !=1}
                    <button class="btn" onclick="window.location='clientarea.php?action=productdetails&id={$id}&modop=custom&a=management&page=apiInfo'; return false;"><span class='icon-info-sign'><i></i></span> {$lang.apiInfo}</button>
                {/if}
        </div>
        
        <h3 class="header_label">{$lang.logs}</h3>
        <table class="table table-bordered">
            <thead>
                <tr><th>{$lang.date}</th><th>{$lang.action}</th><th>{$lang.status}</th></tr>
            </thead>
            <tbody class="ajax-logs">
                
                {foreach from=$logs item="entry"}
                    <tr>
                        <td>{$entry.log_item.created_at|date}</td>
                        <td class="capitalize">{$entry.log_item.action|replace:'_':' '}</td>
                        <td class="{if $entry.log_item.status=='complete'}bg_green{elseif $entry.log_item.status=='running'}bg_blue{elseif $entry.log_item.status=='pending'}bg_yellow{else}bg_red{/if} status">{$entry.log_item.status}</td>
                    </tr>
                {foreachelse}
                    <tr><td colspan="3">{$lang.nothing_label}</td></tr>
                {/foreach}
            </tbody>
            <tfoot class="ajax-pages">
            {if $logs.pages>1}
               <tr><td colspan="3">{section name=i start=1 loop=$logs.pages+1 step=1}<a href="clientarea.php?action=productdetails&id={$id}&modop=custom&a=management&lp={$smarty.section.i.index-1}" class="btn btn-small {if $smarty.section.i.index==$curr_log_page+1}disabled btn-info{/if}">{$smarty.section.i.index}</a> {/section}</td></tr>
            {/if}
            </tfoot>
        </table>
        <script type="text/javascript">{literal} 
            var blocked = false;
            jQuery(document).ready(function() {
                setInterval("doAction('details')",60000);
                setInterval("getLogs()",60000);
                jQuery(document).ajaxStart(function() {
                    jQuery("#serverstatus").show();
                    jQuery("#serverstatus").html( "<img src=\"modules/servers/onappVPS/img/loadingsml.gif\" />" );
                }).ajaxStop(function() {jQuery("#serverstatus").hide();});
                
                $("body").delegate("#changePass", "click",function(){ 
                      var onapp_root_password =  $("#onapp_root_password").val();
                      
                      $.post("clientarea.php?action=productdetails&id={/literal}{$id}{literal}",{"doAction": 'changePass',"ajax":"1", "onapp_root_password": onapp_root_password }, 
                       function(res){
                             if(res.result==1){
                                  doAction("hidePass");
                                  jQuery("#vm_alerts").show();
                                  jQuery("#vm_alerts").html('<div class="alert alert-success">'+res.msg+'</div>').delay(8200).fadeOut(300); 
                             }else{
                                   $("#changePass").show();
                                  jQuery("#vm_alerts").show();
                                   jQuery("#vm_alerts").html('<div class="alert alert-danger">'+res.error+'</div>').delay(8200).fadeOut(300);
                             }
                             
                      }, "json");
                      return false;
                });
            });

            function dateFormat(date){
                 var created = new Date(date);
                 var day     = created.getDate();
                 var month   = created.getMonth()+1; 
                 var year    = created.getFullYear();
                 var hours   = created.getHours();
                 var min     = created.getMinutes();
                 var sec     = created.getSeconds()
                 return (day <= 9 ? '0' + day : day)+'-'+(month<=9 ? '0' + month : month)+'-'+year+' '+(hours <= 9 ? '0' + hours : hours)+':'+(min <= 9 ? '0' + min : min)+':'+(sec <= 9 ? '0' + sec : sec);
            }


            function getLogs(){
                jQuery.post("clientarea.php?action=productdetails&id={/literal}{$id}{literal}",{doAction: 'logs',ajax:1},function(data){     
                    var obj   = jQuery.parseJSON( data );
                    jQuery(".ajax-logs").html('');
                    var name  = null;
                    jQuery(obj).each(function(index,value){
                        if(value.log_item.status=='complete')
                            name = 'bg_green';
                        else if(value.log_item.status=='running')
                            name = 'bg_blue';
                        else if(value.log_item.status=='pending')
                            name = 'bg_yellow';
                        else 
                            name = 'bg_red';
                        jQuery(".ajax-logs").append("<tr><td>"+value.log_item.created_at+"</td><td class='capitalize'>"+value.log_item.action.replace(/_/g, " ")+"</td><td class='status "+name+"'>"+value.log_item.status+"</td></tr>");
                    });
                    if(obj.pages>1){
                        var i=null;
                        var content = '<tr><td colspan="3">';
                        var cur     = window.location.search;
                        if(cur.match(/lp=\d+/)){
                              cur         = cur.match(/lp=\d+/);
                              cur         = cur[0].split("=");
                              for(i=1; i <= obj.pages; i++)
                              {
                                  var page = parseInt(cur[1])+1;
                                  content+='<a href="clientarea.php?action=productdetails&id={/literal}{$id}{literal}&amp;modop=custom&amp;a=management&lp='+(i-1)+'" class="btn btn-small '+(page == i ? 'disabled btn-info' : '')+' ">'+i+'</a> ';
                              }
                              content += '</td></tr>';

                              jQuery(".ajax-pages").html(content);
                        }
                    }
                });
                
            }

            function doAction(action){
                if(action == 'hidePass')
                {
                   $("#onapp_root_password").hide();
                   $("#hidePass").hide();
                   $("#showChangePass").show();
                   jQuery("#showPass").show();
                   $("#changePass").hide();
                   return false;
                }
                if(blocked==false){ 
                    blocked = true;
                    if(action != 'details' || action !='showPass')
                        jQuery("#rbuttons button").attr('disabled',true);

                    $.ajax({
                        url: "clientarea.php?action=productdetails&id={/literal}{$id}{literal}",
                        type: "POST",
                        timeout: 60000,
                        data: {doAction: action,ajax:1},
                        success: function(data) { 
                            blocked = false;
                            jQuery("#rbuttons button").attr('disabled',false);
                                var obj = jQuery.parseJSON(data);
                                if(typeof obj =='object'){
                                    if(obj.error){
                                        jQuery("#vm_alerts").html('<div class="alert alert-danger">'+obj.error+'</div>').delay(8200).fadeOut(300);
                                        jQuery("#vm_alerts").show();
                                        return false;
                                    }
                                    jQuery(".vps_label").text(obj.label);

                                    if(obj.booted==true){
                                       jQuery(".vps_booted").html('<span class="green">{/literal}{$lang.yes}{literal}</span>');
                                       jQuery("#btn-console").show();
                                    }
                                    else{
                                       jQuery(".vps_booted").html('<span class="red">{/literal}{$lang.no}{literal}</span>');
                                       jQuery("#btn-console").hide();
                                    }
                                    if(obj.built==true)
                                       jQuery(".vps_built").html('<span class="green">{/literal}{$lang.yes}{literal}</span>');
                                    else
                                       jQuery(".vps_built").html('<span class="red">{/literal}{$lang.no}{literal}</span>');

                                    if(obj.recovery_mode==true)
                                       jQuery(".vps_recovery").html('<span class="green">{/literal}{$lang.yes}{literal}</span>');
                                    else
                                       jQuery(".vps_recovery").html('<span class="red">{/literal}{$lang.no}{literal}</span>');

                                    jQuery(".vps_bandwidth").text(obj.monthly_bandwidth_used);
                                    jQuery(".vps_created").text(obj.created_at);
                                    jQuery(".vps_updated").text(obj.updated_at);  
                                    jQuery("#vm_alerts").html('');
                                    if(obj.locked==1){
                                        jQuery("#rbuttons button").attr('disabled',true);
                                        jQuery("#unlock").show().attr('disabled',false);
                                    }
                                    else{
                                        jQuery("#rbuttons button").attr('disabled',false);
                                        jQuery("#unlock").hide();
                                    }
                                    if(obj.acceleration_allowed == true){
                                        $("#mg-acceleration-status").text('{/literal}{$lang.enabled}{literal}');
                                    }
                                    else
                                    {
                                        $("#mg-acceleration-status").text('{/literal}{$lang.disabled}{literal}');
                                    }
                                    if(action == 'showPass')
                                    {
                                          $("#onapp_root_password").val(obj.initial_root_password).show();
                                          $("#onapp_root_password").attr("readonly", true);
                                          jQuery("#hidePass").show();
                                          jQuery("#showPass").hide();
                                          $("#showChangePass").hide();
                                    }
                                    else if(action == 'changePassForm'){
                                          $("#onapp_root_password").val(obj.initial_root_password).show();
                                          $("#onapp_root_password").attr("readonly", false);
                                           jQuery("#hidePass").show();
                                           jQuery("#showPass").hide();
                                           $("#showChangePass").hide();
                                            jQuery("#changePass").show();
                                    }
                                    else if(action != 'details')
                                    {
                                         jQuery("#vm_alerts").show();
                                         jQuery("#vm_alerts").html('<div class="alert alert-success">{/literal}{$lang.success}{literal}</div>').delay(8200).fadeOut(300);
                                    }
                            }else {
                                jQuery("#vm_alerts").html('<div class="alert alert-danger">'+data+'</div>').delay(8200).fadeOut(300);
                                jQuery("#vm_alerts").show();
                            }
                            if(action == 'acceleratorEnable'){
                                $("#mg-accelerator-enable").addClass('hidden');
                                $("#mg-accelerator-disable").removeClass('hidden');
                            }else if(action == 'acceleratorDisable'){
                                $("#mg-accelerator-disable").addClass('hidden');
                                $("#mg-accelerator-enable").removeClass('hidden');
                            }
                            getLogs();
                    }});
               }  
                return false;
            }
            
        {/literal}</script>     
     {/if}
    {if $billing_resources}
        <br />
        <h3 class="header_label">{$mg_lang.usage_records} {if $records_range.start_date}<span style="float: right">{$mg_lang.period} {$records_range.start_date|date_format:"%d/%m/%y"} - {$records_range.end_date|date_format:"%d/%m/%y"}{/if}</h3>
        <table class="table table-bordered">
            <thead>
                <tr class="title">
                    <th>{$mg_lang.record}</th>
                    <th>{$mg_lang.usage}</th>
                    <th>{$mg_lang.total}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$billing_resources item=r}
                    <tr>
                        <td>{$r.FriendlyName} {if $r.name} - {$r.name} {/if}</td>
                        <td>{$r.usage}{$r.unit}</td>
                        <td>{$r.total}</td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    {/if}
</div>