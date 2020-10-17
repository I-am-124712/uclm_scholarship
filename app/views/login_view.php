<div id="bg-img">
<div class="modal-overlay" id="overlay-add-ws">
    <div class="modal-panel" id="panel-login">
        <div class="logo-panel">
        <div id="form-panel">
            <h1 class="modal-header" id="label-title" style="font-size:24px">
                    UCLM WORKING SCHOLARS APP</h1>
            <h1 class="modal-subheader" id="label-deptname">LOGIN</h1>
            <form id="ws-info-form" action=<?= $_GET['url']?> method="POST">
                    <label id="form-label" for="username">Username</label><br>
                    <input class="textbox" type="text" name="username" id="username">
                    <label id="form-label" for="password">Password</label><br>
                    <input class="textbox" type="password" name="password" id="password">
                <div class="container">
                    <button class="button-solid round" id="form-button" type="submit" name="login" value="true">Login</button>
                </div>
            </form>
            <button class="button-flashing round" id="form-button-transparent" onclick="window.location.href='/uclm_scholarship/login/guest';">Enter as Guest</button>
            <div id="message-pane">
                <?= isset($_SESSION['prompt'])? $_SESSION['prompt']:Messages::dump('prompt'); ?>
            </div>
        </div>
    </div>
</div>