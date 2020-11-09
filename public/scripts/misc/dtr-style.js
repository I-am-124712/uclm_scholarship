
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
        'color' : 'black',
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
        'width' : 'auto',
        'height': 'auto',
        'float': 'left',
        'position' : 'auto'
    });

    // Fill the Monts combo box...
    for(let i=0; i<months.length; ++i){
        let $monthOption = $("<option>");
        $monthOption.val((i));
        $monthOption.text(months[i]);
        $("select#month").append($monthOption);
    }
})