

// Format DateTime Object to 12-hour format (hh:mm AM|PM)
const format12HourTime = timeObj => {
    let timeHour;
    let timeMinute;
    let stringHour;
    let stringMinute;
    let timeFullString;
    if(timeObj == null)
        return '';

    timeHour = timeObj.getHours();
    timeMinute = timeObj.getMinutes();

    if(timeHour >= 12) {
        stringHour = timeHour==12? 12 : ("" + ((modTimeHour= timeHour % 12) < 10 ? '0'+modTimeHour:modTimeHour));
        stringMinute = timeMinute < 10? "0"+timeMinute:timeMinute; 
        timeFullString = stringHour + ":" + stringMinute + " PM";
    }
    else{
        stringHour = "" + (timeHour < 10? "0"+timeHour:timeHour);
        stringMinute = timeMinute < 10? "0"+timeMinute:timeMinute; 
        timeFullString = stringHour + ":" + stringMinute + " AM";
    }

    return timeFullString;
};

$(()=>{
    // styling for drawer
    let $drawer = $(".drawer"); 
    $drawer.css({
        'background' : "rgb(0, 64, 184)",
        'border-radius': '20px',
        'width' : "225px",
        'height': 'auto',
        'padding': '10px',
        'margin': '0px 20px 20px 0px',
        'float' : 'left',
        'display' : 'block',
        // 'position' : 'fixed'
    });
    $drawer.children().css({
        'color' : 'white',
        'font-size': '14px'
    });
    // styiling title
    $('div#title').css({
        'color' : 'rgb(255, 163, 88)',
        "font-size" : '20px',
        'font-weight' : 'bolder',
        'text-align' : 'center',
        'width' : '100%'
    });
    $drawer.children('select').css({
        'border' : 'unset',
        'background-color' : 'white',
        'color' : 'black',
        'font-size': '14px'
    })
    $drawer.children('select').children("option").css({
        'background-color': 'white',
        'color': 'black'
    })
    $('#btn-load').css({
        'margin-top' : "15px",
        'width' : '100%' 
    });
    // initialize custom style for Save and PDF button
    $("button#btn-save").css({
        'width' : '100%',
        'margin-top': '10px',
        'float' : 'left',
        'background-color' : 'inherit'
    });

    // styling for table
    let $table = $(".table"); 
    $table.css({
        'border-radius' : '20px',
        // 'margin': '0px 20px',
        'width' : 'auto',
        'height': 'auto',
        'float': 'left',
        'position' : 'auto'
    });

    // Fill the Months combo box...
    for(let i=0; i<months.length; ++i){
        let $monthOption = $("<option>");
        $monthOption.val((i));
        $monthOption.text(months[i]);
        $("select#month").append($monthOption);
    }
})