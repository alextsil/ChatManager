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

            var name = prompt("Enter username:", "Guest");
            if (!name || name === ' ' || name == "null") {
                name = "Guest";
            }

            name = name.replace(/(<([^>]+)>)/ig, "");

            var pmWindows = new Array();

            function openNewWindow(userName) {
                //var title = "pm #" + userName;
                //var url = "pm.php?id=" + userName + "&name=" + name;

                if (name !== userName) //&* conversation is unique
                {
                    if (pmWindows[userName] == null)
                    {
                        var convID;
                        //start pm session
                        $.ajax({
                            type: "POST",
                            url: "process.php",
                            data: {
                                'function': 'chatSession',
                                'user': name,
                                'peer': userName
                            },
                            dataType: "json",
                            success: function(data) {
                                convID = data.convID;
                            },
                            async: false
                        });
                        var title = "pm #" + userName;
                        var url = "pm.php?id=" + userName + "&name=" + name + "&convid=" + convID;

                        pmWindows[userName] = window.open(url, title, 'width=600,height=600');
                        pmWindows[userName].onbeforeunload = function() {
                            pmWindows[userName] = null;
                            $.ajax({
                                type: "POST",
                                url: "process.php",
                                data: {
                                    'function': 'removeMessages',
                                    'convID': convID
                                },
                                dataType: "json",
                                async: false
                            });
                        }
                    }
                }
            }

            //add user to db
            $.ajax({
                type: "POST",
                url: "process.php",
                data: {
                    'function': 'insertuser',
                    'username': name
                },
                dataType: "json",
                success: function(data) {
                    name = data.userName;
                    setInterval('users.displayUsers()', 1000);
                    //fixme
                    document.write("<h1>Hello, " + name + "</h1>");
                },
                async: false
            });

            //check for pms
            function checkforPMs()
            {
                $.ajax({
                    type: "POST",
                    url: "process.php",
                    data: {
                        'function': 'hasNewPM',
                        'userName': name
                    },
                    dataType: "json",
                    success: function(data) {
                        //$("#chat-area").append(("<p>dsadas</p>"));
                        if (data.userName != "0")
                        {
                            for (var i = 0; i < data.userName[i].length; i++)
                                openNewWindow(data.userName[i]);
                        }
                    }
                });
            }
            setInterval('checkforPMs()', 1500);

            function checkWindowActivity()
            {
                for (var key in pmWindows)
                {
                    if (pmWindows[key] != null)
                    {
                        //$("#chat-area").append(("<p>open windows: " + key + "</p>"));
                        $.ajax({
                            type: "POST",
                            url: "process.php",
                            data: {
                                'function': 'checkWindowActivity',
                                'userName': name,
                                'peer': key
                            },
                            dataType: "json",
                            success: function(data) {
                                if (data.exists == "0")
                                {
                                    pmWindows[data.key].close();
                                    pmWindows[data.key] = null;
                                }
                            },
                        });
                    }
                }
            }
            setInterval('checkWindowActivity()', 1000);

            var chat = new Chat("chat-area", "1");
            var users = new Users();

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
                            chat.send(text, name);
                            $(this).val("");
                        } else {
                            $(this).val(text.substring(0, maxLength));
                        }
                    }
                });
            });
        </script>

        <script type="text/javascript">
            window.onbeforeunload = function() {
                users.goneOffline(name);
            };
        </script>

    </head>
    <body onload="setInterval('chat.update()', 1000)" background="images/bg.png">
        <div id="page-wrap">
            <h2>Chat Manager - Public</h2>
            <div id="users-area" /> </div>
        <div id="chat-wrap">
            <div id="chat-area" /> </div> 
        <form id="send-message-area">
            <textarea style="resize:none" placeholder="Your message" id="sendie" maxlength="100"></textarea>
        </form>
    </body>
</html>