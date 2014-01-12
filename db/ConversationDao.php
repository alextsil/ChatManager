<?php

require_once 'db/connectToDb.php';

class ConversationDao {

    //get to conv me id=1 ara public msgs
    public function getPublicMessages() {
        $Conn = new connectToDb();
        $MyConn = $Conn->connect();
        $res = $MyConn->prepare("SELECT users.username, messages.text FROM chatmanager.messages,chatmanager.users 
					where users.id=messages.sender_id and conversation_id=1 order by messages.id");
        $res->execute();
        $res->bind_result($senName, $txt);

        $resArr = array();
        while ($res->fetch()) {
            $singleLine = "<span class='user'>" . $senName . "</span> : " . $txt;
            array_push($resArr, $singleLine);
        }
        $res->close();
        $MyConn->close();
        return $resArr;
    }

    public function setPublicMessage($inUser, $inMsg) {
        $userId = $this->getUserIdFromName($inUser);

        $Conn = new connectToDb();
        $MyConn = $Conn->connect();

        $stmt = $MyConn->prepare("insert into messages(conversation_id, sender_id, text, datetime) 
				values (1, ?, ?, current_timestamp)");
        $stmt->bind_param("is", $userId, $inMsg);
        $stmt->execute();

        $stmt->close();
        $MyConn->close();
        //den epistrefei success h fail
    }

    public function getUserIdFromName($inUsername) {
        $Conn = new connectToDb();
        $MyConn = $Conn->connect();
        $res = $MyConn->prepare("select users.id from chatmanager.users where users.username=?");
        $res->bind_param("s", $inUsername);
        $res->execute();
        $res->bind_result($uId);
        $res->fetch();
        $res->close();
        $MyConn->close();
        return $uId;
    }

    public function countPublicMessages() {
        $Conn = new connectToDb();
        $MyConn = $Conn->connect();
        $res = $MyConn->prepare("select count(*) from chatmanager.messages where conversation_id=1");
        $res->execute();
        $res->bind_result($msgCount);
        $res->fetch();
        $res->close();
        $MyConn->close();
        return $msgCount;
    }

    public function insertUser($inUser) {
        $Conn = new connectToDb();
        $MyConn = $Conn->connect();

        $stmt = $MyConn->prepare("insert into users(username)
     values (?)");
        $stmt->bind_param("s", $inUser);
        $stmt->execute();

        $stmt->close();
        $MyConn->close();
    }

}

?>