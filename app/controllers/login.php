<?php
class Login extends Controller {

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
                    Messages::push([
                        'prompt' => 'Please fill all empty fields'
                    ]);
                    return $this->view('login');
                }
        
                $matched_user = $this->model('User')
                ->ready()
                ->find()
                ->where([
                    'username' => $username
                ])
                ->result_set([
                    'index' => 0
                ]);
                
                $storedPassword = $matched_user->get('password');
                $isMatch = password_verify($password, $storedPassword);
                
                if($isMatch){
                    // Log user session to our database.
                    $this->model('UserLogbook')
                    ->ready()
                    ->create([
                        'user_id'=> $matched_user->get('user_id'),
                        'login_datetime' => new DateTime('now')
                    ])
                    ->insert()
                    ->go();

                    // Set logged user session.
                    $_SESSION['user_id'] = $matched_user->get('user_id');
                    $_SESSION['username'] = $matched_user->get('user_fname').' '.$matched_user->get('user_lname');
                    $_SESSION['user_privilege'] = $matched_user->get('user_privilege');
                    $_SESSION['user_photo'] = $matched_user->get('user_photo');
                    $_SESSION['sidebar-visible'] = true;

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
                header('Location: /uclm_scholarship/home',false);
            }
        }

        
    }

    public function guest(){
        session_start();
        $_SESSION['guest_login'] = true;
        header('Location: /uclm_scholarship/home');
    }

    private function updatePasswords(){

        $custom = "SELECT * from [User] where user_id in('0x04569', '8256876')";
        
        $result = $this->model('Finder')
        ->ready()
        ->customSql($custom)
        ->result_set();

        $forQuery = [];

        foreach($result as $res) {
            $userId = $res->get('user_id');
            $newPass = str_replace(" ", "", utf8_encode($res->get("user_lname")));
            $hashed = password_hash($newPass, PASSWORD_DEFAULT);

            echo $userId . "<br>";
            echo $newPass . "<br>";
            echo $hashed. "<br>";

            $item = [ $hashed,$userId ];

            $sql = "UPDATE [User] SET password = ? where user_id = ?";
            $bindParams = $item;

            $this->model('Finder')
            ->ready()
            ->customSql($sql)
            ->setBindParams($bindParams)
            ->go();

            echo "<br>";
        }

    }

}

?>