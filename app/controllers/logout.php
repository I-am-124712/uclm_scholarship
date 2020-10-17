<?php

class Logout extends Controller {

    public function index(){
        session_start();

        // // for guest users, delete from database
        // if($_SESSION['user_privilege'] == 4){
        //     $this->model('User')
        //     ->ready()
        //     ->delete()
        //     ->where([
        //         'user_id' => $_SESSION['user_id'],
        //     ])
        //     ->go();
        // }


        unset($_SESSION['user_id']);
        unset($_SESSION['username']);
        unset($_SESSION['user_privilege']);
        unset($_SESSION['guest_login']);
        Messages::push([
            'prompt' => 'Successfully Logged Out'
        ]);
        Messages::prepare();
        header('Location: /uclm_scholarship/login');
    }
}