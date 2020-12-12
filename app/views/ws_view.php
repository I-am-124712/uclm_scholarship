
<?php
    $allowed_edit_user_detected = false;
    $allowed_edit_ajax_request = $args['allow_edit'];
    if(isset($_SESSION['user_id']) && isset($_SESSION['user_privilege'])){
        if($_SESSION['user_privilege'] == 999 ||
            $_SESSION['user_privilege'] == 1 ||
            $_SESSION['user_privilege'] == 2) 
            $allowed_edit = true;
        if(isset($args['allow_edit']))
            $allowed_edit_user_detected = $args['allow_edit'];
    }
    else {
        echo "Trying to access data without logging in.";
        return;
    }

    require './app/views/user_view.php'; 
?>

<div class="app-dash-panel" id="dashboard-panel">
    <div class="form-flat" style="width:auto;
                                height:30px;
                                padding:20px;
                                display:block;
                                border-radius:20px;
                                font-size:25px">
    
        <button class="button-solid round" id="back-button" 
                style="float:left"
                onclick="location.href='<?=isset($args['generalView'])? '/uclm_scholarship/dash':'/uclm_scholarship/dash/departments' ?>'">
                <?=isset($args['generalView'])? 'Back to Dashboard':'Back to Previous'?>
        </button>
        <div style="float:left; padding-left:20px"><b>WORKING SCHOLARS</b></div>
    </div>
    <div> 
        <div class="form-flat" id="ws-table-panel" style="padding:20px; margin-top: 0; border-radius:20px">
            <span class="table-title" id="dept-name-title" style="width:100%;">
                <b id="label-deptname">
                <?php 
                if(isset($args['depAssigned']))
                    echo strtoupper($this->model('Departments')
                                         ->ready()
                                         ->find()
                                         ->where(['deptId' => $args['depAssigned']])
                                         ->result_set(['index' => 0])
                                         ->get('departmentName'));
                else
                    echo "WORKING SCHOLARS : ". count($args['ws']) . ' as of '. date_format(new DateTime('now'), 'M d, Y');
                ?></b>
            </span>
            <?php 
            if($allowed_edit_user_detected &&
                $allowed_edit_ajax_request){?>
                <button class="button-solid half-round" id="table-button-add" name="go" value="true" onclick="addWorkingScholars(<?=$args['depAssigned']?>)">Add Working Scholar</button>
            <?php }?>
            <table style="width:100%" id="ws-table">
                <tr>
                    <th class="table-flat-header clickable" onclick="tableSort('ws-table',0)">
                        <b>ID Number</b>
                    </th>
                    <th class="table-flat-header clickable" style="text-align:left" onclick="tableSort('ws-table',1)">
                        <b>WS Name</b>
                    </th>
                    <th class="table-flat-header round-top-right">
                        <?php if($allowed_edit_user_detected &&
                                $allowed_edit_ajax_request) { ?>
                            <b>Actions</b>
                        <?php } ?>
                    </th>
                </tr>
            <?php
            if(!empty($args['ws'])){
                foreach($args['ws'] as $ws){ ?>

                <tr>
                    <td class="table-flat-data" id="td-short"> <?=$ws->get_fields()['idnumber']//"▮▮▮▮▮▮▮▮" //?> </td>
                    <td class="table-flat-data" id="td-long" style="font-size:24px;text-align:left"><b><?= utf8_encode($ws->get_fields()['wsName'])?></b></td>
                    <td class="table-flat-data" id="td-max">
                    <?php if($allowed_edit_user_detected &&
                            $allowed_edit_ajax_request) {?>
                        <button class="button-solid round" id="action-button-info" value="<?=$ws->get_fields()['idnumber']?>" 
                        onclick="location.href = '/uclm_scholarship/dash/ws_information/'+this.value">View Info</button>
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
    <button class="button-solid round" id="btn-back-to-top"></button>
</div>
<?php require './app/views/popups_view.php'; ?>

<script src="/uclm_scholarship/public/scripts/misc/ws-view-functions.js"></script>
<script>
    
/// For scrolling back to top of page
/// Retrieved from W3Schools.com

$(function(){

    $('#btn-back-to-top').css({
        'display': 'none',
        'border-radius' : '100%',
        'background-size':'cover',
        'background-repeat':'no-repeat',
        'background-image':'url("/uclm_scholarship/public/sources/icons/iconTop.png")',
        'bottom': '20px',
        'box-shadow': '0px 10px 20px rgba(0,0,0,0.8)',
        'left' : 'calc(50% - 25px)',
        'font-weight': "bold",
        'position' : 'fixed',
        'width' : '50px',
        'height' : '50px',
        'z-index' : '99',
    });

    const backToTop = src =>{
        // document.documentBody.scrollTop = 0;
        src.scrollTop = 0;
    }

    const scrollFunction = src =>{
        if (src.scrollTop >= 100 || src.scrollTop >= 100) {
            $('#btn-back-to-top').css({
                'display': 'block'
            });
        } else {
            $('#btn-back-to-top').css({
                'display': 'none'
            });
        }
    };

    $('#btn-back-to-top').click(()=>{
        backToTop(dashPanel);
    });

    const dashPanel = document.getElementById('dashboard-panel');
    dashPanel.onscroll = ()=>{
        scrollFunction(dashPanel);
    };
});

</script>