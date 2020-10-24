
<div>
    <div class="user-panel">
        <div class="user-icon">
            <div class="user-photo" style="background-image: url('<?=$_SESSION['user_photo']?>');"></div>
        </div>
        <div class="user-info">
            <?=isset($_SESSION['welcome'])? $_SESSION['welcome']: ""?>
        </div>
    </div>
    <div class="user-side-buttons">
        <?php switch($_SESSION['user_privilege']) {
                    case 999:
                    case 1:
                    case 2:
                        require './app/views/html/sidebar_admin.html';
                        break;
                    case 3:
                        require './app/views/html/sidebar_ws.html';
                        break;
                    default:
                        break;
            }?>
            <div>
                <button class="button-flashing" id="app-sidebar-btn" onclick="location.href=''">
                    <div class="icon-panel">
                        <div class="icon" id="setting-icon"></div>
                    </div>
                    <div class="btn-label">Settings</div>
                </button>
            </div>
            <div>
                <button class="button-flashing" id="app-sidebar-btn" onclick="location.href=''">
                    <div class="icon-panel">
                        <div class="icon" id="message-icon"></div>
                    </div>
                    <div class="btn-label">Message</div>
                </button>
            </div>
            <div>
                <button class="button-flashing" id="app-sidebar-btn" onclick="location.href='/uclm_scholarship/logout';">
                    <div class="icon-panel">
                        <div class="icon" id="logout-icon"></div>
                    </div>
                    <div class="btn-label">Logout</div>
                </button>
            </div>
        <form id="dash-options-permit" action="" method="POST">
            <div hidden id="_appAction">4</div>
        </form>
    </div>
</div>
