<?php require './app/views/user_view.php'; ?>

<div class="app-dash-panel" id="dashboard-panel">

    <div class="form-flat title">
        <b>DASHBOARD</b>
        <div style="float:right; 
                    text-align:right; 
                    color:white; 
                    font-size:20px; 
                    font-weight:bold; 
                    background-color:red;
                    padding:5px 20px;
                    border-radius:20px;
                    width:auto;" id="message-prompt" hidden></div>
    </div>
    <?php
        switch($_SESSION['user_privilege']){
            case 999:
            case 1:
                require_once './app/views/dashboard-privileged_view.php';
                break;
            case 2:
                require_once './app/views/dashboard-in-charge_view.php';
                break;
            case 3:
                require_once './app/views/dashboard-ws_view.php';
                break;
            case 85:
                require_once './app/views/dashboard-departmental_view.php';
        }
        
        include 'dashboard-components/dashboard-message-announcements.php'; 
    ?>

</div>