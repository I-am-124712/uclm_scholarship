<?php 
include 'user_view.php'; 

$request_status = isset($_GET['request_status'])? $_GET['request_status']:'all';

$status = [
    'all' => 'All Requests',
    'accepted' => 'Accepted',
    'rejected' => 'Rejected',
    'pending' => 'Pending'
];
?>

<div class="app-dash-panel">
    <div class="form-flat title">
        <b>WORKING SCHOLAR REQUESTS</b>
    </div>
        <div class="drawer">
            <!-- School Year -->
            <form action="/uclm_scholarship/dash/ws/requests">
                <div id="drawer-title">FILTER REQUESTS</div>
                <select name="request_status" id="request_status" class="textbox-transparent" style="border:none; background:white;">
                <?php 
                    foreach($status as $k => $v){ 
                        $selected = $requestStatus === $k ? 'selected':'';
                ?>
                    <option value=<?= $k ?> <?= $selected ?>><?= $v ?></option>
                <?php 
                    } 
                ?>
                </select>
                <button class="button-solid round" id="btn-load" type="submit">Load Entries</button>
            </form>
            <button class="button-solid round" id="btn-transparent" onclick="console.log('Imo mama release!')">Release Summary</button>
        </div>
</div>

<script src="/uclm_scholarship/public/scripts/misc/drawer-widget.js"></script>
<script>
    $('div#drawer-title').css({
        "font-size": "20px",
        "color":"rgb(30, 98, 223)",
        'font-weight':'bold',
    });
</script>