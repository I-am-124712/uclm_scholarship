<?php require './app/views/user_view.php'; ?>

<div class="app-dash-panel">
    <div style="width:100%; height:fit-content; margin: 0px auto">
        <div class="form-flat title">
            <b>UPLOAD RAW DTR FILE</b>
        </div>
        <div class="form-flat">
            <?php if(isset($args['uploadSuccess'])) {?> 
                <b>DTR UPLOAD SUCCESSFUL!</b>
            <?php } else {?>
            <form action="/uclm_scholarship/records/uploadRaw" method="post" enctype="multipart/form-data">
                <input type="file" name="dat-file" id="dat-file" style="width:100%; padding:20px 10px">
                <input class="button-solid round" type="submit" name="submit" value="Upload DTR File">
            </form>
            <?php } ?>
        </div>
    </div>
</div>