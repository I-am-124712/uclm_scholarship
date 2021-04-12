/**
 * Inserts a new row into our Overtime Entries table.
 */
 const addOvertimeEntry = ()=>{
    let tr = $("<tr>");
    let td = $("<td>");
    let select = $("<input>");
    let button = $("<button>");

    // New row to insert
    let row = tr.clone();

    // Cells for the current row
    let dateCell = td.clone();
    let startTimeCell = td.clone();
    let endTimeCell = td.clone();
    let totalTimeCell = td.clone();
    let actionCell = td.clone();
    
    // select boxes
    let dateInput = select.clone();
    let startInput = select.clone();
    let endInput = select.clone();

    // Remove row button
    let removeButton = button.clone();

    // add classes and attributes
    row.attr('id', 'overtime-entry-row');

    dateCell.addClass("table-flat-data");
    startTimeCell.addClass("table-flat-data");
    endTimeCell.addClass("table-flat-data");
    totalTimeCell.addClass("table-flat-data");
    actionCell.addClass("table-flat-data");

    removeButton.css({
        'background-color': 'inherit',
        'border-radius': '20px',
        'color' : 'white',
        'font-family': '"Impact"',
        'font-size' : '20px',
        'font-weight' : 'bold',
        'cursor': 'pointer',
        'border': 'unset',
        'transition-duration': '0.3s',
        'width' : '40px',
        'height' : '40px',
    });
    removeButton.text("⨉");
    removeButton.hover(()=>{
        removeButton.css({
            'background-color': 'red',
        })
    }, ()=>{
        removeButton.css({
            'background-color': 'inherit',
        })
    });
    removeButton.attr("onclick", "$(this).parent().parent().remove()");
    removeButton.attr('title', 'Remove Row');

    dateInput.addClass('textbox');
    startInput.addClass('textbox');
    endInput.addClass('textbox');

    dateInput.attr('type', 'date');
    startInput.attr('type', 'time');
    endInput.attr('type', 'time');

    dateInput.attr('id', 'ot-date');
    startInput.attr('id', 'ot-start');
    endInput.attr('id', 'ot-end');
    
    dateInput.attr('name', 'ot-date');
    startInput.attr('name', 'ot-start');
    endInput.attr('name', 'ot-end');

    totalTimeCell.attr('id', 'ot-total');

    totalTimeCell.css({
        'width' : '100px'
    })

    // add event handlers
    startInput.on('input', computeTotalOvertime);
    endInput.on('input', computeTotalOvertime);

    // add each elements to their desired containers
    dateCell.append(dateInput);
    startTimeCell.append(startInput);
    endTimeCell.append(endInput);
    actionCell.append(removeButton);

    row.append(dateCell);
    row.append(startTimeCell);
    row.append(endTimeCell);
    row.append(totalTimeCell);
    row.append(actionCell);

    $('table#overtime-table').find('tr#add-btn-row').before(row);
}

/**
 * Compute our total overtime using a backend defined formula of the same name.
 */
const computeTotalOvertime = function(){
    let row = $(this).parent().parent();
    let outputCell = row.find('td#ot-total');

    let overtimeStart = row.find('td input#ot-start').val();
    let overtimeEnd = row.find('td input#ot-end').val();

    let params = 'ot-start=' + overtimeStart + '&ot-end=' + overtimeEnd;

    $.ajax({
        method: 'post',
        url: '/uclm_scholarship/utilities/computeTotalOvertime',
        data: params,
        dataType: 'JSON',
        success: res => {
            outputCell.text(res.totalOvertime);
        },
        error: err => {

        }
    })
}


/**
 * Submit our overtime request to the server.
 */
const submitOvertime = () => {

    let idnumber = $('span#idnumber').text();

    let requestObject = {
        idnumber: idnumber,
        overtimeEntries : []
    };

    $('table#overtime-table').find('tr#overtime-entry-row').each(function(){
        let overtimeDate = $(this).find('input#ot-date').val();
        let overtimeStart = $(this).find('input#ot-start').val();
        let overtimeEnd = $(this).find('input#ot-end').val();
        let overtimeTotal = $(this).find('td#ot-total').text();

        requestObject.overtimeEntries.push({
            overtimeDate: overtimeDate,
            overtimeStart: overtimeStart,
            overtimeEnd: overtimeEnd,
            overtimeTotal: overtimeTotal
        });
    })

    console.log(requestObject);

    let params = 'req&overtime-data=' + JSON.stringify(requestObject);

    $.ajax({
        method: 'post',
        url: '/uclm_scholarship/records/saveOvertime',
        data: params,
        dataType: 'json',
        success: res => {
            console.log(res);
            window.location.reload();
        },
        error: err => {
            console.log("ERROR");
            console.log(err.responseText);
        }
    })
}

/**
 * Clears our request form
 */
const clearRequestForm = ()=>{
    $('table#overtime-table').find('tr#overtime-entry-row').remove();
}