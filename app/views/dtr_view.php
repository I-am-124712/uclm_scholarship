<?php require './app/views/user_view.php'; ?>

<div class="app-dash-panel">
    <div class="drawer">

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
            'width' : '60%',
            'height': 'auto',
            'float': 'left'
        });
        

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

        for(let i=0; i<months.length; ++i){
            let $monthOption = $("<option>");
            $monthOption.val((i+1));
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
            }
        });

    });

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

        console.log(params);

        // here comes the request...
        $.ajax({
            type: 'post',
            url: '/uclm_scholarship/records/dtr',
            data: params,
            dataType: 'JSON',
            success: function(res){
                
            }
        })
    };
</script>