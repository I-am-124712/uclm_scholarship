
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

<?php require './app/views/user_view.php';  ?>

<div class="app-dash-panel">
    <div class="form-flat">
        <div style="margin:10px">
            <form action="/uclm_scholarship/dash/ws" method="post"></form>
            <button class="button-solid round" id="back-button" name="department" 
                    value=<?=isset($args['department'])? $args['department']->get_fields()['deptId']:''?> type="submit">
                Back to Previous
            </button><br>

        </div>
    </div>
    <div class="form-flat" style="width:50%">

    </div>
</div>

<?php require './app/views/popups_view.php'; ?>