<?php
require __DIR__ . '/class/database.php';
require __DIR__ . '/class/UIMsg.php';

if (isset($_GET['q'])) {
    $key = $_GET['q'];
    $key = htmlspecialchars($key);

    $db = new Akut\Database();

    if($db->checkSubscriptionStatus($key))

        if($db->removeUser($key)){
            UIMsg::unsubscribeMsg();
        }else{
            UIMsg::genericError();
        }
    } else {
       UIMsg::genericError();
    }
