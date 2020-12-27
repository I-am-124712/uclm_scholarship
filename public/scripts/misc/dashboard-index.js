$(()=>{
    
    $('div.chart-title').css({
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
    testMessageStrip = new MessageStrip().setIcon("/uclm_scholarship/public/sources/users/user_default.png")
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




                    
})