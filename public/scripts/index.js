
const months = [
    'January', 
    'February', 
    'March',
    'April',
    'May',
    'June',
    'July',
    'August',
    'September',
    'October',
    'November',
    'December'
];

const days = [
    'MONDAY',
    'TUESDAY',
    'WEDNESDAY',
    'THURSDAY',
    'FRIDAY',
    'SATURDAY'
]


let message = "";
let json_arg = undefined;
function setResponse(msg){
    message = msg;
}
function httpRequestExternal(method='GET', url, targetTag = "", async=true,data=''){
    var xml = new XMLHttpRequest();
    xml.open(method,url,async);
    if(method === 'POST'){
        xml.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    }
    xml.onreadystatechange = function(){
        if(this.status==200 && this.readyState==4){
            if(targetTag !== ""){
                document.getElementById(targetTag).innerHTML = this.response;
            }
        }
    }
    xml.send(data);
}

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
/**
 * we will create a function for formatting our time string
 * to comply with the format required for the Time input fields.
 * Note that this is not usable for general cases of formatted
 * Time strings and only accepts Time strings with format "hh:mm am|pm".
 * Put short, just for the purpose of this functionality.
 * 
 * Here's a smiley 😂
 */
const formatTime = timeString =>{
    let timeParts = timeString.replace(" ",":").split(':');
    let formattedString = '';
    for(let i=0; i<2; ++i){
        timeParts[i] = parseInt(timeParts[i]);
    }
    let hour, minute;

    switch(timeParts[2]){
        case "am":
        case "AM":
        case "aM":
        case "Am":
            hour = (timeParts[0] < 10) ? "0" + timeParts[0] : "" + timeParts[0];
            minute = (timeParts[1] < 10) ? "0" + timeParts[1] : "" + timeParts[1];
            break;
        case "pm":
        case "PM":
        case "pM":
        case "Pm":
            hour = timeParts[0]==12? "" + 12 : "" + (12 + timeParts[0]);
            minute = (timeParts[1] < 10) ? "0" + timeParts[1] : "" + timeParts[1];
            break;
    }
    formattedString = hour + ":" + minute + ":00";

    return formattedString;
}
/**
 * 
 * Similar to the function above, we will make yet another local formatter
 * this time for the Date string. Just to comply with the date format required
 * by the HTML input element. Luckily this came out shorter than the time formatter.
 * Again, another smiley 😅
*/ 
const formatDate = dateString => {
    let dateParts = dateString.split("/");
    let month = (parseInt(dateParts[0]) < 10 ? "0"+dateParts[0]:dateParts[0]);
    let day = (parseInt(dateParts[1]) < 10 ? "0"+dateParts[1]:dateParts[1]);
    let year = dateParts[2];

    return year + "-" + month + "-" + day;
}

function clearTargetHTML(targetTag){
    if(targetTag !== "")
        document.getElementById(targetTag).innerHTML = '';
}


function loadDash(){
    appAction = document.getElementById("_appAction");
    httpRequestExternal('GET','/uclm_scholarship/dash?_appAction=' + _appAction,'dashboard-panel',true);
}


function loadWSInfo(idnumber){
    appAction = document.getElementById("_appAction");
    httpRequestExternal('GET','/uclm_scholarship/dash/ws_information/'+idnumber + '?_appAction=' + _appAction, "for-popups");
}



function editWSInformation(){
    let serial = $('form').serialize();

    // alert(serial);

    httpRequestExternal('POST','/uclm_scholarship/working_scholars/update','for-popups',true,serial);
    setTimeout(()=>{
        httpRequestExternal('GET','/uclm_scholarship/dash/ws_view_only','dashboard-panel');
    },60);    
}

/// Code based on tutorial from W3Schools.com
const tableSort = (tableId, col)=>{
    let table, rows, switching, i, x, y, shouldSwitch, dir, switchCount = 0;

    table = document.getElementById(tableId);
    switching = true;

    dir = 'asc';

    while(switching){
        switching = false;
        rows = table.rows;

        // skip table Header and Footer
        for(i = 1; i<(rows.length - 2); ++i){
            shouldSwitch = false;

            // current and next row
            x = rows[i].getElementsByTagName('td')[col];
            y = rows[i+1].getElementsByTagName('td')[col];

            if(dir === 'asc'){
                if(x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()){
                    shouldSwitch = true;
                    break;
                }
            }
            else if(dir === 'desc'){
                if(x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()){
                    shouldSwitch = true;
                    break;
                }
            }
        }
        if(shouldSwitch){
            rows[i].parentNode.insertBefore(rows[i+1],rows[i]);
            switching = true;

            switchCount++;
        }
        else{
            if(dir === 'asc' && switchCount == 0){
                dir = 'desc';
                switching = true;
            }
        }
    }
}

