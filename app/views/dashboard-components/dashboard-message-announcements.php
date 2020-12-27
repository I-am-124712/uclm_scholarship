<?php
if($_SESSION['user_privilege'] == 999 || $_SESSION['user_privilege'] == 1)
    $hasHighPrivilege = true;
else
    $hasHighPrivilege = false;
?>

<div class="form-flat">
    <div class="chart-div">
        <div class="chart-title">Messages</div>
        <div id="message-panel" style="height:250px; overflow-y: scroll"></div>
    </div>
    <div class="chart-div">
        <div class="chart-title">
            <span style="float:left">Posts from Scholarship Office</span>
        <?php if($hasHighPrivilege) { // Show a create announcement button if user has high Admin privilege?>
            <style>
                #create-post{
                    float:right;
                    background-image: url('/uclm_scholarship/public/sources/icons/iconCreatePost.png');
                    background-position: center;
                    background-size: 25px;
                    background-repeat: no-repeat;
                    background-color: inherit;
                    width: 35px;
                    height: 35px;
                    margin-right: 10px;
                    transition-duration: 0.3s;
                }
                #create-post:hover {
                    background-image: url('/uclm_scholarship/public/sources/icons/iconCreatePostHover.png');
                    /* background-color: rgb(78, 140, 255); */
                }
            </style>
            <button class="button-flashing" 
                    id="create-post" 
                    title="Create Post"></button>
        <?php } ?>    
        </div>
        <div id="posts-panel" style="height:calc(100% - 40px); overflow-y: auto">
            <div style="padding-top:10%; color:gray; width:100%; height:auto; text-align:center">
                Loading Posts...
            </div>
        </div>
    </div>
</div>
<script src="/uclm_scholarship/public/scripts/misc/dashboard-index.js"></script>
<script src="/uclm_scholarship/public/scripts/misc/posts-updates.js"></script>