<?php
require_once __DIR__ . '/public/class/mailer.php';
require_once __DIR__ . '/public/class/database.php';

$db = new Akut\Database();

// subscribers emails
$subscribedEmailsData = $db->getSubscribedEmails();

foreach ($subscribedEmailsData as $row) {
    $email = $row['email'];
    $token = $row['subKey'];

    $started_at = date('Y-m-d H:i:s');

    $mailObj = new Akut\Mailer();
    $mailStatus = $mailObj->sendComic($email, $token);

    if ($mailStatus) {
        $db->cronSuccess($email);
    } else {
        $db->cronFailed($email);
    }
}
