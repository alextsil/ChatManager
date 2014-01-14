<?php

require_once 'db/ConversationDao.php';

$function = $_POST['function'];

$log = array();
$conversation = new ConversationDao();

switch ($function) {
    case('getState'):
        $convID = $_POST['convID'];
        $log['state'] = $conversation->countMessages($convID);
        break;

    case('update'):
        $state = $_POST['state'];
        $convID = htmlentities(strip_tags($_POST['convID']));
        $count = $conversation->countMessages($convID);
        $log['state'] = $count;
        if ($state == $count) {
            $log['text'] = false;
        } else {
            $lines = $conversation->getMessages($convID);
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
        $convID = htmlentities(strip_tags($_POST['convID']));
        if (($message) != "\n") {
            if (preg_match($reg_exUrl, $message, $url)) {
                $message = preg_replace($reg_exUrl, '<a href="' . $url[0] . '" target="_blank">' . $url[0] . '</a>', $message);
            }
            $conversation->setMessage($nickname, $message, $convID);
        }
        break;

    case('insertuser'):
        $name = $_POST['username'];
        $counter = 0;
        $tempName = $name;
        while ($conversation->isUserUnique($tempName) == 1) {
            $counter++;
            $tempName = $name;
            $tempName = $tempName . $counter;
        }
        $name = $tempName;

        $conversation->insertUser($name);
        $log['userName'] = $name;
        break;

    case('displayUsers'):
        $onlineUsers = $_POST['onlineUsers'];
        $countUsers = $conversation->countUsers();
        $log['onlineUsers'] = $countUsers;

        if ($onlineUsers == $countUsers) {
            $log['users'] = false;
        } else {
            $log['users'] = $conversation->getAllUsers();
        }
        break;

    case('userHasLeft'):
        $conversation->removeUser($_POST['userName']);
        break;

    case('chatSession'):
        $convID = $conversation->startPmConversation($_POST['user'], $_POST['peer']);
        $log['convID'] = $convID;
        break;

    case('hasNewPM'):
        $log['userName'] = $conversation->getPendingConversation($_POST['userName']);
        break;

    case('removeMessages'):
        $conversation->deletePmsByConversationId($_POST['convID']);
        break;

    case('checkWindowActivity'):
        $log['exists'] = $conversation->isTherePmAmongUsers($_POST['userName'], $_POST['peer']);
        $log['key']=$_POST['peer'];
        break;
}

print json_encode($log);
?>