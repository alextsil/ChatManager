<?php

require_once 'db/ConversationDao.php';
$conversationDAO = new ConversationDao();

$respond = array();

$event = $_POST['event'];
switch ($event)
{
    case('getState'):
        $convID = $_POST['convID'];
        $respond['state'] = $conversationDAO->countMessages($convID);
        break;
    
    case('update'):
        $state = $_POST['state'];
        $convID = htmlentities(strip_tags($_POST['convID']));
        $count = $conversationDAO->countMessages($convID);
        $respond['state'] = $count;
        if ($state == $count)
        {
            $respond['text'] = false;
        }
        else
        {
            $lines = $conversationDAO->getMessages($convID);
            $text = array();
            $respond['state'] = count($lines);
            foreach ($lines as $line_num => $line)
            {
                if ($line_num >= $state)
                {
                    $text[] = $line;
                }
            }
            $respond['text'] = $text;
        }
        break;

    case('send'):
        $nickname = htmlentities(strip_tags($_POST['nickname']));
        $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
        $message = htmlentities(strip_tags($_POST['message']));
        $convID = htmlentities(strip_tags($_POST['convID']));
        if (($message) != "\n")
        {
            if (preg_match($reg_exUrl, $message, $url))
            {
                $message = preg_replace($reg_exUrl, '<a href="' . $url[0] . '" target="_blank">' . $url[0] . '</a>', $message);
            }
            $conversationDAO->setMessage($nickname, $message, $convID);
        }
        break;

    case('insertuser'):
        $name = $_POST['username'];
        $counter = 0;
        $tempName = $name;
        while ($conversationDAO->isUserUnique($tempName) == 1)
        {
            $counter++;
            $tempName = $name;
            $tempName = $tempName . $counter;
        }
        $name = $tempName;

        $conversationDAO->insertUser($name);
        $respond['userName'] = $name;
        break;

    case('displayUsers'):
        $onlineUsers = $_POST['onlineUsers'];
        $countUsers = $conversationDAO->countUsers();
        $respond['onlineUsers'] = $countUsers;

        if ($onlineUsers == $countUsers)
        {
            $respond['users'] = false;
        }
        else
        {
            $respond['users'] = $conversationDAO->getAllUsers();
        }
        break;

    case('userHasLeft'):
        $conversationDAO->removeUser($_POST['userName']);
        break;

    case('chatSession'):
        $convID = $conversationDAO->startPmConversation($_POST['user'], $_POST['peer']);
        $respond['convID'] = $convID;
        break;

    case('hasNewPM'):
        $respond['userName'] = $conversationDAO->getPendingConversation($_POST['userName']);
        break;

    case('removeMessages'):
        $conversationDAO->deletePmsByConversationId($_POST['convID']);
        break;

    case('checkWindowActivity'):
        $respond['exists'] = $conversationDAO->isTherePmAmongUsers($_POST['userName'], $_POST['peer']);
        $respond['key'] = $_POST['peer'];
        break;
}
print json_encode($respond);
?>