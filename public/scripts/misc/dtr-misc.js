
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
const generatePDF = (obj, dtrJSONData) => {

    // This object array will hold the data that we will be sending to our
    // post request.
    let dataObj = [];

    // Let us filter out what we will be sending to our request.

    for(x in data = dtrJSONData){
        dataObj.push({
            wsName: data[x].wsName,
            wsRecords : data[x].wsRecords
        });
    }

    let schoolYear = obj.schoolYear? `schoolYear=${obj.schoolYear}`:'';
    let period = obj.period? `&period=${obj.period}`:'';
    let month = obj.month? `&month=${obj.month}`:'';
    let department = obj.department? `&department=${obj.department}`:'';

    let param = `${schoolYear}
        ${period}
        ${month}
        ${department}
        &data=${JSON.stringify(dataObj)}`;


    $.ajax({
        url: '/uclm_scholarship/utilities/sendPost',
        type: 'post',
        dataType: "json",
        data: param,
        success: res => {
            console.log(res);

            if(!res.status){
                console.log("ERROR: POST failed");
                return;
            }
            if(res.status !== 'SUCCESS'){
                console.log(res.status);
                return;
            }

            console.log('SUCCESS. Opening...');
            window.open('/uclm_scholarship/utilities/generate/pdf/dtr', '_blank');
        }
    })
};