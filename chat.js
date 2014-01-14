
var instanse = false;
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
    if (!instanse) {
        instanse = true;
        $.ajax({
            'type': "POST",
            'url': "process.php",
            data: {
                'function': 'getState',
                'convID': convID
            },
            dataType: "json",
            success: function(data) {
                state = data.state;
                instanse = false;
            }
        });
    }
}

function updateChat() {
    if (!instanse) {
        instanse = true;
        $.ajax({
            type: "POST",
            url: "process.php",
            data: {
                'function': 'update',
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
                instanse = false;
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
        url: "process.php",
        data: {
            'function': 'send',
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


