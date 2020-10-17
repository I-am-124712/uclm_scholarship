<?php
class Home extends Controller {

    public function index(){
        session_start();

        if(isset($_SESSION['guest_login'])){
            $_SESSION['user_id'] = rand(111,999);
            $_SESSION['user_privilege'] = 4;
            $_SESSION['user_photo'] = '/uclm_scholarship/public/sources/users/user_default.png';
            header('Location: /uclm_scholarship/dash');
        }


        if(isset($_SESSION['user_id'])){
            if($_SESSION['user_id'] !== '') {
                echo 'Entered validated user_id';
                header('Location: home/my');
            }
        }
        else 
            return $this->view('home');
    }

    public function my(){
        session_start();
        if(!isset($_SESSION['user_id'])){
            header('Location: /uclm_scholarship/home');
        }
        $current_user_priv = $_SESSION['user_privilege']+0;
        $user_privilege = $this->model('UserPrivilege')
        ->ready()
        ->find()
        ->where([
            'columns' => ['privilege_type'],
            'privilege_id' => $current_user_priv
        ])
        ->go()[0];
        $message = '<b>'.(isset($_SESSION['username'])?
                          $_SESSION['username']:'Visitor').'</b>';
        $message .= '<div style="font-size: 14px;"> ';
        $message .= isset($user_privilege)? $user_privilege->get_fields()['privilege_type']:"Website Guest";
        $message .= '</div>';
        
        $_SESSION['welcome'] = $message;

        header('Location: /uclm_scholarship/dash');

        // return $this->view('user',['welcome' => $message]);
    }

}