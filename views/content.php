<link type="text/css" rel="stylesheet" href="views/style/content.css" />
<div id="content">

<!-- Ta li sketa simenoun message. to h2 channel name -->
<ul id="chatBox">  
	<li><h2>Global</h2></li>
	
	<?php
	//testarw pws fenetai 
	include 'db/conversationsMessagesDao.php';
	
	$dao = new ConversationDao();
	$tmpConv = $dao->getConversationById(2);
	$tmpMsgArray = $tmpConv->getMessageList();
	foreach ($tmpMsgArray as $curMsg) {
		echo "<li><span class='user'>" . $curMsg->getSenderName() . "</span>" . " : " . $curMsg->getText();
	}
	?>
	
	<form action="index.php" method="post" id="messageForm">
    <input type="text" name="message" id="userTextBox">
    <p>Press enter to send!</p>
</form>
</ul>


</div>