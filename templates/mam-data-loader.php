<?php
$request = 'resources';

if(strpos($_SERVER['HTTP_REFERER'], 'mamdevsite.com/mam-lb/client') !== false){
    $request = 'client-orders';
}

if($request == 'resources'){
    include_once 'mam-data-resources.php';
}