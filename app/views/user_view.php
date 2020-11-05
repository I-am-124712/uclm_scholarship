<!-- Main User Page. This will contain the different options for the different -->
<!-- types of users for this app.    -->
<body onload="prepareSidebar()">
    
<div class="app-main">
    <div class="app-header">
        <button class="button-flashing round" id="hamburger1">≡</button>
        <div style="margin:5px;font-size:20px;margin-left:10px;float:left">
            <b>WORKING SCHOLARS APP</b>
        </div>
    </div>
    <div class="app-sidebar-panel" id="sidebar"><?php require 'sidebar.php'; ?></div>

<script>
    
    $("#hamburger1").click(function(){

        $(".app-sidebar-panel").toggle();

    });
</script>