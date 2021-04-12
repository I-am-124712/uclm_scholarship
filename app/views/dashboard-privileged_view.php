<div style="width:auto; height:fit-content;">

    <?php 
        include 'dashboard-components/dashboard-major-buttons-panel.php'; 
        if($_SESSION['user_privilege'] != 999)
            include 'dashboard-components/dashboard-ws-performance-panel.php'; 
        include 'dashboard-components/dashboard-statistics.php'; 
    ?>

</div>