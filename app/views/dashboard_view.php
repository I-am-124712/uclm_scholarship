<?php require './app/views/user_view.php'; ?>

<div class="app-dash-panel" id="dashboard-panel">

    <div class="form-flat" style="width:inherit;
                                  padding:20px;
                                  border-radius:20px;
                                  font-size:25px">
        <b>DASHBOARD</b>
    </div>
    <?php
        switch($_SESSION['user_privilege']){
            case 999:
            case 1:
            case 2:
                require_once './app/views/dashboard-privileged_view.php';
                break;
            case 3:
            default:
                require_once './app/views/dashboard-ws_view.php';
                
        }
    ?>

</div>