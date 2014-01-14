<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" href="style.css" type="text/css" />
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
        <script type="text/javascript" src="chat.js"></script>
        <script type="text/javascript">
            var name = <?php
$title = urldecode($_GET["name"]);
echo json_encode($title);
?>;
            var otherName = <?php
$title = urldecode($_GET["id"]);
echo json_encode($title);
?>;
            var convID = <?php
$title = urldecode($_GET["convid"]);
echo json_encode($title);
?>;


            var chat = new Chat(name, convID);

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
    </head>

    <body onload="setInterval('chat.update()', 1000)" background="images/bg.png">
        <div id="page-wrap">
            <?php
            $title = urldecode($_GET["id"]);
            echo "<h2>" . $title . "</h2>";
            ?>
            <div class="pmchat-wrap" >
                <?php
                $title = urldecode($_GET["name"]);
                echo "<div class='pmchat-area' id='" . $title . "'/> </div>";
                ?>
                <form id="send-message-area">
                    <textarea style="resize:none" placeholder="Your message" id="sendie" maxlength="100"></textarea>
                </form>
            </div>
    </body>

</html>