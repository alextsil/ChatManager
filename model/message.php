<?php 

class message {
	//private $id;
	private $senderName;
	private $text;
	//private $datetime;



	public function getConversationId()
	{
		return $this->conversationId;
	}

	public function setConversationId($inConversationId)
	{
		$this->conversationId = $inConversationId;
	}

	public function getSenderName()
	{
		return $this->senderName;
	}

	public function setSenderName($inSenderName)
	{
		$this->senderName = $inSenderName;
	}

	public function getText()
	{
		return $this->text;
	}

	public function setText($inText)
	{
		$this->text = $inText;
	}
	
	public function getDatetime()
	{
		return $this->datetime;
	}
	
	public function setDatetime($inDatetime)
	{
		$this->datetime = $inDatetime;
	}
}

?>