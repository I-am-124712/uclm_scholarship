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
        
        <label for="hide" id="form-label2" style="margin: 10px 0px; float:left; font-size:12px">
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
            'margin': '0px 20px',
            'width' : 'auto',
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

    });

    const format12HourTime = timeObj => {
        let timeHour;
        let timeMinute;
        let stringHour;
        let stringMinute;
        let timeFullString;
        if(timeObj == null)
            return 'NULL';

        timeHour = timeObj.getHours();
        timeMinute = timeObj.getMinutes();

        if(timeHour >= 12) {
            stringHour = "" + ((modTimeHour= 1 + timeHour % 12) < 10 ? '0'+modTimeHour:modTimeHour);
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
        let requested = "req";

        let params = schoolYear 
                + "&" + department 
                + "&" + semester 
                + "&" + period 
                + "&" + month
                + "&" + hide
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
                

                for(ws in data){
                    let $newHeaderRow = $row.clone();
                    let $newHeaderData = $data.clone();
                    $newHeaderData.addClass('table-flat-header');
                    $newHeaderData.attr('colspan', 4);
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
                        $newHeaderData.attr('colspan', 4);
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
                            'width' : "auto"
                        })
                        $newHeaderRow = $row.clone();
                        $newHeaderDataBase = $data.clone();
                        $newHeaderDataBase.addClass('table-flat-data');
                        $newHeaderDataBase.css({
                            'background-color' : 'rgb(255, 115, 0)'
                        });
                        $dateHeader = $newHeaderDataBase.clone();
                        $timeInHeader = $newHeaderDataBase.clone();
                        $timeOutHeader = $newHeaderDataBase.clone();
                        $totalHeader = $newHeaderDataBase.clone();

                        $dateHeader.text("Record Date");
                        $timeInHeader.text("Time-in");
                        $timeOutHeader.text("Time-out");
                        $totalHeader.text("Hours Rendered");

                        $newHeaderRow.append($dateHeader);
                        $newHeaderRow.append($timeInHeader);
                        $newHeaderRow.append($timeOutHeader);
                        $newHeaderRow.append($totalHeader);

                        $table.append($newHeaderRow);
                        for(x in records){
                            $newHeaderRow = $row.clone();
                            $newDataBase = $data.clone();

                            $dateData = $newDataBase.clone();
                            $timeInData = $newDataBase.clone();
                            $timeOutData = $newDataBase.clone();
                            $totalData = $newDataBase.clone();
                            
                            $dateData.addClass('table-flat-data');
                            $timeInData.addClass('table-flat-data');
                            $timeOutData.addClass('table-flat-data');
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
                            $totalData.text(hoursRendered + " Hour(s)");

                            $newHeaderRow.attr("id",recordId);

                            $newHeaderRow.append($dateData);
                            $newHeaderRow.append($timeInData);
                            $newHeaderRow.append($timeOutData);
                            $newHeaderRow.append($totalData);

                            $table.append($newHeaderRow);

                            // console.log(dateString + "\t" + timeInString + " - " + timeOutString +" Total: " + hoursRendered);
                        }
                    }
                }
                $('div.table').append($table);

            },
            error: function(err){
                console.log('Is Error');
                console.log(err.responseText);
            }
        })
    };
</script>