
const decisionForRequest = (id, response) => {


    showFeedbackPopup(id, response);
}

const showFeedbackPopup = (id, res)=>{
    let div = $('<div>');
    let button = $('<button>');
    let textarea = $('<textarea>');

    let modalOverlay = div.clone();
    let modalPanel = div.clone();

    let topButtons = div.clone();
    let infoPanel = div.clone();
    let feedbackArea = textarea.clone();

    let title = div.clone()
    let requestId = div.clone()
    let response = div.clone()


    let closeButton = button.clone();
    let submitFeedbackButton = button.clone();



    modalOverlay.addClass('modal-overlay');
    modalPanel.addClass('modal-panel');

    modalPanel.css({
        'width' : '50%',
        'height' : 'auto',
        'background-color' : 'rgb(0, 64, 184)',
        'color':'white',
    });
    topButtons.css({
        'width': '100%',
        'height':'50px',
    })
    closeButton.css({
        'width' : '40px',
        'height': '40px',
        'background-color': 'inherit',
        'border':'unset',
        'border-radius': '100%',
        'color': 'white',
        'cursor':'pointer',
        'float' : 'right',
        'font-size': '30px',
        'font-weight':'bold',
        'transition-duration': '0.1s'
    });
    closeButton.hover(()=>{
        closeButton.css({
            'color':'red'
        })
    },()=>{
        closeButton.css({
            'color':'white'
        })
    });

    title.css({
        'float':'left',
        'font-size': '20px',
        'font-weight' : 'bold',
        'margin': '20px',
    })
    infoPanel.css({
        'border-radius': '20px',
        'color':'black',
        'float':'left',
        'margin': '0px 15px',
        'padding':'10px 10px',
        'width':'calc(100% - 50px)',
        'height':'auto',
        'background-color':'rgb(200,200,200)',
        'box-shadow': '3px 3px 3px rgba(0,0,0,0.1)'
    });
    requestId.css({
        'width': '100%',
        'height':'30px',
        'font-size':'18px',
    });
    response.css({
        'width':'100%',
        'height':'30px',
        'font-size':'18px',
    });

    feedbackArea.attr('id','feedback-message');
    feedbackArea.css({
        'border-radius':'20px',
        'border':'unset',
        'font-size':'16px',
        'margin':'20px 20px 10px 20px',
        'padding':'10px',
        'max-width':'calc(100% - 60px)',
        'min-width':'calc(100% - 60px)',
        'width':'calc(100% - 60px)',
        'height': 'auto',
        'max-height':'300px',
        'min-height':'100px',
    })

    submitFeedbackButton.addClass('button-solid.round');
    submitFeedbackButton.css({
        'background-color':'rgb(255, 81, 0)',
        'border-radius':'20px',
        'border':'unset',
        'color' : 'white',
        'cursor':'pointer',
        'float': 'none',
        'font-size':'16px',
        'width':'200px',
        'height':'30px',
        'margin':'15px calc(50% - 110px)',
        'margin-top' : '5px',
        'padding': '0px 10px',
        'transition-duration':'0.3s',
    });
    submitFeedbackButton.hover(()=>{
        submitFeedbackButton.css({
            'background-color':'rgb(30, 98, 223)',
        })
    },()=>{
        submitFeedbackButton.css({
            'background-color':'rgb(255, 81, 0)',
        })
    });
    submitFeedbackButton.text('Send Feedback');
    submitFeedbackButton.attr('onclick', 'submitDecision(' + id + ",'" + res + "')");

    title.text('SEND FEEDBACK ON TRANSFER REQUEST');

    closeButton.attr('title','Close');
    closeButton.attr('onclick', "closeModalWindow()");
    closeButton.text('⨉');

    requestId.html('Request ID: <b>' + id + '</b>');

    response.html('Request Response: <b>' + res.toUpperCase() + '</b>');

    topButtons.append(title);
    topButtons.append(closeButton);

    infoPanel.append(requestId);
    infoPanel.append(response);

    modalPanel.append(topButtons);
    modalPanel.append(infoPanel);
    modalPanel.append(feedbackArea);
    modalPanel.append(submitFeedbackButton);

    modalOverlay.append(modalPanel);
    $('div.app-main').append(modalOverlay);


};

const submitDecision = (id, response) => {
    let feedbackMessage = $('textarea#feedback-message').val();
    let dataObj = {
        request_id : id,
        response: response,
        feedback: feedbackMessage
    };
    let params = "req&data=" + JSON.stringify(dataObj);

    console.log(params);

    $.ajax({
        method: 'post',
        url: '/uclm_scholarship/working_scholars/transferRequestFeedback',
        data: params,
        dataType: 'json',
        success: res =>{
            console.log('SUCCESS');
            console.log(res);
            closeModalWindow();
            reloadPage(idForUpdate);
        },
        error: err => {
            console.log('ERR');
            console.log(err.responseText);
        }
    })
}

const closeModalWindow = ()=>{
    $('div.modal-overlay').remove();
}