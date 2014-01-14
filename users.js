var onlineUsers = 0;

function Users() {
    this.displayUsers = displayOnlineUsers;
    this.goneOffline = hasGoneOffline;
}

function displayOnlineUsers()
{
    $.ajax({
        type: "POST",
        url: "process.php",
        data: {
            'function': 'displayUsers',
            'onlineUsers': onlineUsers
        },
        dataType: "json",
        success: function(data) {
            if (data.users) {
                $('#users-area').text('');
                onlineUsers = data.onlineUsers;
                for (var i = 0; i < data.users.length; i++) {
                     // $('#users-area').append("abc " + userIsUnique("Guest"));
                    $('#users-area').append("<span>" + "<a href =\"javascript:openNewWindow('"+ data.users[i]['userName']+"');\">"+ data.users[i]['userName'] + "</a></span> ");
                }
            }
        }
    });
}

function hasGoneOffline(userName)
{
    $.ajax({
        type: "POST",
        url: "process.php",
        data: {
            'function': 'userHasLeft',
            'userName': userName
        },
        dataType: "json",
        async:false
    });
}
