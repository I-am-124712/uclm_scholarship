<?php require './app/views/user_view.php'; ?>

<script>
    window.onload = function(){
        let chart1 = new CanvasJS.Chart('chart-panel',{
            title:{
                text: "WS Hires for the 1st Half of 2019",
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
                text: "Lates and Undertime Per Duty"              
            },

            data: [  //array of dataSeries     
            { //dataSeries - first quarter
        /*** Change type "column" to "bar", "area", "line" or "pie"***/        
            type: "line",
            name: "First Quarter",
            dataPoints: [
            { label: "banana", y: 58 },
            { label: "orange", y: 69 },
            { label: "apple", y: 80 },                                    
            { label: "mango", y: 74 },
            { label: "grape", y: 64 }
            ]
            },
            { //dataSeries - second quarter

            type: "line",
            name: "Second Quarter",
            dataPoints: [
            { label: "banana", y: 63 },
            { label: "orange", y: 73 },
            { label: "apple", y: 88 },                                    
            { label: "mango", y: 77 },
            { label: "grape", y: 60 }
            ]
            }
            ]
        });

        chart1.render();
        chart2.render();
    };
</script>



<div class="app-dash-panel" id="dashboard-panel">

    <!-- <div>
        <button>
            <div>Working Scholars with Overtime</div>
            <div><b><?//=isset($args['ws_with_ot'])? $args['ws_with_ot']:''?> WSs</b></div>
        </button>
    </div> -->
    <div style="width:inherit;padding:20px;border-radius:20px;border:1px solid gray;font-size:25px">
        <b>DASHBOARD</b>
    </div>
    <div style="width:100%; height:fit-content;">
        <!-- For dash button top panel -->
        <div style="padding-bottom:30px;border:1px solid gray;border-radius:20px;margin-top:10px;margin-bottom:20px;padding:10px">
            <button class="button-dashboard">
                <div class="label-dash-btn">Number of Working Scholars</div>
                <div class="sub-dash-btn"><b><?=isset($args['no_of_ws'])? $args['no_of_ws']:''?> WS</b></div>
            </button>
            <button class="button-dashboard">
                <div class="label-dash-btn">Newly Hired Working Scholars</div>
                <div class="sub-dash-btn"><b><?=isset($args['ws_with_ot'])? $args['ws_with_ot']:''?> WS</b></div>
            </button>
            <button class="button-dashboard">
                <div class="label-dash-btn">Newly Hired Working Scholars</div>
                <div class="sub-dash-btn"><b><?=isset($args['ws_with_ot'])? $args['ws_with_ot']:''?> WS</b></div>
            </button>
            <button class="button-dashboard">
                <div class="label-dash-btn">Newly Hired Working Scholars</div>
                <div class="sub-dash-btn"><b><?=isset($args['ws_with_ot'])? $args['ws_with_ot']:''?> WS</b></div>
            </button>
        </div>
        <div class="chart-div">
            <div id="chart-panel" style="height:100%"></div>
        </div>
        <div class="chart-div">
            <div id="chart-panel-2" style="height:100%"></div>
        </div>

        <div class="list-div">
        </div>

    </div>

</div>
<?php require './app/views/popups_view.php'; ?>