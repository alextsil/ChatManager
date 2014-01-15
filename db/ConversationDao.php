<?php

require_once 'db/connectToDb.php';

class ConversationDao {

    //get conversation by id
    //@Returns 2-D array
    public function getMessages($inConvId) {
        $Conn = new connectToDb();
        $MyConn = $Conn->connect();
        $stmt = $MyConn->prepare("SELECT users.username, messages.text FROM chatmanager.messages,chatmanager.users 
					where users.id=messages.sender_id and conversation_id=? order by messages.id");
        $stmt->bind_param("i", $inConvId);
        $stmt->execute();
        $stmt->bind_result($senName, $txt);

        $resArr = array();
        while ($stmt->fetch()) {
            $singleLine = "<span class='user'>" . $senName . "</span>  " . $txt;
            array_push($resArr, $singleLine);
        }
        $stmt->close();
        $MyConn->close();
        return $resArr;
    }

    //den epistrefei success h fail
    public function setMessage($inUser, $inMsg, $inConvId) {
        $userId = $this->getUserIdFromName($inUser);

        $Conn = new connectToDb();
        $MyConn = $Conn->connect();

        $stmt = $MyConn->prepare("insert into messages(conversation_id, sender_id, text, datetime) 
				values (?, ?, ?, current_timestamp)");
        $stmt->bind_param("iis", $inConvId, $userId, $inMsg);
        $stmt->execute();

        $stmt->close();
        $MyConn->close();
    }

    public function getUserIdFromName($inUsername) {
        $Conn = new connectToDb();
        $MyConn = $Conn->connect();
        $stmt = $MyConn->prepare("select users.id from chatmanager.users where users.username=?");
        $stmt->bind_param("s", $inUsername);
        $stmt->execute();
        $stmt->bind_result($uId);
        $stmt->fetch();
        $stmt->close();
        $MyConn->close();
        return $uId;
    }

    public function getUsernameFromId($inUserId) {
        $Conn = new connectToDb();
        $MyConn = $Conn->connect();
        $stmt = $MyConn->prepare("select users.username from chatmanager.users where users.id=?");
        $stmt->bind_param("i", $inUserId);
        $stmt->execute();
        $stmt->bind_result($uName);
        $stmt->fetch();
        $stmt->close();
        $MyConn->close();
        return $uName;
    }

    //count messages by conversation id
    public function countMessages($inConvId) {
        $Conn = new connectToDb();
        $MyConn = $Conn->connect();
        $stmt = $MyConn->prepare("select count(*) from chatmanager.messages where conversation_id=?");
        $stmt->bind_param("i", $inConvId);
        $stmt->execute();
        $stmt->bind_result($msgCount);
        $stmt->fetch();
        $stmt->close();
        $MyConn->close();
        return $msgCount;
    }

    public function insertUser($inUser) {
        $Conn = new connectToDb();
        $MyConn = $Conn->connect();

        $stmt = $MyConn->prepare("insert into users(username) values (?)");
        $stmt->bind_param("s", $inUser);
        $stmt->execute();

        $stmt->close();
        $MyConn->close();
    }

    public function getAllUsers() {
        $Conn = new connectToDb();
        $MyConn = $Conn->connect();
        $stmt = $MyConn->prepare("SELECT * FROM chatmanager.users");
        $stmt->execute();
        $stmt->bind_result($uId, $uName);

        $resArr = array();
        $singleUserArray = array();
        while ($stmt->fetch()) {
            $singleUserArray['userID'] = $uId;
            $singleUserArray['userName'] = $uName;
            array_push($resArr, $singleUserArray);
        }
        $stmt->close();
        $MyConn->close();
        return $resArr;
    }

    public function countUsers() {
        $Conn = new connectToDb();
        $MyConn = $Conn->connect();
        $stmt = $MyConn->prepare("select count(*) from chatmanager.users");
        $stmt->execute();
        $stmt->bind_result($userCount);
        $stmt->fetch();
        $stmt->close();
        $MyConn->close();
        return $userCount;
    }

    //HACK : na svinei kai to conversation
    //afairei tous users KAI ta messages tou
    //@param username
    public function removeUser($inUser) {
        $userId = $this->getUserIdFromName($inUser);

        $Conn = new connectToDb();
        $MyConn = $Conn->connect();
        //svinei messages tou user
        $stmt = $MyConn->prepare("DELETE FROM chatmanager.messages where sender_id=?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        //ksekleidonei to foreign key
        $stmt = $MyConn->prepare("SET foreign_key_checks = 0");
        $stmt->execute();
        //svinei ta pms tou user PRIN svisei ton user
        $stmt = $MyConn->prepare("DELETE FROM chatmanager.pms where sender_id=? OR receiver_id=?");
        $stmt->bind_param("ii", $userId, $userId);
        $stmt->execute();
        //svinei ton user
        $stmt = $MyConn->prepare("DELETE FROM users where id=?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        //kleidonei to foreign key
        $stmt = $MyConn->prepare("SET foreign_key_checks = 1");
        $stmt->execute();
        echo "etrekse";
        $stmt->close();
        $MyConn->close();
    }

    public function isUserUnique($inUser) {
        $userId = $this->getUserIdFromName($inUser);
        if ($userId) {
            return 1;
        } else {
            return 0;
        }
    }

    //TODO : check an thelei $stmt->close meta apo kathe statement.
    //vazei sth vash tis times gia to pm metaksi twn 2 users
    //@Returns to neo conv. id -or- to hdh iparxon conv id
    public function startPmConversation($inUser1, $inUser2) {
        //pernei ta id twn 2 users
        $user1Id = $this->getUserIdFromName($inUser1);
        $user2Id = $this->getUserIdFromName($inUser2);

        $Conn = new connectToDb();
        $MyConn = $Conn->connect();
        //checkarei an hdh iparxei pm metaksi twn 2 users kai return to conv id
        $stmt = $MyConn->prepare("select conversation_id from pms 
    			where (sender_id=? and receiver_id=?) or (sender_id=? and receiver_id=?)");
        $stmt->bind_param("iiii", $user1Id, $user2Id, $user2Id, $user1Id);
        $stmt->execute();
        $stmt->bind_result($existsConvId);
        $stmt->fetch();
        if ($existsConvId != 0) {
            return $existsConvId;
        }

        //an dn iparxei conv metaksi twn 2 users, ftiakse.
        //vazei new conv kai pernei to generated id stin metavlith $newConvId
        $stmt = $MyConn->prepare("insert into conversations(type) values(2)");
        $stmt->execute();
        $newConvId = $stmt->insert_id;

        $Conn = new connectToDb();
        $MyConn = $Conn->connect();

        //vazei ston pinaka pms to $newConvId kai ta id twn 2 users.
        $stmt = $MyConn->prepare("insert into pms(conversation_id, sender_id, receiver_id)
    			 values(?,?,?)");
        $stmt->bind_param("iii", $newConvId, $user1Id, $user2Id);
        $stmt->execute();

        $stmt->close();
        $MyConn->close();
        return $newConvId;
    }

    public function isTherePmAmongUsers($inUser1, $inUser2) {
        //pernei ta id twn 2 users
        $user1Id = $this->getUserIdFromName($inUser1);
        $user2Id = $this->getUserIdFromName($inUser2);

        $Conn = new connectToDb();
        $MyConn = $Conn->connect();
        //checkarei an hdh iparxei pm metaksi twn 2 users kai return 1 h 0 an dn iparxei
        $stmt = $MyConn->prepare("select conversation_id from pms
       where (sender_id=? and receiver_id=?) or (sender_id=? and receiver_id=?)");
        $stmt->bind_param("iiii", $user1Id, $user2Id, $user2Id, $user1Id);
        $stmt->execute();
        $stmt->bind_result($existsConvId);
        $stmt->fetch();
        if ($existsConvId != 0) {
            return 1;
        } else {
            return 0;
        }
    }

    //mou les to username sou kai sou stelnw array me poious milas (ta usernames tous)
    public function getPendingConversation($inUser) {
        $userId = $this->getUserIdFromName($inUser);

        $Conn = new connectToDb();
        $MyConn = $Conn->connect();

        $stmt = $MyConn->prepare("select sender_id, receiver_id from pms 
    			where (sender_id=? or receiver_id=?)");
        $stmt->bind_param("ii", $userId, $userId);
        $stmt->execute();
        $stmt->bind_result($senderId, $receiverId);

        $senderReceiverIdsArray = array();
        $senderReceiverIds = array();
        while ($stmt->fetch()) {
            $senderReceiverIds['senderId'] = $senderId;
            $senderReceiverIds['receiverId'] = $receiverId;
            array_push($senderReceiverIdsArray, $senderReceiverIds);
        }
        $stmt->close();
        $MyConn->close();

        //ws edw exw ton 2D pinaka me ta ids. prepei na vrw poia stilh einai o $inUser kai na tin afairesw

        if (!$senderReceiverIdsArray) {
            return 0;
        }
        $openPmsUserId = array();
        //An eimai egw o sender, dwse mou ta receiver ids
        if ($senderReceiverIdsArray[0]['senderId'] == $userId) {
            foreach ($senderReceiverIdsArray as $singlePm) {
                array_push($openPmsUserId, $singlePm['receiverId']);
            }
        } else {
            foreach ($senderReceiverIdsArray as $singlePm) {
                array_push($openPmsUserId, $singlePm['senderId']);
            }
        }

        //o $openPmsUserId exei ta ids twn user pou exw pm anoixta.
        //apo ta IDs pernw ta usernames
        $openPmsUsernames = array();
        foreach ($openPmsUserId as $singleUserId) {
            $singleUsername = $this->getUsernameFromId($singleUserId);
            array_push($openPmsUsernames, $singleUsername);
        }

        return $openPmsUsernames;
    }

    public function deletePmsByConversationId($inConvId) {
        $Conn = new connectToDb();
        $MyConn = $Conn->connect();
        $stmt = $MyConn->prepare("DELETE FROM chatmanager.pms where conversation_id=?");
        $stmt->bind_param("i", $inConvId);
        $stmt->execute();

        $stmt->close();
        $MyConn->close();
    }

}



?>