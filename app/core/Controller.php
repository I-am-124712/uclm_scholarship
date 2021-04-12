<?php
require_once './app/core/Messages.php';

class Controller {
    
    public function model($model){
        
        require_once './app/models/ModelObjects.php';
        return new $model();
    }

    public function view($view,$args=[]){
        return $this->view_custom('index.html', $view, $args);
    }
    
    public function view_custom($header, $view, $args=[]){
        // include './index.html';
        // require_once './app/views/html/footer.html'; 
        if($header !== "")
            require_once $header;
        require_once './app/views/'.$view.'_view.php';
    }


    public function trap_no_user_session(){
        if(!isset($_SESSION['user_id']))
            header('location: /uclm_scholarship/home');
    }
}