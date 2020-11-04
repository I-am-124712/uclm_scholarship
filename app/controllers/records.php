<?php

class Records extends Controller {

    public function index(){
        session_start();
        $this->trap_no_user_session();
    }

    public function dtr(){
        session_start();
        $this->trap_no_user_session();
    }

    public function summary(){
        session_start();
        $this->trap_no_user_session();
    }

    public function overtime(){
        session_start();
        $this->trap_no_user_session();
    }
}