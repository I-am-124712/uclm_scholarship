<?php require './app/views/user_view.php'; ?>

<div class="app-dash-panel" id="dashboard-panel">
    <div style="color:rgb(255,115,0);font-size:20px;padding-left:25px;padding-top:5px;padding-bottom:5px">
            <b>SELECT A DEPARTMENT</b>
        </div>
    <div style="width:fit-content;height:inherit; overflow-y:scroll;">
        <table class="table-flat" style="border-collapse:separate; width: 500px">
                <?php  
                if(isset($args))
                    foreach($args as $dept){
                ?>
                <form action="/uclm_scholarship/dash/ws" method="POST">
                    <tr>
                        <td>
                            <button class="button-solid round" id="clickable-table-row" 
                            action="submit" 
                            name="department"
                            value="<?=$dept->get_fields()['deptId']?>">
                                
                                <div style="text-align:left;font-size:20px;width:inherit">
                                    <b><?=$dept->get_fields()['departmentName']?></b>
                                </div>
                                <div style="text-align:left;font-size:15px;color:rgb(255, 130, 20);">
                                    <b><?=$dept->get_fields()['wsCount']?> Working Scholars</b>
                                </div>
                            </button>
                        </td>
                    </tr>
                </form>
                <?php } ?>
        </table>
    </div>
</div>
<?php require './app/views/popups_view.php'; ?>