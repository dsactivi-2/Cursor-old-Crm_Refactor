//callNumberOfNotifications Start
function callNumberOfNotifications() {
    $.ajax({
        url: "get.php?case=getNumberOfNotifications",
        success: (function (result) {
            $(".getNumberOfNotifications").html(result);
        })
    })
};

callNumberOfNotifications();
setInterval(callNumberOfNotifications, (2 * 1000));
//callNumberOfNotifications End

//callNotifications_20 Start
function callNotifications_20() {
    $.ajax({
        url: "get.php?case=getNotifications_20",
        success: (function (result) {
            $(".getNotifications_20").html(result);
        })
    })
};

callNotifications_20();
setInterval(callNotifications_20, (2 * 1000));
//callNotifications_20 End

function notifications_mark_read() {
    callNumberOfNotifications();
    callNotifications_20();
}

//callNumberOfMessages Start
function callNumberOfMessages() {
    $.ajax({
        url: "get.php?case=getNumberOfMessages",
        success: (function (result) {
            $(".getNumberOfMessages").html(result);
        })
    })
};

callNumberOfMessages();
setInterval(callNumberOfMessages, (2 * 1000));
//callNumberOfMessages End

//callMessages_10 Start
function callMessages_10() {
    $.ajax({
        url: "get.php?case=getMessages_10",
        success: (function (result) {
            $(".getMessages_10").html(result);
        })
    })
};

callMessages_10();
setInterval(callMessages_10, (2 * 1000));
//callMessages_10 End
