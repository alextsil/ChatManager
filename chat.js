var onTransaction = false;
var state;
var chatarea;
var convID;

function Chat(appendingarea, conversationID) {
    chatarea = appendingarea;
    convID = conversationID;
    this.update = updateChat;
    this.send = sendChat;
    this.getState = getStateOfChat;
}

function getStateOfChat() {
    if (!onTransaction) {
        onTransaction = true;
        $.ajax({
            'type': "POST",
            'url': "chatHandler.php",
            data: {
                'event': 'getState',
                'convID': convID
            },
            dataType: "json",
            success: function(data) {
                state = data.state;
                onTransaction = false;
            }
        });
    }
}

function updateChat() {
    if (!onTransaction) {
        onTransaction = true;
        $.ajax({
            type: "POST",
            url: "chatHandler.php",
            data: {
                'event': 'update',
                'state': state,
                'convID': convID
            },
            dataType: "json",
            success: function(data) {
                if (data.text) {
                    for (var i = 0; i < data.text.length; i++) {
                        $("#" + chatarea).append(("<p>" + data.text[i] + "</p>"));
                    }
                    var chatAreaElement = document.getElementById(chatarea);
                    chatAreaElement.scrollTop = chatAreaElement.scrollHeight;
                }
                onTransaction = false;
                state = data.state;
            }
        });
    }
    else {
        setTimeout(updateChat, 1500);
    }
}

function sendChat(message, nickname) {
    $.ajax({
        type: "POST",
        url: "chatHandler.php",
        data: {
            'event': 'send',
            'message': message,
            'nickname': nickname,
            'convID': convID
        },
        dataType: "json",
        success: function() {
            updateChat();
        }
    });
}


