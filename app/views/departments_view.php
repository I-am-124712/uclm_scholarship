<?php require './app/views/user_view.php'; ?>

<div class="app-dash-panel" id="dashboard-panel">
    <div class="form-flat" style="width:450px;height:auto; float:left;">
        <div style="color:rgb(255,115,0);font-size:30px;padding-left:25px;padding-top:5px;padding-bottom:5px">
                <b>SELECT A DEPARTMENT</b>
        </div>
        <div class="form-flat" id="for-list-container">
            <div style="width:100%">
                <span>Search</span>
                <input class="textbox-transparent" type="text" name="search" id="search">
                <button class="button-solid round" id="search-btn">Go</button>
            </div>
            <div class="form-flat" id="for-list"></div>
        </div>
    </div>
    <div id="for-ws-view" style="width:50%; 
                                height: auto;
                                float:left;
                                margin:10px;
                                padding: 0px">
    </div>
</div>
<?php require './app/views/popups_view.php'; ?>

<script>
    $(function(){
        // take all the information we need for the Departments
        const paginateDepartments = ()=>{
            let jsonArray = JSON.parse('<?= str_replace("'","\'",json_encode($args)) ?>');

            $("#for-list").append('<ul id="departments-list"></ul>');
            $("#search").css({
                "width" : "50%",
                "height" : "25px",
                "margin" : "5px"
            })
            $("#search-btn").css({
                "float" : "right",
                "width" : "120px",
                "margin-top" : "5px"
            })
            $("div#for-list-container").css({
                "width" : "100%",
                "height": "inherit",
                "float" : "left",
                "margin": "10px",
                "padding": "10px"
            })
            $("div#for-list").css({
                "width" : "100%",
                "float" : "left",
                "box-shadow" : "none"
            })
            $("ul#departments-list").css({
                "width" : "100%",
                "float" : "left"
            })
            let len = jsonArray.length;
            for(let i=0; i< len; ++i){

                let deptId = jsonArray[i].deptId;
                let departmentName = jsonArray[i].departmentName;
                let wsCount = jsonArray[i].wsCount;
                
                $("#departments-list").append('<li onclick="location.href = \'/uclm_scholarship/dash/ws?allow_edit&department=\' '
                                                + '+ this.value" value=' + deptId 
                                                + ' id="' + deptId
                                                + '"></li>');
                                    
                // add the content
                $("li#"+deptId).append('<div id="deptname-'+deptId+'"></div>');
                $("li#"+deptId).append('<div id="wsCount-'+deptId+'"></div>');

                // add mouseEnter and mouseLeave handler. We need to partially show
                // the Working Scholars beside the departments list.
                $('li#'+deptId).mouseenter(function(){
                    deptId = $(this).attr("id");
                    url = '/uclm_scholarship/dash/ws_view_grouped?';
                    params = 'department=' + deptId;
                    $("div#for-ws-view").load(url+params + " div#ws-table-panel");
                }).mouseleave(function(){
                    $("div#ws-table-panel").delay(5000).fadeOut(100,function(){
                        $("div#for-ws-view").text('');
                    });
                });

                // add text
                $("div#deptname-" + deptId).text(departmentName);
                $("div#wsCount-" + deptId).text(wsCount + " Working Scholars");

                $("li#"+deptId).css({
                    'list-style': 'none'
                })
                $("li#"+deptId).addClass("button-outline");

                $("div#deptname-" + deptId).css({
                    "color" : "inherit",
                    "font-size" : "inherit",
                    "float" : "left",
                    "margin" : "5px"
                });
                $("div#wsCount-" + deptId).css({
                    "color" : "white",
                    "font-size" : "12px",
                    'font-weight' : 'normal',
                    "float" : "right",
                    "margin" : "5px"
                });
            }
            $("#departments-list").JPaging({
                visiblePageSize : 5
            });
        };
        paginateDepartments();

    })
</script>