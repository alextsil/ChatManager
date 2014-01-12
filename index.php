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
            if (!name || name === ' ') {
                name = "Guest";
            }

            name = name.replace(/(<([^>]+)>)/ig, "");

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
                    //FIXME
                    $("#name-area").text("Hello, " + name);
                },
                async: false
            });

            var chat = new Chat();
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

        <script>
            window.onbeforeunload = function() {
                users.goneOffline(name);
            };
        </script>

    </head>
    <body onload="setInterval('chat.update()', 1000)" background="images/bg.png">
  
        <div id="page-wrap">
            <h2>Chat Manager</h2>
            <div id="users-area" /> </div>
            <div id="chat-wrap">
            <div id="chat-area" /> </div> 
            <form id="send-message-area">
                <textarea style="resize:none" placeholder="Your message" id="sendie" maxlength="100"></textarea>
            </form>
    </body>
</html>