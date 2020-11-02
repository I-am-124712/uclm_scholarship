
<?php
    $allowed_edit = false;
    if(isset($_SESSION['user_id']) && isset($_SESSION['user_privilege'])){
        if($_SESSION['user_privilege'] == 999 ||
            $_SESSION['user_privilege'] == 1 ||
            $_SESSION['user_privilege'] == 2) 
            $allowed_edit = true;
    }
    else {
        echo "Trying to access data without logging in.";
        return;
    }
?>

<div> 
    <div>
        <button class="button-solid round" id="back-button" onclick="location.href='/uclm_scholarship/dash/departments'">Back to Previous</button><br>
        <div class="table-title" id="dept-name-title">
            <b id="label-deptname"><?php echo strtoupper($this->model('Departments')->ready()->find()->where(['deptId' => $args['depAssigned']])->go()[0]->get_fields()['departmentName'])?></b>
        </div>
    </div><br>
    <div style="padding:10px;border-radius:20px;border:1px solid gray">
        <table style="width:100%" id="ws-table">
            <?php 
            if($allowed_edit){?>
                <button class="button-solid half-round" id="table-button-add" name="go" value="true" onclick="addWorkingScholars(<?=$args['depAssigned']?>)">Add Working Scholar</button>
            <?php }?>
            <tr>
                <th class="table-flat-header clickable" onclick="tableSort('ws-table',0)">
                    <b>ID Number</b>
                </th>
                <th class="table-flat-header clickable" style="text-align:left" onclick="tableSort('ws-table',1)">
                    <b>WS Name</b>
                </th>
                <th class="table-flat-header round-top-right">
                    <b>Actions</b>
                </th>
            </tr>
        <?php
        if(!empty($args['ws'])){
            foreach($args['ws'] as $ws){ ?>

            <tr>
                <td class="table-flat-data" id="td-short"> <?= $ws->get_fields()['idnumber']?> </td>
                <td class="table-flat-data" id="td-long" style="font-size:24px;text-align:left"><b><?= utf8_encode($ws->get_fields()['wsName'])?></b></td>
                <td class="table-flat-data" id="td-max">
                    <button class="button-solid round" id="action-button-info" value="<?=$ws->get_fields()['idnumber']?>" 
                    onclick="location.href = '/uclm_scholarship/dash/ws_information/'+this.value">View Info</button>
                <?php if($allowed_edit) {?>
                    <button class="button-flashing round" id="action-button-delete" value="<?=$ws->get_fields()['idnumber']?>" onclick="deleteWorkingScholar(this.value)">Delete</button> 
                </td>
            </tr>
        <?php   } }
        }
        ?>
        <tr>
            <td class="table-flat-footer" style="border-bottom-left-radius:20px"></td>
            <td class="table-flat-footer"></td>
            <td class="table-flat-footer" style="border-bottom-right-radius:20px"></td>
        </tr>
        </table>
    </div>
</div>


<script>
    const addWorkingScholars = (departmentId)=>{
        url = "/uclm_scholarship/working_scholars/add_ws/"+departmentId;
        form = $("#for-popups").load(url+" .modal-overlay");
        $("#for-popups").removeAttr("hidden");
        console.log(form);
    }
    const closeModal = ()=>{
        $("#for-popups").text("");
    };

    const save = ()=>{
        url = "/uclm_scholarship/working_scholars/add";
        params = $('form').serialize();
        console.log(params);

        // callback constants
        const success = (data)=>{
            console.log(data);

            $("#err-msg-idnum").text(data.err_idnum);
            $("#err-msg-lname").text(data.err_lname);
            $("#err-msg-fname").text(data.err_fname);
            $("#err-msg-course").text(data.err_course);

            if(data.success){
                console.log(data.success);
                closeModal();
                console.log("Next line I think causes the bug...");
                setTimeout(() => {
                    console.log("Waiting...");
                    location.href = '/uclm_scholarship/dash/ws';
                }, 100);
            }
        };
        const err = (data)=>{
            console.log(data);
        };

        // Time to add WS here.
        $.post({
            url: url,
            async: true,
            data: params,
            dataType: 'JSON',
            success: success,
            error : err
        });

    }
</script>