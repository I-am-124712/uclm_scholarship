
$(function(){

    $("button#btn-load").css({
        'width' : '100%',
        'margin-top': '10px',
        'float' : 'left'
    });

    // initialize custom style for Save and PDF button
    $("button#btn-save").css({
        'width' : '100%',
        'margin-top': '10px',
        'float' : 'left',
        'background-color' : 'inherit'
    });
    $("button#btn-pdf").css({
        'width' : '100%',
        'margin-top': '10px',
        'float' : 'left',
        'background-color' : 'inherit'
    });

    // add a mouse-over listener for both buttons.
    // For Save:
    $("button#btn-save").mouseenter(function(){
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
    // For PDF:
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


    /* Fill departments combo box */
    $.ajax({
        type: 'get',
        url: '/uclm_scholarship/records/get_departments',
        data: 'req',
        dataType: 'JSON',
        success: function(res){
            let departments = res;
            for(let i=0; i<departments.length; ++i){
                let $row = $("<option>");
                $row.val(departments[i].deptId);
                $row.text(departments[i].departmentName);
                $('select#department').append($row);
            }
        },
        error: function(err){
            console.log(err);
        }
    });

    /* Default Schedule plotting will be automatic */
    $('input#auto').attr('checked',true);

});


/* Loads the DTR data from the selected settings... */
const getDtrData = _=>{
    $("button#btn-save").slideDown();

    let schoolYear = $('#school-year').serialize();
    let department = $('#department').serialize();
    let semester = $('#semester').serialize();
    let period = $('#period').serialize();
    let month = $('#month').serialize();
    let hide = $('#hide').serialize();
    let loadMethod = $("#load-methods").serialize();
    let requested = "req";

    let params = schoolYear 
            + "&" + department 
            + "&" + semester 
            + "&" + period 
            + "&" + month
            + "&" + hide
            + "&" + loadMethod
            + "&" + requested;


    // prepare the div that will contain the table
    $('div.table').text('');

    // here comes the request...
    $.ajax({
        type: 'post',
        url: '/uclm_scholarship/records/dtr',
        data: params,
        dataType: 'JSON',
        success: function(data){
            let $table = $('<table>');
            let $row = $('<tr>');
            let $data = $('<td>');
            $table.addClass('table-flat');

            // console.log(data);
            
            for(ws in data){
                let $newHeaderRow = $row.clone();
                let $newHeaderData = $data.clone();
                $newHeaderData.addClass('table-flat-header');
                $newHeaderData.attr('colspan', 10);
                $newHeaderData.css({
                    'border-top-left-radius': '20px',
                    'border-top-right-radius': '20px',
                    'font-size' : '20px'
                });
                $newHeaderData.text(data[ws].idnumber + " - " + data[ws].wsName);
                // $newHeaderData.text("▮▮▮▮▮▮▮▮ - " + data[ws].wsName);
                $newHeaderRow.append($newHeaderData);
                $table.append($newHeaderRow);

                let wsRecords = data[ws].wsRecords;

                // We will show a unique looking prompt telling the user
                // there is no DTR entry saved for the selected month & period

                if(wsRecords.length <= 0){
                    // fix the table's width to not compress its contents
                    $('div.table').css({
                        'width' : "60%"
                    })

                    $newHeaderRow = $row.clone();
                    $newHeaderData = $data.clone();
                    $newHeaderData.addClass('table-flat-data');
                    $newHeaderData.attr('colspan', 10);
                    $newHeaderData.css({
                        'border-bottom-left-radius': '20px',
                        'border-bottom-right-radius': '20px',
                    });
                    $newHeaderData.text("No DTR Entry to show");
                    $newHeaderRow.append($newHeaderData);
                    $table.append($newHeaderRow);
                }
                else{
                    // automatically adjust table width
                    $('div.table').css({
                        'width' : "75%"
                    })
                    $newHeaderRow = $row.clone();
                    $newHeaderDataBase = $data.clone();
                    $newHeaderDataBase.addClass('table-flat-data');
                    $newHeaderDataBase.css({
                        'background-color' : 'rgb(255, 115, 0)',
                        'font-size' : '14px'
                    });

                    // For the subheader rows
                    let $dateHeader = $newHeaderDataBase.clone();
                    let $schedInHeader = $newHeaderDataBase.clone();
                    let $schedOutHeader = $newHeaderDataBase.clone();
                    let $timeInHeader = $newHeaderDataBase.clone();
                    let $timeOutHeader = $newHeaderDataBase.clone();
                    let $lateHeader = $newHeaderDataBase.clone();
                    let $undertimeHeader = $newHeaderDataBase.clone();
                    let $totalHeader = $newHeaderDataBase.clone();
                    let $actionHeader = $newHeaderDataBase.clone();


                    $dateHeader.text("Record Date");
                    $schedInHeader.text('Schedule-in');
                    $schedOutHeader.text('Schedule-out');
                    $timeInHeader.text("Time-in");
                    $timeOutHeader.text("Time-out");
                    $lateHeader.text('Lates');
                    $undertimeHeader.text('Undertime');
                    $totalHeader.text("Total Hours");


                    $newHeaderRow.append($dateHeader);
                    $newHeaderRow.append($schedInHeader);
                    $newHeaderRow.append($schedOutHeader);
                    $newHeaderRow.append($timeInHeader);
                    $newHeaderRow.append($timeOutHeader);
                    $newHeaderRow.append($lateHeader);
                    $newHeaderRow.append($undertimeHeader);
                    $newHeaderRow.append($totalHeader);
                    $newHeaderRow.append($actionHeader);

                    $table.append($newHeaderRow);

                    // We'll prepare all schedules for this current
                    // working scholar for the purpose of not re-retrieving
                    // them for the 'Manual' Schedule plotting. See below.
                    let schedules = data[ws].schedule;


                    for(x in wsRecords){
                        $newDataRow = $row.clone();
                        $newDataBase = $data.clone();
                        $newDataBase.css({
                            'font-size' : '14px'
                        });

                        // for each record data cell
                        let $dateData = $newDataBase.clone();
                        let $schedInData = $newDataBase.clone();
                        let $schedOutData = $newDataBase.clone();
                        let $timeInData = $newDataBase.clone();
                        let $timeOutData = $newDataBase.clone();
                        let $lateData = $newDataBase.clone();
                        let $undertimeData = $newDataBase.clone();
                        let $totalData = $newDataBase.clone();
                        let $actionsData = $newDataBase.clone();
                        
                        // Edit Entry button
                        let $editButton = $("<button>");
                        $editButton.addClass("button-solid round");
                        $editButton.attr("id", "action-button-info-icon");
                        $editButton.attr("onclick", "editDtrData($(this))");

                        // Delete Entry Button
                        let $deleteButton = $("<button>");
                        $deleteButton.addClass("button-flashing round");
                        $deleteButton.attr("id", "action-button-delete-icon-white");
                        $deleteButton.attr("onclick", "deleteDtrData($(this))");

                        $editButton.val(wsRecords[x].record_id);
                        $deleteButton.val(wsRecords[x].record_id);

                        $actionsData.append($editButton);
                        $actionsData.append($deleteButton);

                        // Add class for styling...
                        $dateData.addClass('table-flat-data');
                        $schedInData.addClass('table-flat-data');
                        $schedOutData.addClass('table-flat-data');
                        $timeInData.addClass('table-flat-data');
                        $timeOutData.addClass('table-flat-data');
                        $lateData.addClass('table-flat-data');
                        $undertimeData.addClass('table-flat-data');
                        $totalData.addClass('table-flat-data');
                        $actionsData.addClass('table-flat-data');

                        // Add ids for each tds
                        $dateData.attr('id','date');
                        $schedInData.attr('id','sched-in');
                        $schedOutData.attr('id','sched-out');
                        $timeInData.attr('id','time-in');
                        $timeOutData.attr('id','time-out');
                        $lateData.attr('id','late');
                        $undertimeData.attr('id','undertime');
                        $totalData.attr('id','totalData');
                        $actionsData.attr('id','table-flat-data');

                        
                        let dateString = "";
                        let timeInString = "";
                        let timeOutString = "";

                        let recordId = wsRecords[x].record_id;
                        let recordDate = new Date(wsRecords[x].recorddate.date);
                        let timeIn = (tin = wsRecords[x].timeIn)? new Date(tin.date):null;
                        let timeOut = (tout = wsRecords[x].timeOut)? new Date(tout.date):null;
                        let hoursRendered = wsRecords[x].hoursRendered;

                        dateString = months[recordDate.getMonth()] + " " + recordDate.getDate() + ", " + recordDate.getFullYear();
                        timeInString = format12HourTime(timeIn);
                        timeOutString = format12HourTime(timeOut);
                        
                        // Assign values for each cells
                        $dateData.val(dateString);
                        $timeInData.val(timeInString);
                        $timeOutData.val(timeOutString);
                        $lateData.val(wsRecords[x].late);
                        $undertimeData.val(wsRecords[x].undertime);
                        $totalData.val(hoursRendered);

                        // then display them as text in the cells.
                        $dateData.text(dateString);
                        $timeInData.text(timeInString);
                        $timeOutData.text(timeOutString);
                        $lateData.text(wsRecords[x].late);
                        $undertimeData.text(wsRecords[x].undertime);
                        $totalData.text(hoursRendered + " Hour(s)");

                        // Style the cells for Lates and Undertimes depending
                        // on the value. For Lates/Undertimes less than one (1)
                        // hour, we highlight the cell with orange. More than
                        // one, we will highlight with red.
                        if(wsRecords[x].late > 0){
                            if(wsRecords[x].late <= 1)
                                $lateData.css({
                                    'background-color' : 'rgb(255,100,0)'
                                });
                            else
                                $lateData.css({
                                    'background-color' : 'rgb(200,0,0)'
                                });
                            $lateData.css({
                                'font-weight' : 'bolder'
                            });
                        }
                        if(wsRecords[x].undertime > 0){
                            if(wsRecords[x].undertime <= 1)
                                $undertimeData.css({
                                    'background-color' : 'rgb(255,100,0)'
                                });
                            else
                                $undertimeData.css({
                                    'background-color' : 'rgb(200,0,0)'
                                });
                            $undertimeData.css({
                                'font-weight' : 'bolder'
                            });
                        }


                        // Let's take the assumed schedule for this row
                        var scheduleFor = wsRecords[x].schedule;


                        // On this part we get to decide what we will display
                        // on the schedule-in and -out cells in our DTR entry rows
                        // based on what the user chooses.
                        switch(loadMethod){

                            case 'load-method=manual':
                                // For manual schedule plotting (manually assign each schedule for each row/entry)
                                // a combo box will be provided, containing the Schedule for the working scholar,
                                // as well as pre-selecting the presumed appropriate schedule for that DTR date.
                                let $comboBox = $("<select>");
                                let $option = $("<option>");

                                // Style 'a bit'
                                $comboBox.css({
                                    'width' : '80px',
                                    'margin-bottom' : '5px'
                                });


                                // prepare each option elements
                                let $schedInOption = $option.clone();
                                let $schedOutOption= $option.clone();

                                // add name attributes for them...
                                $schedInOption.attr('name', 'schedIn');
                                $schedOutOption.attr('name', 'schedOut');

                                if(scheduleFor.length > 0){
                                    for(x in scheduleFor){

                                        // prepare the two combo boxes for the row...
                                        let $schedInDefault = $comboBox.clone();
                                        let $schedOutDefault = $comboBox.clone();

                                        for(i in schedules){
                                            // Fill each comboboxes with options.
                                            $in = $schedInOption.clone();
                                            $out = $schedOutOption.clone();

                                            $in.val(schedules[i].schedule_id);
                                            $out.val(schedules[i].schedule_id);

                                            $in.text(format12HourTime(new Date(schedules[i].tin.date)));
                                            $out.text(format12HourTime(new Date(schedules[i].tout.date)));

                                            if(schedules[i].schedule_id == scheduleFor[x].schedule_id){
                                                $in.attr('selected', true);
                                                $out.attr('selected', true);
                                            }

                                            $schedInDefault.append($in);
                                            $schedOutDefault.append($out);
                                        }
                                        $schedInData.append($schedInDefault);
                                        $schedOutData.append($schedOutDefault);
                                    }
                                }

                                break;

                            case 'load-method=auto':   
                            default:
                                if(scheduleFor.length > 0){
                                    // we'll loop through every schedule associated with the duty hours
                                    // since there will be duty records with one or more schedule
                                    // (in our terms, "broken" schedules)
                                    let schedIn = "";
                                    let schedOut = "";
                                    for(i in scheduleFor){
                                        schedIn += format12HourTime(new Date(scheduleFor[i].schedIn.date)) + "<br>";
                                        schedOut += format12HourTime(new Date(scheduleFor[i].schedOut.date)) + "<br>";
                                    }
                                    $schedInData.html(schedIn);
                                    $schedOutData.html(schedOut);
                                }
                        }

                        $newDataRow.addClass('dtr-entry');
                        $newDataRow.attr("id",recordId);

                        $newDataRow.append($dateData);
                        $newDataRow.append($schedInData);
                        $newDataRow.append($schedOutData);
                        $newDataRow.append($timeInData);
                        $newDataRow.append($timeOutData);
                        $newDataRow.append($lateData);
                        $newDataRow.append($undertimeData);
                        $newDataRow.append($totalData);
                        $newDataRow.append($actionsData);

                        $table.append($newDataRow);

                    }
                }
            }
            $('div.table').append($table);

        },
        error: function(err){
            console.log('Error retrieving DTR Entries. ');
            console.log(err.responseText);
        }
    });


};

// For DTR Data Deletion. Now, instead of reloading all data,  we will
// just delete the row element of the schedule after we successfully
// performed deletion on the server.
const deleteDtrData = $src => {

    if(confirm("Are you sure you want to permanently delete this DTR entry?")){
        let recordId = 'record_id=' + $src.val();
        let $rowToDelete = $src.parent().parent('tr');

        $.post({
            url: '/uclm_scholarship/records/delete',
            data: recordId,
            success: function(data){
                $rowToDelete.remove();
            },
            error: function(err){
                console.log(err);
            }
        });
    }

}

// For Editing DTR Data. This functionality will allow users to edit the data
// on the selected row. This functionality is put here since some Records are not
// properly parsed once the raw data from the Biometrics Machine is uploaded to 
// the system. One reason is that Working Scholars tend to forget switching the
// machine to "Check-out" once they check out from their attendance. The user 
// will have the control to modify such discrepant entries.
const editDtrData = $src => {

    // we get the row for querying...
    let $editingRow = $src.parent();

    // we then get the cells for time-in and time-out
    let $timeIn = $editingRow.siblings('td#time-in').eq(0);
    let $timeOut = $editingRow.siblings('td#time-out').eq(0);

    // Get schedule id...
    let record_id = $src.val();
    

    // Check first if we are editing the row...
    if($src.attr("isEditing")){

        // Switch back to orange edit button
        $src.removeAttr("id");
        $src.attr("id", "action-button-info-icon");

        // Get the newly changed DTR data.
        let newTimeIn = $timeIn.children('input').eq(0).val();
        let newTimeOut = $timeOut.children('input').eq(0).val();

        // Set to null whenever an empty input is provided
        newTimeIn = (newTimeIn === '')? null : newTimeIn;
        newTimeOut = (newTimeOut === '')? null : newTimeOut;

        console.log('New time-in: ' + newTimeIn);
        console.log('New time-out: ' + newTimeOut);

        let params = "record_id=" + record_id + "&"
                    + "timeIn=" + newTimeIn + "&"
                    + "timeOut=" + newTimeOut;

        // Perform a backend commit to our changes. If successful,
        // we proceed to change the cells in the current row.
        $.post({
            url: '/uclm_scholarship/records/update',
            data: params,
            success: function(res){
                console.log(res);
                // only if we're successful in commiting changes
                // should we remove the attribute...
                $src.removeAttr("isEditing");

                // Update cells.
                $timeIn.text(newTimeIn);
                $timeOut.text(newTimeOut);
            },
            error: function(r){
                console.log(r.responseText);
            }
        });

    }
    else{
        $src.attr("isEditing", true);

        console.log("Editing DTR Entry ID: " + $src.val());

        // Switch to green check button
        $src.removeAttr("id");
        $src.attr("id", "action-button-confirm-icon");


        // extract the respective cell's values
        let $timeInValue = $timeIn.val();
        let $timeOutValue = $timeOut.val();

        // replace them with textboxes
        let $textbox = $('<input type="text">');
        let $timeInText = $textbox.clone();
        let $timeOutText = $textbox.clone();

        // assign the values
        $timeInText.val($timeInValue);
        $timeOutText.val($timeOutValue);

        // style the textboxes a bit
        $timeInText.css({
            'width' : '60px'
        })        
        $timeOutText.css({
            'width' : '60px'
        })        

        // Then show
        $timeIn.html($timeInText);
        $timeOut.html($timeOutText);
    }
};
