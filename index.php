<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Chat Manager</title>
        <link rel="stylesheet" href="style.css" type="text/css" />

        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
        <script type="text/javascript" src="chat.js"></script>
        <script type="text/javascript" src="users.js"></script>
        <script type="text/javascript">

            var nickname = prompt("Enter nickname:", "Guest");
            if (!nickname || nickname === ' ' || nickname == "null") {
                nickname = "Guest";
            }
            nickname = nickname.replace(/(<([^>]+)>)/ig, "");
            var pmWindows = new Array();

            function openNewPMwindow(receiverNickname) {
                if (nickname !== receiverNickname) //conversation is unique
                {
                    if (pmWindows[receiverNickname] == null)
                    {
                        var convID;
                        //start pm session
                        $.ajax({
                            type: "POST",
                            url: "chatHandler.php",
                            data: {
                                'event': 'chatSession',
                                'user': nickname,
                                'peer': receiverNickname
                            },
                            dataType: "json",
                            success: function(data) {
                                convID = data.convID;
                            },
                            async: false
                        });
                        var title = "pm #" + receiverNickname;
                        var url = "pm.php?id=" + receiverNickname + "&name=" + nickname + "&convid=" + convID;

                        //open popup window
                        pmWindows[receiverNickname] = window.open(url, title, 'width=600,height=600');
                        pmWindows[receiverNickname].onbeforeunload = function() {
                            $.ajax({
                                type: "POST",
                                url: "chatHandler.php",
                                data: {
                                    'event': 'removeMessages',
                                    'convID': convID
                                },
                                dataType: "json",
                                async: false
                            });
                            pmWindows[receiverNickname] = null;
                        };
                    }
                }
            }

            var users = new Users();
            //add user to db
            $.ajax({
                type: "POST",
                url: "chatHandler.php",
                data: {
                    'event': 'insertuser',
                    'username': nickname
                },
                dataType: "json",
                success: function(data) {
                    nickname = data.userName;
                    setInterval('users.displayUsers()', 1000);

                },
                async: false
            });

            function setTitle()
            {
                document.getElementById("name-area").innerHTML = "Welcome to ChatManager " + nickname;
            }
            //check for pms
            function checkforPMs()
            {
                $.ajax({
                    type: "POST",
                    url: "chatHandler.php",
                    data: {
                        'event': 'hasNewPM',
                        'userName': nickname
                    },
                    dataType: "json",
                    success: function(data) {
                        if (data.userName != "0")
                        {
                            for (var i = 0; i < data.userName[i].length; i++)
                            {
                                openNewPMwindow(data.userName[i]);
                            }
                        }
                    }
                });
            }

            function checkPMwindowsActivity()
            {
                for (var receiverNickname in pmWindows)
                {
                    if (pmWindows[receiverNickname] != null)
                    {
                        $.ajax({
                            type: "POST",
                            url: "chatHandler.php",
                            data: {
                                'event': 'checkWindowActivity',
                                'userName': nickname,
                                'peer': receiverNickname
                            },
                            dataType: "json",
                            success: function(data) {
                                if (data.exists == "0")
                                {
                                    pmWindows[data.key].close();
                                    pmWindows[data.key] = null;
                                }
                            }
                        });
                    }
                }
            }

            setInterval('checkforPMs()', 1500);
            setInterval('checkPMwindowsActivity()', 1000);
        </script>

        <script type="text/javascript">
            var chat = new Chat("chat-area", "1");

            $(function() {
                chat.getState();
                $("#sendie").keydown(function(event) {
                    var key = event.which;
                    if (key >= 33) {
                        var maxLength = $(this).attr("maxlength");
                        var length = $(this).val().length;
                        if (length >= maxLength) {
                            event.preventDefault();
                        }
                    }
                });

                $('#sendie').keyup(function(event) {
                    if (event.keyCode === 13) {
                        var text = $(this).val();
                        var maxLength = $(this).attr("maxlength");
                        var length = text.length;
                        if (length <= maxLength + 1) {
                            chat.send(text, nickname);
                            $(this).val("");
                        } else {
                            $(this).val(text.substring(0, maxLength));
                        }
                    }
                });
            });

            window.onbeforeunload = function() {
                users.goneOffline(nickname);
            };
        </script>
    </head>
    <body onload="setInterval('chat.update()', 1000), setTitle()">
        <div id="page-wrap">
            <h2 id="name-area"></h2>
            <div id="users-area" /> </div>
        <div id="chat-wrap">
            <div id="chat-area" /> </div> 
        <form id="send-message-area">
            <textarea style="resize:none" placeholder="Your message" id="sendie" maxlength="100"></textarea>
        </form>
    </body>
</html>