<?php
class Login extends Controller {

    // private $salt = '7ba85c1ef9b655e2';

    public function index($params = ''){

        session_start();
        
        if(!isset($_SESSION['user_id'])){

            // sUcCesSfuLly loGgEd oUt message.
            Messages::push(['prompt' => isset($_SESSION['prompt'])?$_SESSION['prompt']:'']);
            unset($_SESSION['prompt']);


            if(isset($_POST['login']) && $_POST['login'] === 'true'){
                $username = $password = '';

                
                echo Messages::dump('prompt');
        

                // Here starts the checking. In case wala mo kahibaw HAHAHA
                if(isset($_POST['username'])){
                    $username = $_POST['username'];
                    unset($_POST['username']);
                }
                if(isset($_POST['password'])){
                    $password = $_POST['password'];
                    unset($_POST['password']);
                }
        
                if(($username === '' || $password === '')){
                    return $this->view('login');
                }
        
                $matched_user = $this->model('User')
                ->ready()
                ->find()
                ->where([
                    'username' => $username,
                    'password' => md5(SALT.$password)
                ])
                ->go();
                
                if($matched_user){
                    $_SESSION['user_id'] = $matched_user[0]->get_fields()['user_id'];
                    $_SESSION['username'] = $matched_user[0]->get_fields()['user_fname'].' '.$matched_user[0]->get_fields()['user_lname'];
                    $_SESSION['user_privilege'] = $matched_user[0]->get_fields()['user_privilege'];
                    $_SESSION['user_photo'] = $matched_user[0]->get_fields()['user_photo'];
                    header('Location: /uclm_scholarship/home',false);
                }
                else{
                    Messages::push([
                        'prompt' => 'Username or Password Incorrect'
                    ]);
                }
            }
            return $this->view('login');
        }
        else{
            if($_SESSION['user_id'] !== ''){
                // echo 'Entered if($_SESSION["user_id"] !== "") on login.php';
                header('Location: /uclm_scholarship/home',false);
            }
        }

        
    }

    public function guest(){
        session_start();
        $_SESSION['guest_login'] = true;
        header('Location: /uclm_scholarship/home');
    }

}

?>