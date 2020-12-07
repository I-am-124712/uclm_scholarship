// STYLES //
let scheduleForToday = {
    rawTimeInValue : '',
    rawTimeOutValue: '',
    total: 0
};
let attendanceButtonPanelStyle = {
    'float': 'left',
    'width' : '25%',
    'height' : '250px',
    'margin' : '5px',
    'padding': '15px 15px',
    'background': 'white',
    'border-radius': '20px',
    'box-shadow': '3px 3px 5px rgba(0,0,0,0.3)',
};
let mySchedulePanelStyle = {
    'float': 'left',
    'width' : 'calc(75% - 50px)',
    'height' : '250px',
    'margin' : '5px',
    'background': 'white',
    'border-radius': '20px',
    'box-shadow': '3px 3px 5px rgba(0,0,0,0.3)',
};
let myAttendancePanelStyle = {
    'width' : 'auto',
    'height' : 'auto',
    'max-height' : '300px',
    // 'height': 'calc(min(auto, 300px))',
    'margin' : '5px',
    'background': 'white',
    'border-radius': '20px',
    'box-shadow': '3px 3px 5px rgba(0,0,0,0.3)',
    'padding': '0px 15px 15px 15px',
};
let tableStyle = {
    "overflow-y": "auto",
    "width" : "100%",
    "height": "auto",
    "max-height": "180px",
    "border-radius": "20px"
};
let panelTitleHeaderStyle = {
    "width":"100%", 
    "height": "24px", 
    "font-size":"20px", 
    "font-weight":"bold", 
    "padding": "15px",
}
let scheduleTabPanelStyle = {
    "background" : "rgb(0,64,184)",
    "border-radius": "20px",
    "float":"left",
    "width":"25%",
    "height": "100%",
};
let scheduleTabSmallStyle = {
    "background" : "rgb(40,40,40)",
    "border-radius": "20px",
    "float":"left",
    "width":"200px",
    "height": "100%",
    'margin' : '0px 5px',

};

let scheduleLabelStyle = {
    'width' : '100%',
    "height" : "auto",
    "float" : 'left',
    'text-align': 'center',
    'color' : 'white',
    "margin" : "80px 0px 10px 0px",
};
let scheduleLabelSmallStyle = {
    'width' : '100%',
    "height" : "auto",
    "float" : 'left',
    'text-align': 'center',
    'color' : 'white',
    "margin" : "50px 0px 10px 0px",
};

let scheduleActualStyle = {
    'width' : '100%',
    "height" : "auto",
    "float" : 'left',
    'font-size' : '20px',
    'color' : 'rgb(255, 81, 0)',
    'text-align': 'center',
};
let scheduleActualSmallStyle = {
    'width' : '100%',
    "height" : "auto",
    "float" : 'left',
    'font-size' : '16px',
    'color' : 'rgb(255, 81, 0)',
    'text-align': 'center',
};
// END STYLES //


$(()=>{
    // Load them schedules
    loadSchedules();

    // load our JS coded styles
    loadStyles();

    // load our records
    loadRecords();

    // Functions for our Virtual Biometrics
    $('button#btn-submit-in').click(submitAttendance);
    $('button#btn-submit-out').click(submitAttendance);
});


/**
 * Makes an AJAX POST request to retrieve a JSON containing the
 * current WS user's schedule for the current semester.
 */
const loadSchedules = ()=>{
    $.ajax({
        url: '/uclm_scholarship/records/retrieveScheduleAsJSON',
        data: 'req=true',
        dataType: 'JSON',
        method: 'post',
        success: res => {
            console.log(res);

            let $div = $("<div>");
            $('span#for-semester-name').text(res.semester);
            $('span#for-day').text("NO SCHEDULE");
            $('span#for-time').html("For Today");

            if(res.forToday != null)
                scheduleForToday = res.forToday;

            for(index in res = res.schedule){
                $tab = $div.clone();
                $label = $div.clone();
                $actual = $div.clone();

                $tab.attr("id", "schedule-tab-small");
                $label.attr("id",'schedule-label-small');
                $actual.attr("id",'schedule-actual-small');

                $label.html(res[index].day);
                $actual.html(res[index].time);


                $tab.append($label);
                $tab.append($actual);
                
                if(res[index].isForToday){
                    $check = $div.clone();
                    $check.css({
                        'float' : 'left',
                        // 'margin': 'auto',
                        'margin-left': '20px',
                        'width': '30px',
                        'height': '30px',
                        'background-color' : 'rgb(0,180,0)',
                        'background-image': 'url("/uclm_scholarship/public/sources/icons/iconCheck.png")',
                        'background-size': 'cover',
                        'background-repeat': 'no-repeat',
                        'border-radius': '100%'
                    })
                    $tab.append($check);

                    // Show this info to that big box on the right as well.
                    $('span#for-day').text(res[index].day);
                    $('span#for-time').html(res[index].time);
                }

                $('div#schedule-container').append($tab);
            }

            loadStyles();
        },
        error: err => {
            console.log(err.responseText);
            console.log("ERROR Error processing request.");
        }
    })
}

const loadStyles = ()=>{
    $('div#attendance-button-panel').css(attendanceButtonPanelStyle);
    $('div#my-schedules-panel').css(mySchedulePanelStyle);
    $('div#my-attendance-panel').css(myAttendancePanelStyle);
    $('div#for-table-panel').css(tableStyle);
    $("div#panel-title").css(panelTitleHeaderStyle)

    $('div#schedule-tab').css(scheduleTabPanelStyle);
    $('div#schedule-tab-small').css(scheduleTabSmallStyle);

    $('div#schedule-label').css(scheduleLabelStyle);
    $('div#schedule-label-small').css(scheduleLabelSmallStyle);

    $('div#schedule-actual').css(scheduleActualStyle);
    $('div#schedule-actual-small').css(scheduleActualSmallStyle);



}

const submitAttendance = function(){
    let attype = $(this).val();
    let scheduleIn = scheduleForToday.rawTimeInValue;
    let scheduleOut = scheduleForToday.rawTimeOutValue;
    let totalHours = scheduleForToday.total;

    let forPassing = "";
    if(attype === 'in'){
        forPassing = scheduleIn;
    }else if(attype==="out"){
        forPassing = scheduleOut;
    }

    let params = 'req&attype=' + attype + "&schedule=" + forPassing + "&totalHours=" + totalHours;
    console.log(params);

    $.ajax({
        url: '/uclm_scholarship/working_scholars/submitAttendance',
        data: params,
        method: 'post',
        dataType: 'JSON',
        success: res => {
        },
        error: err => {
            console.log(err.responseText);
        }
    });

    // Reload our records table to update
    loadRecords();
}

const loadRecords = ()=>{
    // We will buffer the table rows so to prevent any flickering during update.
    // Plus we will do the update through this object before displaying it.
    let $table = $('table#attendance-table').clone();

    // Clear data rows first...
    $table.find('tr#header-row').siblings().remove();

    // retrieve our data from server...tada!
    $.ajax({
        url: '/uclm_scholarship/working_scholars/getMyRecordForThisMonth',
        data: 'req',
        dataType: 'JSON',
        method: 'post',
        success: res => {

            let $row = $('<tr>');
            let $data = $('<td>');

            $data.addClass('table-flat-data');

            for(i in rec = res.records){
                let $currentRow = $row.clone();

                let $date = $data.clone();
                let $in = $data.clone();
                let $out = $data.clone();
                let $late = $data.clone();
                let $undertime = $data.clone();
                
                $date.text(rec[i].recordDate);
                $in.text(rec[i].timeIn);
                $out.text(rec[i].timeOut);
                $late.text(rec[i].late);
                $undertime.text(rec[i].undertime);

                $currentRow.append($date);
                $currentRow.append($in);
                $currentRow.append($out);
                $currentRow.append($late);
                $currentRow.append($undertime);

                $table.append($currentRow);
                $('div#for-table-panel').html($table);
                $('span#for-month-name').text(months[res.month - 1].toUpperCase() + " " + res.year);
            }
        },
        error: err => {
            console.log(err.responseText);
        }
    });
}