
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


/// Testing purposes only. 
function squirt(args){
    let serial = '/uclm_scholarship/working_scholars/add?'+($("form").serialize());
    alert(args)
    if(args == undefined)
        return;
    let vals = JSON.parse(args);
    if(vals['ok'])
        alert("Yeah");

    httpRequestExternal('GET',serial);
    alert(serial);
}




function loadDash(){
    appAction = document.getElementById("_appAction");
    httpRequestExternal('GET','/uclm_scholarship/dash?_appAction=' + _appAction,'dashboard-panel',true);
}

function addWorkingScholars(departmentID){
    // departmentID = document.getElementById('deptComboBox').value;
    if(departmentID === "0"){
        alert("Select a department first");
        return;
    }
    appAction = document.getElementById("_appAction");
    httpRequestExternal('GET','/uclm_scholarship/dash/add_ws/'+departmentID + '?_appAction=' + _appAction, "for-popups");
}

function loadWSInfo(idnumber){
    appAction = document.getElementById("_appAction");
    httpRequestExternal('GET','/uclm_scholarship/dash/ws_information/'+idnumber + '?_appAction=' + _appAction, "for-popups");
}

function save_ws(){
    let serial = $('form').serialize();
    // alert('/uclm_scholarship/working_scholars/add?'+serial);

    httpRequestExternal('GET','/uclm_scholarship/working_scholars/add?'+serial,'for-popups');
    setTimeout(()=>{
        httpRequestExternal('GET','/uclm_scholarship/dash/ws_view_only','dashboard-panel');
    },60);    
}

function deleteWorkingScholar(idnumber){
    if(confirm('Are you sure you want to delete this WS?')){
        httpRequestExternal('GET','/uclm_scholarship/working_scholars/delete?idnumber='+idnumber);
        setTimeout(()=>{ location.href = '/uclm_scholarship/dash/ws'; },60);
    }
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
function tableSort(tableId, col){
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