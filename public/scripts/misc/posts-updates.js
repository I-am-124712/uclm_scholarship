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