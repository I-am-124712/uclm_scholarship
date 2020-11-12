<!-- Main User Page. This will contain the different options for the different -->
<!-- types of users for this app.    -->
<body onload="prepareSidebar()">
    
<div class="app-main">
    <div class="app-header">
        <button class="button-flashing round" id="hamburger1">≡</button>
        <div style="margin:5px;font-size:20px;margin-left:10px;float:left">
            <b>WORKING SCHOLARS APP</b>
        </div>
        <!-- For Notifications -->
        <div class="wrapper" style="float:right; padding-right: 20px">
            <div class="notif-wrapper">
                <!-- Messages Icon -->
                <div class="navbar-icon">
                    <div class="icon-container" id="message-icon"></div>
                    <div class="tooltip-text">Messages</div>
                </div>
                <!-- Notification Icon -->
                <div class="navbar-icon">
                    <div class="icon-container" id="notif-icon"></div>
                    <div class="tooltip-text">Notifications</div>
                </div>
                <!-- User Icon -->
                <div class="navbar-panel">
                    <div class="icon-container" style="background-image:url('<?=$_SESSION['user_photo']?>')"></div>
                    <div id="user-name"><?=$_SESSION['username']?></div>
                </div>

                <!-- Dropdown for Messages and Notifications -->
                <div class="dropdown">

                    <span class="drop-title"></span>
                    <!-- Item -->
                    <div class="drop-strip">
                        <div class="drop-img">
                            <div class="drop-profile"></div>
                        </div>
                        <div class="drop-info">
                            <p>(WS) Relador, Ravin Louis added a concern</p>
                            <span class="drop-time">19 minutes ago</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="app-sidebar-panel" id="sidebar"><?php require 'sidebar.php'; ?></div>

<script src="/uclm_scholarship/public/scripts/misc/navbar-functions.js"></script>