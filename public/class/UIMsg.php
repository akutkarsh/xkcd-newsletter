<?php
class UIMsg {

    //shows error msg "You should retry !" in html page
    public static function genericError() {
        $html = file_get_contents(__DIR__ . '/../views/genericError.html');
        echo $html;
    }

    //shows specific msg  in html page
    public static function specificMsg($msg) {
        $replace_array = array(
            '{$msg}' => $msg,
        );

        $html = file_get_contents(__DIR__ . '/../views/specificMsg.html');
        $html = strtr($html, $replace_array);
        echo $html;
    }

    //shows unsubscribe msg  in html page
    public static function unsubscribeMsg() {
        $html = file_get_contents(__DIR__ . '/../views/unsubscribeMsg.html');
        echo $html;
    }
}
