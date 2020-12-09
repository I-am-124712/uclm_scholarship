$(()=>{


    $('div#for-contents').css({
        'width': 'calc(100% - 300px)',
        'height':'auto',
        'float': 'left'
    });

    $('div#left').css({
        'width' : 'calc(60% - 10px)',
        'height': '180px',
        'float':'left'
    });

    $('div#right').css({
        'width' : 'calc(40% - 10px)',
        'height': 'auto',
        'float':'left',
        'padding': '20px 0px 0px 0px',
    });

    $('div#ws-profile-photo').css({
        'display': 'block',
        'width' : '120px',
        'height': '120px',
        'float': 'left',
        'margin': '25px 10px',
        'background-color': 'rgb(0, 81, 255)',
        'background-size' : 'cover',
        'background-position':'10px 0px 0px 0px',
        'background-repeat' : 'no-repeat',
        'background-image' : 'url("/uclm_scholarship/public/sources/users/user_default.png")',
        'border-radius' : '100%'
    })

    // Left Division Content Styles
    $('div#for-details').css({
        'display': 'block',
        'margin': '30px 0px',
        'float' : 'left',
        'width' : 'calc(100% - 200px)',
        'height': 'auto'
    });
    $('div#for-id-number').css({
        'font-size': '18px',
        'margin' : '5px 0px',
        'color': 'rgb(30, 98, 223)',
    })
    $('div#for-ws-name').css({
        'margin' : '5px 0px',
        'font-size': '20px',
        'font-weight': 'bold',
    })
    $('div#for-department-name').css({
        'font-size': '18px',
        'margin' : '5px 0px',
    })

    // Right division content styles
    $('div#title').css({
        'margin': '5px 0px',
        'font-size': '20px',
    });
    $('div#total').css({
        'margin': '5px 0px',
        'font-size': '18px',
    });

});