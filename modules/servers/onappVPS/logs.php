<?php


/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */
         
$transactions = $vm->getTransactions();

if($vm->isSuccess()){
    $vars['logs']      = $transactions;
}else {
    $vars['msg_error'] = $vm->error();

}


