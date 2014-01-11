<?php 
require_once 'db/connectToDb.php';
require_once 'model/message.php';
require_once 'model/conversation.php';
class conversationsMessagesDao {
	
	
	public function getConversationById($inConversationId) {
		$Conn = new connectToDb();
		$MyConn = $Conn->connect();
		$res = $MyConn->prepare("SELECT users.username, messages.text FROM chatmanager.messages,chatmanager.users 
					where users.id=messages.sender_id and conversation_id=? order by messages.id;");
		$res->bind_param("i", $inConversationId);
		$res->execute();
		$res->bind_result($senName,$txt);
		$retConv = new Conversation();
		$retConv->setId($inConversationId); //auto p zitise auto tha parei
		while ($res->fetch())
		{
			$tmpMsg = new Message();
			$tmpMsg->setSenderName($senName);
			$tmpMsg->setText($txt);
			$retConv->addMessage($tmpMsg);
			unset($tmpMsg);
		}
		$res->close();
		$MyConn->close();
		return $retConv;
	
	}
}
?>