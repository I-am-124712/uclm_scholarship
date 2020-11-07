<?php require './app/views/user_view.php'; ?>

<div class="app-dash-panel">
    <div class="drawer">

        <!-- Title -->
        <div id="title">
            DAILY TIME RECORD
        </div>
        <!-- School Year -->
        <label for="" id="form-label2">School Year</label>
        <select name="school-year" id="school-year" class="textbox-transparent">
            <option value="2019-2020">2019-2020</option>
        </select>

        <!-- Department -->
        <label for="" id="form-label2">Department</label>
        <select name="department" id="department" class="textbox-transparent"></select>

        <!-- Selector for semester to be used as basis for schedule -->
        <label for="semester" id="form-label2">Use schedule for</label>
        <select name="semester" id="semester" class="textbox-transparent">
            <option value="1">First Semester</option>
            <option value="2">Second Semester</option>
            <option value="3">Summer</option>
        </select>
        <!-- checkbox for hiding or showing records with no times-in or -out -->
        
        <label for="hide" id="form-label2" style="margin: 10px 0px; float:left; width: 100%; font-size:12px">
            <input type="checkbox" name="hide" id="hide">
            Hide records with no in/out
        </label>

        <!-- DTR period to load  -->
        <label for="period" id="form-label2">DTR Period</label>
        <select name="period" id="period" class="textbox-transparent">
            <option value="1" >First Period</option>
            <option value="2" >Second Period</option>
        </select>
        <label for="month" id="form-label2">Month</label>
        <select name="month" id="month" class="textbox-transparent">
        </select>
        <form action="" name="load-methods" id="load-methods">
            <label for="" class="form-label2" style="float: left; margin:10px 5px;">Schedule plotting method
                <label for="load-method" class="form-label2" style="float:left; margin-left: 20px; width:100%">
                    <input type="radio" name="load-method" id="auto" value="auto">
                    Automatic
                </label>
                <label for="load-method" class="form-label2" style="float:left; margin-left: 20px; width:100%">
                    <input type="radio" name="load-method" id="manual" value="manual">
                    Manual
                </label>
            </label>
        </form>
        <button class="button-solid round" id="btn-load" onclick="getDtrData()">Load</button>
    </div>
    <div class="table"></div>
</div>

<script>
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
    $(function(){
        // styling for drawer
        let $drawer = $(".drawer"); 
        $drawer.css({
            // 'border': "1px solid black",
            'background' : "rgb(30, 98, 223)",
            'border-radius': '20px',
            'width' : "20%",
            'height': 'auto',
            'padding': '10px',
            'margin': '0px 20px 20px 0px',
            'float' : 'left'
        });
        $drawer.children().css({
            'color' : 'white'
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
            'color' : 'black'
        })
        $drawer.children('select').children("option").css({
            'background-color': 'white',
            'color': 'black'
        })
        $('#btn-load').css({
           'margin-top' : "15px",
           'width' : '100%' 
        });

        // styling for table
        let $table = $(".table"); 
        $table.css({
            'border-radius' : '20px',
            // 'margin': '0px 20px',
            'width' : '75%',
            'height': 'auto',
            'float': 'left'
        });
        


        for(let i=0; i<months.length; ++i){
            let $monthOption = $("<option>");
            $monthOption.val((i));
            $monthOption.text(months[i]);
            $("select#month").append($monthOption);
        }

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

    /* Loads the DTR data from the selected settings... */
    const getDtrData = _=>{
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

        console.log(loadMethod);

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
                
                console.log(data);

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
                    $newHeaderRow.append($newHeaderData);
                    $table.append($newHeaderRow);

                    let records = data[ws].wsRecords;
                    if(records.length <= 0){
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
                        $dateHeader = $newHeaderDataBase.clone();
                        $schedInHeader = $newHeaderDataBase.clone();
                        $schedOutHeader = $newHeaderDataBase.clone();
                        $timeInHeader = $newHeaderDataBase.clone();
                        $timeOutHeader = $newHeaderDataBase.clone();
                        $lateHeader = $newHeaderDataBase.clone();
                        $undertimeHeader = $newHeaderDataBase.clone();
                        $totalHeader = $newHeaderDataBase.clone();

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

                        $table.append($newHeaderRow);
                        for(x in records){
                            $newHeaderRow = $row.clone();
                            $newDataBase = $data.clone();
                            $newDataBase.css({
                                'font-size' : '12px'
                            });

                            // for each record data cell
                            $dateData = $newDataBase.clone();
                            $schedInData = $newDataBase.clone();
                            $schedOutData = $newDataBase.clone();
                            $timeInData = $newDataBase.clone();
                            $timeOutData = $newDataBase.clone();
                            $lateData = $newDataBase.clone();
                            $undertimeData = $newDataBase.clone();
                            $totalData = $newDataBase.clone();
                            
                            $dateData.addClass('table-flat-data');
                            $schedInData.addClass('table-flat-data');
                            $schedOutData.addClass('table-flat-data');
                            $timeInData.addClass('table-flat-data');
                            $timeOutData.addClass('table-flat-data');
                            $lateData.addClass('table-flat-data');
                            $undertimeData.addClass('table-flat-data');
                            $totalData.addClass('table-flat-data');

                            
                            let dateString = "";
                            let timeInString = "";
                            let timeOutString = "";

                            let recordId = records[x].record_id;
                            let recordDate = new Date(records[x].recorddate.date);
                            let timeIn = (tin = records[x].timeIn)? new Date(tin.date):null;
                            let timeOut = (tout = records[x].timeOut)? new Date(tout.date):null;
                            let hoursRendered = records[x].hoursRendered;

                            dateString = months[recordDate.getMonth()] + " " + recordDate.getDate() + ", " + recordDate.getFullYear();
                            timeInString = format12HourTime(timeIn);
                            timeOutString = format12HourTime(timeOut);
                            

                            $dateData.text(dateString);
                            $timeInData.text(timeInString);
                            $timeOutData.text(timeOutString);
                            $lateData.text(records[x].late);
                            $undertimeData.text(records[x].undertime);
                            $totalData.text(hoursRendered + " Hour(s)");

                            // On this part we get to decide what we will display
                            // on the schedule-in and -out cells in our DTR entry rows
                            // based on what the user chooses.
                            switch(loadMethod){
                                case 'load-method=manual':
                                    // console.log("MANUAL");
                                    break;

                                case 'load-method=auto':   
                                default:
                                    // console.log("AUTO");
                                    let scheduleFor = records[x].schedule;
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

                            $newHeaderRow.attr("id",recordId);

                            $newHeaderRow.append($dateData);
                            $newHeaderRow.append($schedInData);
                            $newHeaderRow.append($schedOutData);
                            $newHeaderRow.append($timeInData);
                            $newHeaderRow.append($timeOutData);
                            $newHeaderRow.append($lateData);
                            $newHeaderRow.append($undertimeData);
                            $newHeaderRow.append($totalData);

                            $table.append($newHeaderRow);

                        }
                    }
                }
                $('div.table').append($table);

            },
            error: function(err){
                console.log('Is Error');
                console.log(err.responseText);
            }
        });


    };
</script>