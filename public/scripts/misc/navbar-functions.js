
$(()=>{
    $("#hamburger1").click(function(){

        $(".app-sidebar-panel").toggle();

    });

    // For Messages button...
    $('div#message-icon').click(()=>{
        $('.dropdown').toggleClass('active');
        $('span.drop-title').text('MESSAGES');
    });

    // For Notification button...
    $('div#notif-icon').click(()=>{
        $('.dropdown').toggleClass('active');
        $('span.drop-title').text('NOTIFICATIONS');

    });


})
