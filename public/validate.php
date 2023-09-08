<?php
require __DIR__ . "/class/database.php";
require __DIR__ . "/class/UIMsg.php";

if (isset($_GET["q"])) {
    //input sanitizaiton
    $key = $_GET["q"];
    $key = htmlspecialchars($key);

    $db = new Akut\Database();

    if (!$db->checkSubscriptionStatus($key)) {

        if ($db->validateUser($key)) {
            //specificMsg -> "Your Subscription has been activated."
            UIMsg::specificMsg("Your Subscription has been activated.");
        } else {
            //generic error
            UIMsg::genericError();
        }
    } else {
        UIMsg::specificMsg("You have already Subscribed !");
    }
} else {
    UIMsg::genericError();
}
