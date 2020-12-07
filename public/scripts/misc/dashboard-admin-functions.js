
$(function(){
    let chart1 = new CanvasJS.Chart('chart-panel',{
        title:{
            text: "(Test) WS Hires for the 1st Half of 2019",
        },

        data: [  //array of dataSeries 
        { //dataSeries - second quarter

        type: "column",
        name: "Actual Hires",
        showInLegend: true,
        dataPoints: [
        { label: "January", y: 2 },
        { label: "February", y: 5 },
        { label: "March", y: 7 },                                    
        { label: "April", y: 1 },
        { label: "May", y: 0 },
        { label: "June", y: 12 }
        ]
        },    
        { //dataSeries - first quarter
    /*** Change type "column" to "bar", "area", "line" or "pie"***/        
        type: "line",
        name: "WS Appilcants",
        showInLegend: true,
        dataPoints: [
        { label: "January", y: 5 },
        { label: "February", y: 5 },
        { label: "March", y: 10 },                                    
        { label: "April", y: 8 },
        { label: "May", y: 15 },
        { label: "June", y: 24 }
        ]
        }
        ]
    }),
    
    
    chart2 = new CanvasJS.Chart('chart-panel-2',{
        title:{
            text: "Performance Summary - First Half of 2020"              
        },
        axisY:{
            suffix: '%'
        },
        data: [  //array of dataSeries     
        { //dataSeries - first quarter
    /*** Change type "column" to "bar", "area", "line" or "pie"***/        
        type: "line",
        name: "Complete Duty Renders",
        showInLegend: true,
        dataPoints: [
        { label: "January", y: 58 },
        { label: "February", y: 69 },
        { label: "March", y: 80 },                                    
        { label: "April", y: 74 },                                    
        { label: "May", y: 74 },
        { label: "June", y: 70 }
        ]},
        { //dataSeries - second quarter
            type: "line",
            name: "w/ Lates",
            showInLegend: true,
            dataPoints: [
                { label: "January", y: 26 },
                { label: "February", y: 33 },
                { label: "March", y: 18 },                                    
                { label: "April", y: 19 },                                   
                { label: "May", y: 22 },
                { label: "June", y: 32 }
            ]
        },
        { //dataSeries - second quarter
            type: "line",
            name: "w/ Undertimes",
            showInLegend: true,
            dataPoints: [
                { label: "January", y: 12 },
                { label: "February", y: 27 },
                { label: "March", y: 4 },                                    
                { label: "April", y: 30 },                                   
                { label: "May", y: 16 },
                { label: "June", y: 19 }
            ]
        }
    ]}
    );

    chart1.render();
    chart2.render();

});


