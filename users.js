var onlineUsers = 0;

function Users() {
    this.displayUsers = displayOnlineUsers;
    this.goneOffline = hasGoneOffline;
}

function displayOnlineUsers()
{
    $.ajax({
        type: "POST",
        url: "chatHandler.php",
        data: {
            'event': 'displayUsers',
            'onlineUsers': onlineUsers
        },
        dataType: "json",
        success: function(data) {
            if (data.users) {
                $('#users-area').text('');
                onlineUsers = data.onlineUsers;
                for (var i = 0; i < data.users.length; i++) {
                    $('#users-area').append("<span>" + "<a href =\"javascript:openNewPMwindow('"+ data.users[i]['userName']+"');\">"+ data.users[i]['userName'] + "</a></span> ");
                }
            }
        }
    });
}

function hasGoneOffline(userName)
{
    $.ajax({
        type: "POST",
        url: "chatHandler.php",
        data: {
            'event': 'userHasLeft',
            'userName': userName
        },
        dataType: "json",
        async:false
    });
}
