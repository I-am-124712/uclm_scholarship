<?php
require 'user_view.php';

switch($_SESSION['user_privilege']){
    case 999:
    case 1:
        require 'overtime-admin_view.php';
        break;
    case 2:
        require 'overtime-in-charge_view.php';
        break;
    case 3:
        require 'overtime-ws_view.php';
        break;
}