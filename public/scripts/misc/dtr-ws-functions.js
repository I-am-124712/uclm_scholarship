let dtrJSONData = null;

const getDtrDataWS = _=>{

    let school_year = $('select#school-year').serialize();
    let semester = $('select#semester').serialize();
    let hide = $('input#hide').serialize();
    let period = $('select#period').serialize();
    let month = $('select#month').serialize();

    let params = school_year + '&'
                + semester + '&'
                + hide + '&'
                + period + '&'
                + month + '&'
                + 'req';
    
    console.log(params);
    $.get({
        url: '/uclm_scholarship/records/my_dtr',
        data: params,
        dataType: 'JSON',
        success: function(res){
            $('div.table').text('');

            // These will be refactored, I Promise.
            // Here's a smiley 😅.

            let $table = $('<table>');
            let $row = $('<tr>');
            let $data = $('<td>');

            dtrJSONData = res;

            let $newHeaderRow = $row.clone();
            let $newHeaderData = $data.clone();
            $newHeaderData.addClass('table-flat-header');
            $newHeaderData.attr('colspan', 10);
            $newHeaderData.css({
                'border-top-left-radius': '20px',
                'border-top-right-radius': '20px',
                'font-size' : '20px'
            });

            $newHeaderData.text(res.idnumber + " - " + res.wsName);
            $newHeaderRow.append($newHeaderData);
            $table.append($newHeaderRow);


            let wsRecords = res.wsRecords;

            if(wsRecords.length <= 0){
                // fix the table's width to not compress its contents
                $('div.table').css({
                    'width' : "60%"
                })
                $table.css({
                    'width' : "100%"
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
            else {
                // automatically adjust table width
                $('div.table').css({
                    'width' : "75%"
                })
                $newHeaderRow = $row.clone();
                $newHeaderDataBase = $data.clone();
                $newHeaderDataBase.addClass('table-flat-data');
                $newHeaderDataBase.css({
                    'background-color' : 'rgb(40, 40, 40)',
                    // 'color' : 'rgb(255, 115, 0)',
                    'font-weight': 'bold',
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


                    // Add class for styling...
                    $dateData.addClass('table-flat-data');
                    $schedInData.addClass('table-flat-data');
                    $schedOutData.addClass('table-flat-data');
                    $timeInData.addClass('table-flat-data');
                    $timeOutData.addClass('table-flat-data');
                    $lateData.addClass('table-flat-data');
                    $undertimeData.addClass('table-flat-data');
                    $totalData.addClass('table-flat-data');

                    // Add ids for each tds
                    $dateData.attr('id','date');
                    $schedInData.attr('id','sched-in');
                    $schedOutData.attr('id','sched-out');
                    $timeInData.attr('id','time-in');
                    $timeOutData.attr('id','time-out');
                    $lateData.attr('id','late');
                    $undertimeData.attr('id','undertime');
                    $totalData.attr('id','totalData');

                    
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

                    if(scheduleFor.length > 0){
                        let schedIn = "";
                        let schedOut = "";
                        for(i in scheduleFor){
                            schedIn += format12HourTime(new Date(scheduleFor[i].schedIn.date)) + "<br>";
                            schedOut += format12HourTime(new Date(scheduleFor[i].schedOut.date)) + "<br>";
                        }
                        $schedInData.html(schedIn);
                        $schedOutData.html(schedOut);
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

                    $table.append($newDataRow);

                }
            }
            $('div.table').append($table);
        }
    });
}


/**
 * Sends a request to generate a pdf and opens it in a new tab.
 */
const generatePDFWS = () => {

    let data = [];
    data.push(dtrJSONData);

    let schoolYears = $('select#school-year').val().split("-");

    let period = $('select#period option:selected').text();
    let month = $('select#month').val();
    let monthName = months[month];
    let schoolYear = month <= 5 ? schoolYears[1] : schoolYears[0];

    let periodMonth = period.toUpperCase() + " OF " + monthName.toUpperCase() + " " + schoolYear;
    
    generatePDF(periodMonth, data);
};