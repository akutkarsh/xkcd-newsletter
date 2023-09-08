<?php
namespace Akut;

require_once __DIR__ . '/api.php';

class Mailer {
    private function getServerAddr() {


        //getting server address from the env file
        if (getenv("xkcd_site_name")){
            $url = "http://".getenv("xkcd_site_name")."/";
            return $url;
        }

        //for local deployment
        if (isset($_SERVER['LANDO_INFO'])) {
            $local_address = json_decode($_SERVER['LANDO_INFO']);
            $local_address = $local_address->appserver_nginx->urls;
            return $local_address[2];
        }

        //for live server
        $protocol =
            (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ||
            (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] === 443)
                ? 'https'
                : 'http';
        $server_name = isset($_SERVER['SERVER_NAME'])
            ? $_SERVER['SERVER_NAME']
            : getenv('DOMAIN_NAME');
        $complete_addr = $protocol . '://' . $server_name . '/';
        return $complete_addr;
    }

    // creates validation link using token
    // send validation mail to email then send it ; return true if no error in mail sending
    public function sendValidationMail($email, $key) {
        $server_addr = $this->getServerAddr();
        $validationURL = $server_addr . 'validate.php?q=' . $key;
        $subject = 'Confirm your XKCD newsletter sign up! 🎉';

        $from = 'rtcampakut@gmail.com';
        $fromName = 'rtcampakut';

        $headers = "From: {$fromName}" . ' <' . $from . '>';

        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        $replace_array = array(
            '{$css}' => file_get_contents(__DIR__."/../views/validationMail.css"),
            '{$validationURL}' => $validationURL,
        );

        $htmlContent = file_get_contents(__DIR__ . "/../views/validationMail.html");
        $htmlContent = strtr($htmlContent, $replace_array);

        $mail = mail($email, $subject, $htmlContent, $headers);

        if ($mail) {
            return true;
        } else {
            return false;
        }
    }

    public function sendNotification($msg) {
        //to implement new feature
    }

    // get comic information using xkcd api
    // then create unsubscibe link
    // then send mail ; return true if mail sended
    public function sendComic($email,$token) {

        $server_addr = $this->getServerAddr();
        $unsub_url = $server_addr . 'unsubscribe.php?q=' . $token;
        $api = new Api();
        $image_filesize = $api->getImageFilesize();
        $xkcd_title = $api->getTitle();
        $xkcd_imgLink = $api->getImageLink();
        $xkcd_description = $api->getDescription();
        $xkcd_image = $api->getImage();

        $to = $email;
        $from = 'akutkarsh@gmail.com';
        $fromName = 'Utkarsh';

        $subject = $api->getSafetitle();

        $replace_array = array(
            '{$xkcd_imgLink}' => $xkcd_imgLink,
            '{$css}' => file_get_contents(__DIR__."/../views/comicMail.css"),
            '{$xkcd_description}' => $xkcd_description,
            '{$xkcd_title}' => $xkcd_title,
            '{$unsubscribeLink}' => $unsub_url,
        );

        $htmlContent = file_get_contents(__DIR__ . "/../views/comicMail.html");
        $htmlContent = strtr($htmlContent, $replace_array);

        $headers = "From: {$fromName}" . ' <' . $from . '>';

        $semi_rand = md5(time());
        $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

        $headers .=
            "\nMIME-Version: 1.0\n" .
            "Content-Type: multipart/mixed;\n" .
            " boundary=\"{$mime_boundary}\"";

        $message =
            "--{$mime_boundary}\n" .
            "Content-Type: text/html; charset=\"UTF-8\"\n" .
            "Content-Transfer-Encoding: 8bit\n\n" .
            $htmlContent .
            "\n\n";

        $message .= "--{$mime_boundary}\n";

        $file = 'xkcd.png';
        $data = chunk_split($xkcd_image);
        $message .=
            "Content-Type: {\"application/octet-stream\"};\n" .
            " name=\"" .
            $file .
            "\"\n" .
            'Content-Description: ' .
            basename($file) .
            "\n" .
            "Content-Disposition: attachment;\n" .
            " filename=\"" .
            basename($file) .
            "\"; size=" .
            $image_filesize .
            ";\n" .
            "Content-Transfer-Encoding: base64\n\n" .
            $data .
            "\n\n";
        $message .= "--{$mime_boundary}--";
        $returnpath = '-f' . $from;
        $mail = mail($to, $subject, $message, $headers, $returnpath);
        if (!$mail) {
            return false;
        } else {
            return true;
        }
    }
}
