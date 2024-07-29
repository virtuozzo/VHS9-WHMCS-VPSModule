<?php

/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */
if ($vars['disallow_action']['backups'] == 1)
{
    ob_clean();
    header("Location: clientarea.php?action=productdetails&id=" . $params['serviceid']);
    die();
}

if (isset($_POST['disk_id']) && $_POST['disk_id'] > 0)
{
    $disk = new NewOnApp_Disk();
    $disk->setconnection($params, true);
    $disk->setID($_POST['disk_id']);

    if (isset($_POST['doAction']))
    {
        switch ($_POST['doAction'])
        {
            case 'save':

                if (isset($_POST['schedule_id']))
                {
                    $disk->editSchedule($_POST['schedule_id'], array(
                        'schedule' => array(
                            'duration' => (int) $_POST['edit']['duration'],
                            'period'   => (string) $_POST['edit']['period'],
                            'status'   => ($_POST['edit']['status'] != 'enabled' ? 'disabled' : 'enabled')
                        )
                    ));
                    if ($disk->isSuccess())
                    {
                        $vars['msg_success'] = $vars['lang']['schedule_edited'];
                        break;
                    }
                    else
                    {
                        
                    }
                    $vars['msg_error'] = $disk->error();
                }
                else
                    $vars['msg_error'] = $vars['lang']['invalid_schedule_id'];
            case 'edit':
                $vars['step']      = 'edit';
                if (isset($_POST['schedule_id']))
                {
                    $details = $disk->getScheduleDetails($_POST['schedule_id']);
                    if ($disk->isSuccess())
                    {
                        $vars['data'] = $details;
                    }
                    else
                        $vars['msg_error'] = $disk->error();
                }
                else
                    $vars['msg_error'] = $vars['lang']['invalid_schedule_id'];
                break;

            case 'new':

                $vars['step'] = 'add';
                break;

            case 'delete':
                if (isset($_POST['schedule_id']))
                {
                    $disk->deleteSchedule($_POST['schedule_id']);
                    if ($disk->isSuccess())
                    {
                        $vars['msg_success'] = $vars['lang']['schedule_deleted'];
                    }
                    else
                        $vars['msg_error'] = $disk->error();
                }
                else
                    $vars['msg_error'] = $vars['lang']['invalid_schedule_id'];
                break;

            case 'add':

                if (isset($_POST['add']['duration']))
                {

                    $disk->addSchedule(array(
                        'schedule' => array(
                            'action'   => 'autobackup',
                            'duration' => $_POST['add']['duration'],
                            'period'   => (string) $_POST['add']['period']
                        )
                    ));
               

                    if ($disk->isSuccess())
                    {

                        $vars['msg_success'] = $vars['lang']['schedule_added'];
                    }
                    else
                    {

                        $vars['msg_error'] = $disk->error();
                    }
                }
                break;
        }
    }


    if ($disk->isSuccess())
    {
        $vars['disk_id']   = (int) $_POST['disk_id'];
        $vars['schedules'] = $details;
    }
    else
        $vars['msg_error'] = $disk->error();
} else
{

    $vm = new NewOnApp_VM($vars['params']['customfields']['vmid']);
    $vm->setconnection($params);


    if (isset($_POST['doAction']))
    {
        switch ($_POST['doAction'])
        {

            case 'save':

                if (isset($_POST['schedule_id']))
                {

                    $vm->editSchedule($_POST['schedule_id'], array(
                        'schedule' => array(
                            'duration' => (int) $_POST['edit']['duration'],
                            'period'   => (string) $_POST['edit']['period'],
                            'status'   => ($_POST['edit']['status'] != 'enabled' ? 'disabled' : 'enabled')
                        )
                    ));
                    if ($vm->isSuccess())
                    {
                        $vars['msg_success'] = $vars['lang']['schedule_edited'];
                        break;
                    }
                    else
                    {
                        
                    }
                    $vars['msg_error'] = $vm->error();
                }
                else
                    $vars['msg_error'] = $vars['lang']['invalid_schedule_id'];
            case 'edit':
                $vars['step']      = 'edit';
                if (isset($_POST['schedule_id']))
                {
                    $details = $vm->getSchedules();
                    foreach ($details as $d)
                    {
                        if ($d['schedule']['id'] == $_POST['schedule_id'])
                        {
                            $detailsSchedule = $d;
                            break;
                        }
                    }
                    if ($vm->isSuccess())
                    {
                        $vars['data'] = $detailsSchedule;
                    }
                    else
                        $vars['msg_error'] = $vm->error();
                }
                else
                    $vars['msg_error'] = $vars['lang']['invalid_schedule_id'];
                break;

            case 'new':

                $vars['step'] = 'add';
                break;

            case 'delete':
                if (isset($_POST['schedule_id']))
                {
                    $vm->deleteSchedule($_POST['schedule_id']);
                    if ($vm->isSuccess())
                    {
                        $vars['msg_success'] = $vars['lang']['schedule_deleted'];
                    }
                    else
                        $vars['msg_error'] = $vm->error();
                }
                else
                    $vars['msg_error'] = $vars['lang']['invalid_schedule_id'];
                break;

            case 'add':


                if (isset($_POST['add']['duration']))
                {
                        $vm->addSchedule(array(
                        'schedule' => array(
                            'action'   => 'autobackup',
                            'duration' => (int) $_POST['add']['duration'],
                            'period'   => (string) $_POST['add']['period']
                        )
                    ));
              
                    if ($vm->isSuccess())
                    {
                       $vars['msg_success'] = $vars['lang']['schedule_added'];
                    }
                    else
                    {
                       $vars['msg_error'] = $vm->error();
                    }
                   }
                  break;
        }
    }

    $details = $vm->getSchedules();

    foreach ($details as $value)
    {

        if ($value['schedule']['action'] == 'autobackup')
        {
            $value['schedule']['action'] = 'auto&nbspbackup';

            $detailsToTemplate[] = $value;
        }
    }
    $vars['schedules'] = $detailsToTemplate;
}
