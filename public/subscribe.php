<?php
require_once __DIR__ . "/class/database.php";
require_once __DIR__ . "/class/mailer.php";
require_once __DIR__ . "/class/UIMsg.php";


if (isset($_POST["emailid"])) {

    $email = filter_var($_POST["emailid"], FILTER_SANITIZE_EMAIL);

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $db = new Akut\Database();
        $adduserResult = $db->addUser($email);

        if ($adduserResult) {
            // get validation key
            // then sendValidation key using mailer class instance
            $key = $db->getValidationKey($email);

            if ($key == "") {
                // exit some error occured
                UIMsg::genericError();
            }
            $mailer = new Akut\Mailer();
            $sendmailvalidationResult = $mailer->sendValidationMail(
                $email,
                $key
            );

            if ($sendmailvalidationResult == true) {
                //done check you mail
                UIMsg::specificMsg('Almost done, you\'re so close! 🤏 Please check your inbox for a confirmation email!');

            } else {
                //genericerror
                UIMsg::genericError();
            }
        } else {
            // genericerror
            UIMsg::genericError();
        }
    } else {
        UIMsg::specificMsg("Email not valid.");
    }
} else {
    //generic error
    UIMsg::genericError();
}
