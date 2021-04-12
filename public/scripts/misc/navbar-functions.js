
$(()=>{
    $("#hamburger1").click(function(){

        let sidebarVisible = $(".app-sidebar-panel").is(":visible")? 
            _=>{
                $(".app-sidebar-panel").hide();
                return 'off';
            } : 
            _=> {
                $(".app-sidebar-panel").show()
                return 'on';
            } ;

        $.ajax({
            method: 'post',
            data: "toggle-sidebar=" + sidebarVisible(),
            url: "/uclm_scholarship/utilities/hideSidebar",
            success: res => { console.log(res); },
            error: err => { console.log(err.message); }
        })

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
