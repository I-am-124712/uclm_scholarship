<?php
require_once './app/core/Messages.php';

class Controller {
    
    public function model($model){
        
        require_once './app/models/ModelObjects.php';
        return new $model();
    }

    public function view($view,$args=[]){
        // include './index.html';
        require_once 'index.html';
        require_once './app/views/'.$view.'_view.php';
    }


    public function trap_no_user_session(){
        if(!isset($_SESSION['user_id']))
            header('location: /uclm_scholarship/home');
    }
}