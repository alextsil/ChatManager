
var instanse = false;
var state;


function Chat() {
    this.update = updateChat;
    this.send = sendChat;
    this.getState = getStateOfChat;
    this.displayUsers = displayOnlineUsers;
}


function getStateOfChat() {
    if (!instanse) {
        instanse = true;
        $.ajax({
            type: "POST",
            url: "process.php",
            data: {
                'function': 'getState'
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
                'state': state
            },
            dataType: "json",
            success: function(data) {
                if (data.text) {
                    for (var i = 0; i < data.text.length; i++) {
                        $('#chat-area').append(("<p>" + data.text[i] + "</p>"));

                    }
                    var chatArea = document.getElementById('chat-area');
                    chatArea.scrollTop = chatArea.scrollHeight;
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
            'nickname': nickname
        },
        dataType: "json",
        success: function() {
            updateChat();
        }
    });
}


