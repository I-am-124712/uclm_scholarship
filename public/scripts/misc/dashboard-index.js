$(()=>{
    
    $('.title').css({
        'font-weight':'bolder',
        'font-size':'20px',
        'color':'black',
        'text-align':'left',
        'padding':'5px 0px 5px 20px',
        'width':'auto',
        'height':'30px',
        'border-bottom':'1px solid gray'
    })

    // Test add strips
    testMessageStrip = new MessageStrip()
                    .setIcon("/uclm_scholarship/public/sources/users/user_default.png")
                    .setUserLabel('Gazel Ann Aying')
                    .setLink('/uclm_scholarship/dash/ws_information/17940578')
                    .setMessage('For releasing na ang Second Period sa November nga Allowance. Thank you')
                    .setTime('16 minutes ago')
                    .getMessagesStrip();
    
    $('div#message-panel').append(testMessageStrip);
    testMessageStrip = new MessageStrip()
                    .setIcon("/uclm_scholarship/public/sources/users/user_default.png")
                    .setUserLabel('Ravin Louis Relador')
                    .setLink('/uclm_scholarship/dash/ws_information/18027276')
                    .setMessage('Humana na nakog himo ang DTR gahapon para sa December')
                    .setTime('16 minutes ago')
                    .getMessagesStrip();
    
    $('div#message-panel').append(testMessageStrip);
    testMessageStrip = new MessageStrip()
                    .setIcon("/uclm_scholarship/public/sources/users/user_default.png")
                    .setUserLabel('Jeramine Gine Igot')
                    .setLink('/uclm_scholarship/dash/ws_information/16877839')
                    .setMessage("Rav gipangita kang ma'am Marsha paarion kas office karon dayon naa siyay gipangita")
                    .setTime('16 minutes ago')
                    .getMessagesStrip();
    
    $('div#message-panel').append(testMessageStrip);
    testMessageStrip = new MessageStrip()
                    .setIcon("/uclm_scholarship/public/sources/users/user_default.png")
                    .setUserLabel('Jeramine Gine Igot')
                    .setLink('/uclm_scholarship/dash/ws_information/16877839')
                    .setMessage("Rav asa na daw ka?")
                    .setTime('16 minutes ago')
                    .getMessagesStrip();
    
    $('div#message-panel').append(testMessageStrip);




    // Test Announcement Strips.
    testPostStrip = new PostStrip()
                    .set({
                        profileNameText: 'The Administrator',
                        picUrl: "/uclm_scholarship/public/sources/users/user_default.png",
                        url: '/uclm_scholarship/dash/',
                        dateTimeText: '1 minute ago',
                        messageText: "The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. "
                            + "Sections 1.10.32 and 1.10.33 from \"de Finibus Bonorum et Malorum\" by Cicero are also reproduced "
                            + "in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham."
                    })
                    .finalize()
                    .getPostStrip();

    $('div#posts-panel').append(testPostStrip);
    testPostStrip = new PostStrip()
                    .set({
                        profileNameText: 'The Administrator',
                        picUrl: "/uclm_scholarship/public/sources/users/user_default.png",
                        url: '/uclm_scholarship/dash/',
                        dateTimeText: '23 minutes ago',
                        messageText: "There will be no classes on dates January 2, 2021 to January 16, 2021. Therefore there will be no duty "
                                + "for the mentioned days. Please be guided. Thank you."
                    })
                    .finalize()
                    .getPostStrip();

    $('div#posts-panel').append(testPostStrip);
                    
})