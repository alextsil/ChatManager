<?php

class conversation {
	
	private $id;
	private $type; //1=channel 2=pm
	private $messageList = array(); //init
	
	
	public function getId()
	{
		return $this->Id;
	}
	
	public function setId($inId)
	{
		$this->id = $inId;
	}
	
	public function getType()
	{
		return $this->type;
	}
	
	public function setType($inType)
	{
		$this->type = $inType;
	}
	
	public function addMessage($inMessage) {
		array_push($this->messageList, $inMessage);
	}
	
	public function getMessageList() {
		return $this->messageList;
	}
	
	//TODO : remove message
}