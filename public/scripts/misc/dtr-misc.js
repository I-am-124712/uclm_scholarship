


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