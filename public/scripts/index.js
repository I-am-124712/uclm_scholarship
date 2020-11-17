
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

