

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
    // styling for table
    let $table = $("div.table"); 
    $table.css({
        'border-radius' : '20px',
        'width' : 'calc(100% - 170px)',
        'height': 'auto',
        'float': 'left',
        'position' : 'auto',
        'box-shadow': '3px 3px 3px rgba(0,0,0,0.3)'
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