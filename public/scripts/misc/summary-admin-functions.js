$(()=>{

    // styling for drawer
    let $drawer = $(".drawer"); 
    $drawer.css({
        'background' : "rgb(0, 64, 184)",
        'border-radius': '20px',
        'width' : "225px",
        'height': 'auto',
        'padding': '10px',
        'margin': '0px 20px 20px 0px',
        'float' : 'left',
        'display' : 'block',
        // 'position' : 'fixed'
    });
    $drawer.children().css({
        'color' : 'white',
        'font-size': '14px'
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
        'color' : 'black',
        'font-size': '14px'
    })
    $drawer.children('select').children("option").css({
        'background-color': 'white',
        'color': 'black'
    })
    $('#btn-load').css({
        'margin-top' : "15px",
        'width' : '100%' 
    });
    // initialize custom style for Save and PDF button
    $("button#btn-save").css({
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
    // For PDF button:
    $("button#btn-pdf").css({
        'width' : '100%',
        'margin-top': '10px',
        'float' : 'left',
        'background-color' : 'inherit'
    });
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
    // Fill the Months combo box...
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
            update();
        },
        error: function(err){
            console.log(err);
        }
    }); 

    // Styling for our div for the Table
    $('div.table').css({
        'border-radius': '20px',
        'box-shadow':'3px 3px 5px rgba(0,0,0,0.1)',
        'float':'left',
        'width': '75%',
        'height': 'auto',
        'margin':'10px',
    });

    $('select#school-year').change(()=>{
        update();
    });
    $('select#department').change(()=>{
        update();
    });
    $('select#period').change(()=>{
        update();
    });
    $('select#month').change(()=>{
        update();
    });

    prepareTable();

    update();

});

var titlePanel = null;
var tableMaker = new MinimalistTable();
var table = null;

/**
 * Prepares our Summary table by adding the appropriate headers.
 */
const prepareTable = ()=>{
    tableMaker.addHeader({
        wsName : "WS Name",
        grossHours: "Duty Hours",
        overtime: "Overtimes",
        lates: "Lates",
        undertimes: "Undertimes",
        totalHours: "Total Hours"
    });
};

const showTable = _=>{

    table = tableMaker.prepare().getTable();

    table.insertAfter('div#summary-title');


    $("div#for-table-panel").html(table);
}

/**
 * Wrapper method for loading both the Summary title panel
 * and our Summary Table.
 */
const update = ()=>{
    updateTitlePanel();
    loadSummary();
}

/** 
 * Updates the Summary Title Panel
*/
const updateTitlePanel = ()=> {
        
    if(titlePanel == null)
        titlePanel = new SummaryTitlePanel();

    let schoolYear = $("select#school-year").val();
    let department = $("select#department option:selected").text();
    let period = $("select#period").val();
    let month = $("select#month").val();

    titlePanel.set({
        schoolYearText: schoolYear,
        periodIndex : period,
        monthIndex : month,
        departmentNameText: department
    }).prepare();

    $('div#for-title-panel').html(titlePanel.getSummaryTitlePanel());
};


/** 
 * Loads the WS Allowance Summary data as table.
*/
const loadSummary = ()=>{
    let schoolYear = $('select#school-year').serialize();
    let period = $('select#period').serialize();
    let month = $('select#month').serialize();
    let department = $('select#department').serialize();

    let params = schoolYear + "&"
                + period + "&"
                + month + "&"
                + department;

    console.log(params);

    // perform a request to retrieve DTR Summary
    $.ajax({
        url: "/uclm_scholarship/records/loadSummary",
        type: 'post',
        data: params,
        dataType: 'JSON',
        success: res => {
            console.log("Success:");
            console.log(res);

            tableMaker.resetDataRows();

            for(idnumber in res){
                tableMaker.addDataRow({
                    wsName : res[idnumber].wsName,
                    grossHours : res[idnumber].gross_hours,
                    overtime : 0,
                    lates : res[idnumber].lates,
                    undertimes : res[idnumber].undertimes,
                    hoursRendered : res[idnumber].hours_rendered
                }, idnumber);
            }
            showTable();
        },
        error: err => {
            console.log("Error:");
            console.log(err.responseText);
        }
    });
}

/**
 * Performs a request to save the backend-loaded summary into the database.
 */
const saveSummary = () => {
    $('tr#header-row').siblings().each(function(){
        
        let idnumber = "idnumber=" + $(this).attr("id");
        let schoolYear = $('select#school-year').serialize();
        let period = $('select#period').serialize();
        let month = $('select#month').serialize();
        let grossHours = "grossHours=" + $(this).find('td#grossHours').text();
        let lates = "lates=" + $(this).find('td#lates').text();
        let undertimes = "undertimes=" + $(this).find('td#undertimes').text();
        let hoursRendered = "hoursRendered=" + $(this).find('td#hoursRendered').text();

        let data = idnumber + "&"
                + schoolYear + "&"
                + period + "&"
                + month + "&"
                + grossHours + "&"
                + lates + "&"
                + undertimes + "&"
                + hoursRendered;

        console.log(data);
        
        $.post({
            url: '/uclm_scholarship/records/saveSummary',
            data: data,
            dataType: 'JSON',
            success: res => {
                console.log(res);
                $(this).children().css({
                    'background-color': 'rgb(0,100,0)',
                    'color' : 'white'
                })
            },
            error: err => {
                console.log(err.responseText);
                $(this).children().css({
                    'background-color': 'rgb(100,0,0)',
                    'color' : 'white'
                })
            }
        })
    });
}

/**
 * Request a Generated PDF for our 
 */
const generateSummaryPDF = ()=>{


}