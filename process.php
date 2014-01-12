<?php

require_once 'db/ConversationDao.php';

$function = $_POST['function'];

$log = array();
$conversation = new ConversationDao();

switch ($function) {
    case('getState'):
        $log['state'] = $conversation->countPublicMessages();
        break;
    case('update'):
        $state = $_POST['state'];
        $count = $conversation->countPublicMessages();
        if ($state == $count) {
            $log['state'] = $state;
            $log['text'] = false;
        } else {
            $lines = $conversation->getPublicMessages();
            $text = array();
            $log['state'] = count($lines);
            foreach ($lines as $line_num => $line) {
                if ($line_num >= $state) {
                    $text[] = $line = str_replace("\n", "", $line);
                }
            }
            $log['text'] = $text;
        }
        break;
    case('send'):
        $nickname = htmlentities(strip_tags($_POST['nickname']));
        $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
        $message = htmlentities(strip_tags($_POST['message']));
        if (($message) != "\n") {
            if (preg_match($reg_exUrl, $message, $url)) {
                $message = preg_replace($reg_exUrl, '<a href="' . $url[0] . '" target="_blank">' . $url[0] . '</a>', $message);
            }
            $conversation->setPublicMessage($nickname, $message);
        }
        break;
    case('insertuser'):
        $insertUser = $conversation->insertUser($_POST['username']);
        break;
}
echo json_encode($log);
?>