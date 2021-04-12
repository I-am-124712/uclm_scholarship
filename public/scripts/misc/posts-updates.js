const updatePosts = optional =>{
    let postStripMaker = new PostStrip();

    $.ajax({
        method: 'post',
        url: '/uclm_scholarship/dash/retrieveAdminPosts',
        data: 'req_id=69',
        dataType: 'JSON',
        success: res => {
            let buffer = $('<div>');

            if(res.length > 0) for(let x in res){
                let userFullName = res[x].userFullName;
                let userPhoto = res[x].userPhoto;
                let postText = res[x].postText;
                let postTimestamp = res[x].postTimestamp;
                let userId = res[x].userId;

                // Test Announcement Strips.
                let strip = postStripMaker.set({
                    profileNameText: userFullName,
                    picUrl: userPhoto,
                    url: '/uclm_scholarship/profile/' + userId,
                    dateTimeText: postTimestamp,
                    messageText: postText
                })
                .finalize()
                .getPostStrip();

                buffer.append(strip);
            }
            $('div#posts-panel').html(buffer);

        },
        error: err => {
            console.log(err.responseText);
        }
    });
}

addForPerform(updatePosts, {});

const openCreatePostPopup = ()=>{
    let button = $('<button>');
    let div = $('<div>');
    let textarea = $('<textarea>');

    let close = button.clone();
    let overlay = div.clone();
    let panel = div.clone();
    let title = div.clone();
    let forPost = textarea.clone();
    let postButton = button.clone();

    overlay.addClass('modal-overlay');
    panel.addClass('modal-panel');

    panel.css({
        'background': 'rgb(0, 64, 184)',
        'color': 'white',
        'width': '50%',
        'height': '50%',
        'font-size': '1vw',
    });

    title.css({
        'width':'auto',
        'height':'30px',
        'margin':'15px',
        'font-size': '20px',
        'font-weight':'bold',
        'font-size': '1vw',
    });
    title.text('CREATE POST');

    forPost.css({
        'width':'calc(100% - 30px)',
        'min-width':'calc(100% - 30px)',
        'max-width':'calc(100% - 30px)',
        'margin':'15px 10px',
        'height': '60%',
        'max-height': '60%',
        'min-height': '60%',
        'font-size':'1vw'
    });
    forPost.attr('id', 'post-area');

    button.css({
        'font-family': "'Century Gothic'",
        'border':'unset',
        'border-radius' : '100%',
        'width':'30px',
        'height':'30px',
        'color': 'white',
        'background': 'inherit',
        'font-size': '16px',
        'font-weight': 'bold',
        'transition-duration':'0.3s',
        'float':'right',
        'margin':'10px'
    });
    button.hover(()=>{
        button.css({
            'background': 'red'
        })
    }, ()=>{
        button.css({
            'background': 'inherit'
        })
    });
    button.on('click', closeModalPanel);
    button.text('⨉');

    postButton.addClass('button-solid round');
    postButton.css({
        'margin':'0px 20px',
        'width':'calc(100% - 40px)',
        'height':'30px',
        'font-size': '1vw'
    })
    postButton.text('Post');
    postButton.on('click', submitPost);

    title.append(button);

    panel.append(title);
    panel.append(forPost);
    panel.append(postButton);
    overlay.append(panel);

    $('div.app-main').append(overlay);
}

const submitPost = ()=>{
    let postText = $('textarea#post-area').val();
    let params = 'req&post=' + postText;

    $.ajax({
        method: 'POST',
        data: params,
        url: '/uclm_scholarship/dash/createPost',
        success: res => {
            console.log('NOICE');
            closeModalPanel();
        },
        error: err => {
            console.log('Could not post');
        }
    })
}

const closeModalPanel = ()=>{
    $('div.modal-overlay').remove();
}