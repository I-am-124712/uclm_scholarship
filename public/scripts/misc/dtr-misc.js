

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


    // For PDF button:
    $("button#btn-pdf").css({
        'width' : '100%',
        'margin-top': '10px',
        'float' : 'left',
        'background-color' : 'inherit'
    });
    $("button#btn-pdf").mouseenter(function(){
        $(this).css({
            'width' : '100%',
            'margin-top': '10px',
            'float' : 'left',
            'background-color' : 'rgb(90,150,255)'
        });
    }).mouseleave(function(){
        $(this).css({
            'width' : '100%',
            'margin-top': '10px',
            'float' : 'left',
            'background-color' : 'inherit'
        });
    });

    // Fill the Months combo box...
    for(let i=0; i<months.length; ++i){
        let $monthOption = $("<option>");
        $monthOption.val((i));
        $monthOption.text(months[i]);
        $("select#month").append($monthOption);
    }
})


/**
 * Sends a request to generate a pdf and opens it in a new tab.
 */
const generatePDF = (departmentName, dtrJSONData) => {

    // This object array will hold the data that we will be sending to our
    // post request.
    let dataObj = [];

    // Let us filter out what we will be sending to our request.

    dataObj.push
    for(x in data = dtrJSONData){
        dataObj.push({
            wsName: data[x].wsName,
            wsRecords : data[x].wsRecords
        });
    }
    let param = 'department="' + departmentName + '"&' +
            "data=" + JSON.stringify(dataObj);
    console.log(param);

    $.ajax({
        url: '/uclm_scholarship/utilities/generate/pdf/dtr',
        type: 'post',
        dataType: "html",
        data: param,
        success: res => {
            let printer = window.open('','_blank');
            printer.document.write(res);
            setTimeout(function(){
                printer.print();
            }, 500);
        }
    })
};