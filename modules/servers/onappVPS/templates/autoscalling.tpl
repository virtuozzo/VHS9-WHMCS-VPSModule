<link rel="stylesheet" type="text/css" href="{$dir}/css/style.css" />
<script type="text/javascript" src="{$dir}/js/highcharts.js"></script>
<div>
    <a href="clientarea.php?action=productdetails&id={$id}" class="btn btn-small"><i class="icon-arrow-left"></i> {$lang.back}</a>
    <h2 class="set_main_header">{$lang.main_header}</h2>
    {if $msg_error or $msg_success}
        <div class="alert {if $msg_error}alert-danger{else}alert-success{/if}">
            <p></p><li>{if $msg_error}{$msg_error}{else}{$msg_success}{/if}</li><p></p>
        </div>
    {/if}
</div>
    <div class='autoscale_buttons'>
        <a href="#" class="auto_scaling_tab button active btn" rel="#auto_scaling_memory_chart">{$lang.memory_usage}</a>
        <a href="#" class="auto_scaling_tab button btn" rel="#auto_scaling_disk_chart">{$lang.disk_usage}</a>
        <a href="#" class="auto_scaling_tab button btn" rel="#auto_scaling_cpu_chart">{$lang.cpu_usage}</a>
    </div>
    <div id="autoscale_chart">
        {$chart|replace:'minWidth':'width'}
    </div>
    <form class="form-horizontal" action="clientarea.php?action=productdetails&id={$id}&modop=custom&a=management&page=autoscalling" method="post">
 
    {if $disallow_action.manage_autoscalling_up !=1}
    <table class='table table-bordered autoscale'>
        <thead>
            <tr><th colspan="2">{$lang.up}</th></tr>
        </thead>
        <tbody>
            <tr><th>{$lang.ram}</th><th><input type="checkbox" id="enable_up_ram" {if $rules.memory.up.id>0}checked{/if} /></tr>
            <tr class="up_ram"><td>{$lang.time}</td><td><select name="auto_scaling_configurations[up][memory][for_minutes]" class="chzn-select foursix chzn-done" ><option value="5" selected="selected">5 minutes</option>
                                            <option value="10">10 minutes</option>
                                            <option value="15">15 minutes</option>
                                            <option value="20">20 minutes</option>
                                            <option value="25">25 minutes</option>
                                            <option value="30">30 minutes</option></select></td></tr>
            <tr class="up_ram"><td>{$lang.is_usage_above}</td><td><input type="text" name="auto_scaling_configurations[up][memory][limit_trigger]" value="{$rules.memory.up.limit_trigger}" /> %</td></tr>
            <tr class="up_ram"><td>{$lang.add}</td><td><input type="text" name="auto_scaling_configurations[up][memory][adjust_units]" value="{$rules.memory.up.adjust_units}" /> {$lang.MB}</td></tr>
            <tr class="up_ram"><td>{$lang.24hr}</td><td><input type="text" name="auto_scaling_configurations[up][memory][up_to]" value="{$rules.memory.up.up_to}" /> {$lang.MB}</td></tr>
            
            <tr><th>{$lang.cpu}</th><th><input type="checkbox" id="enable_up_cpu" {if  $rules.cpu.up.id>0}checked{/if} /></tr>
            <tr class="up_cpu"><td>{$lang.time}</td><td><select name="auto_scaling_configurations[up][cpu][for_minutes]" class="chzn-select foursix chzn-done" ><option value="5" selected="selected">5 minutes</option>
                                            <option value="10">10 minutes</option>
                                            <option value="15">15 minutes</option>
                                            <option value="20">20 minutes</option>
                                            <option value="25">25 minutes</option>
                                            <option value="30">30 minutes</option></select></td></tr>
            <tr class="up_cpu"><td>{$lang.is_usage_above}</td><td><input type="text" name="auto_scaling_configurations[up][cpu][limit_trigger]" value="{$rules.cpu.up.limit_trigger}" /> %</td></tr>
            <tr class="up_cpu"><td>{$lang.add}</td><td><input type="text" name="auto_scaling_configurations[up][cpu][adjust_units]" value="{$rules.cpu.up.adjust_units}" /> %</td></tr>
            <tr class="up_cpu"><td>{$lang.24hr}</td><td><input type="text" name="auto_scaling_configurations[up][cpu][up_to]" value="{$rules.cpu.up.up_to}" /> %</td></tr>
            
            <tr><th>{$lang.disk}</th><th><input type="checkbox" id="enable_up_disk" {if  $rules.disk.up.id>0}checked{/if} /></tr>
            <tr class="up_disk"><td>{$lang.time}</td><td><select name="auto_scaling_configurations[up][disk][for_minutes]" class="chzn-select foursix chzn-done" ><option value="5" selected="selected">5 minutes</option>
                                            <option value="10">10 minutes</option>
                                            <option value="15">15 minutes</option>
                                            <option value="20">20 minutes</option>
                                            <option value="25">25 minutes</option>
                                            <option value="30">30 minutes</option></select></td></tr>
            <tr class="up_disk"><td>{$lang.is_usage_above}</td><td><input type="text" name="auto_scaling_configurations[up][disk][limit_trigger]" value="{$rules.disk.up.limit_trigger}" /> %</td></tr>
            <tr class="up_disk"><td>{$lang.add}</td><td><input type="text" name="auto_scaling_configurations[up][disk][adjust_units]" value="{$rules.disk.up.adjust_units}" /> {$lang.GB}</td></tr>
            <tr class="up_disk"><td>{$lang.24hr}</td><td><input type="text" name="auto_scaling_configurations[up][disk][up_to]" value="{$rules.disk.up.up_to}" /> {$lang.GB}</td></tr>
        </tbody>
   </table>  
    {/if}    
        
    {if $disallow_action.manage_autoscalling_down !=1}
  <table class='table table-bordered autoscale'>
        <thead>
            <tr><th colspan="2">{$lang.down}</th></tr>
        </thead>
        <tbody>
            <tr><th>{$lang.ram}</th><th><input type="checkbox" id="enable_down_ram" {if $rules.memory.down.id>0}checked{/if} /></tr>
            <tr class="down_ram"><td>{$lang.time}</td><td><select name="auto_scaling_configurations[down][memory][for_minutes]" class="chzn-select foursix chzn-done" ><option value="5" selected="selected">5 minutes</option>
                                            <option value="10">10 minutes</option>
                                            <option value="15">15 minutes</option>
                                            <option value="20">20 minutes</option>
                                            <option value="25">25 minutes</option>
                                            <option value="30">30 minutes</option></select></td></tr>
            <tr class="down_ram"><td>{$lang.is_usage_below}</td><td><input type="text" name="auto_scaling_configurations[down][memory][limit_trigger]" value="{$rules.memory.down.limit_trigger}" /> %</td></tr>
            <tr class="down_ram"><td>{$lang.remove}</td><td><input type="text" name="auto_scaling_configurations[down][memory][adjust_units]" value="{$rules.memory.down.adjust_units}" /> {$lang.MB}</td></tr>
            {*<tr class="down_ram"><td>{$lang.24hr}</td><td><input type="text" name="auto_scaling_configurations[down][memory][up_to]" value="{$rules.memory.down.up_to}" /> {$lang.MB}</td></tr>*}
            
            <tr><th>{$lang.cpu}</th><th><input type="checkbox" id="enable_down_cpu" {if $rules.cpu.down.id>0}checked{/if} /></tr>
            <tr class="down_cpu"><td>{$lang.time}</td><td><select name="auto_scaling_configurations[down][cpu][for_minutes]" class="chzn-select foursix chzn-done" ><option value="5" selected="selected">5 minutes</option>
                                            <option value="10">10 minutes</option>
                                            <option value="15">15 minutes</option>
                                            <option value="20">20 minutes</option>
                                            <option value="25">25 minutes</option>
                                            <option value="30">30 minutes</option></select></td></tr>
            <tr class="down_cpu"><td>{$lang.is_usage_below}</td><td><input type="text" name="auto_scaling_configurations[down][cpu][limit_trigger]" value="{$rules.cpu.down.limit_trigger}" /> %</td></tr>
            <tr class="down_cpu"><td>{$lang.remove}</td><td><input type="text" name="auto_scaling_configurations[down][cpu][adjust_units]" value="{$rules.cpu.down.adjust_units}" /> %</td></tr>
            {*<tr class="down_cpu"><td>{$lang.24hr}</td><td><input type="text" name="auto_scaling_configurations[down][cpu][up_to]" value="{$rules.cpu.down.up_to}" /> %</td></tr>*}
            
            <tr><th>{$lang.disk}</th><th><input type="checkbox" id="enable_down_disk" {if $rules.disk.down.id>0}checked{/if} /></tr>
            <tr class="down_disk"><td>{$lang.time}</td><td><select name="auto_scaling_configurations[down][disk][for_minutes]" class="chzn-select foursix chzn-done" ><option value="5" selected="selected">5 minutes</option>
                                            <option value="10">10 minutes</option>
                                            <option value="15">15 minutes</option>
                                            <option value="20">20 minutes</option>
                                            <option value="25">25 minutes</option>
                                            <option value="30">30 minutes</option></select></td></tr>
            <tr class="down_disk"><td>{$lang.is_usage_below}</td><td><input type="text" name="auto_scaling_configurations[down][disk][limit_trigger]" value="{$rules.disk.down.limit_trigger}" /> %</td></tr>
            <tr class="down_disk"><td>{$lang.remove}</td><td><input type="text" name="auto_scaling_configurations[down][disk][adjust_units]" value="{$rules.disk.down.adjust_units}" /> {$lang.GB}</td></tr>
            {*<tr class="down_disk"><td>{$lang.24hr}</td><td><input type="text" name="auto_scaling_configurations[down][disk][up_to]" value="{$rules.disk.down.up_to}" /> {$lang.MB}</td></tr>*}
        </tbody>
   </table>   
    {/if}
    {if $disallow_action.manage_autoscalling_up !=1 || $disallow_action.manage_autoscalling_down !=1}
    <input type="hidden" name="doAction" value="save" />
    <input type="submit" value="{$lang.save_changes}" class="btn btn-success" />
    {/if}
</form>        


<script type="text/javascript">
    //<![CDATA[
    jQuery(document).ready(function(){ldelim}
        {if $rules.memory.up.id>0}
            jQuery("select[name='auto_scaling_configurations[up][memory][for_minutes]']").val("{$rules.memory.up.for_minutes}")
        {/if}
        {if $rules.memory.down.id>0}
            jQuery("select[name='auto_scaling_configurations[down][memory][for_minutes]']").val("{$rules.memory.down.for_minutes}")
        {/if}
        {if $rules.cpu.up.id>0}
            jQuery("select[name='auto_scaling_configurations[up][cpu][for_minutes]']").val("{$rules.cpu.up.for_minutes}")
        {/if}
        {if $rules.cpu.down.id>0}
            jQuery("select[name='auto_scaling_configurations[down][cpu][for_minutes]']").val("{$rules.cpu.down.for_minutes}")
        {/if}
        {if $rules.disk.up.id>0}
            jQuery("select[name='auto_scaling_configurations[up][disk][for_minutes]']").val("{$rules.disk.up.for_minutes}")
        {/if}
        {if $rules.disk.down.id>0}
            jQuery("select[name='auto_scaling_configurations[down][disk][for_minutes]']").val("{$rules.disk.down.for_minutes}")
        {/if}
            
        {literal}    
        jQuery("input[type=checkbox]").each(function(){
            if(jQuery(this).is(":checked")){
                 var name =   jQuery(this).closest("tr").next().attr("class");
                 jQuery("."+name).show();
            }
       
        });

        jQuery("input[type=checkbox]").change(function(){
            var name =   jQuery(this).closest("tr").next().attr("class");
            if(jQuery(this).is(":checked"))
                jQuery("."+name).show();
            else {
                jQuery("."+name).hide();
                jQuery("."+name+"").find("input").attr("value","");
            }
        });    
           
        jQuery("#autoscale_chart .chart").eq(1).addClass('hidden');
        jQuery("#autoscale_chart .chart").eq(2).addClass('hidden');
        
        jQuery("#autoscale_chart .chart").eq(0).attr('id','auto_scaling_memory_chart');
        jQuery("#autoscale_chart .chart").eq(1).attr('id','auto_scaling_disk_chart');
        jQuery("#autoscale_chart .chart").eq(2).attr('id','auto_scaling_cpu_chart');
        
    (function() {
      var links  = jQuery('a.auto_scaling_tab');
      var charts = jQuery('.chart');
    
      links.on('click', function(e) {
        e.preventDefault();
        links.removeClass('active');
        charts.addClass('hidden'); 
        var link = jQuery(this);
        link.addClass('active');
        jQuery(link.attr('rel')).removeClass('hidden');
    
        jQuery(document).resize(); // fix chart resizing
      });
    })();
    });
    {/literal} 
</script>
