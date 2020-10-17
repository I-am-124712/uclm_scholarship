<?php
class Register extends Controller {

    private $salt = '7ba85c1ef9b655e2';

    public function index(){
        if(isset($_POST['register']) && $_POST['register'] === 'true'){
            $user_id = isset($_POST['user_id']) ? $_POST['user_id']:'';
            $username = isset($_POST['username']) ? $_POST['username']:'';
            $password = isset($_POST['password']) ? $_POST['password']:'';
            $user_privilege = isset($_POST['user_privilege']) ? $_POST['user_privilege']:'0';
            $user_lname = isset($_POST['user_lname']) ? $_POST['user_lname']:'';
            $user_fname = isset($_POST['user_fname']) ? $_POST['user_fname']:'';
    
            $user_fields = [
                'user_id' => $user_id,
                'username' => $username,
                'password' => md5($this->salt.$password),
                'user_privilege' => $user_privilege + 0,
                'user_lname' => $user_lname,
                'user_fname' => $user_fname,
                'logic' => 'OR'
            ];
    
            $validation_object = $this->model('User');
    
    
            $ok_count = 0;
    
    
            $id_num_validate = $validation_object
            ->ready()->find()->where(['user_id' => $user_fields['user_id']])->go();
            
            
            $username_validate = $validation_object
            ->ready()->find()->where(['username' => $user_fields['username']])->go();
    
            /// Check if User ID has already been registered
            if(!empty($id_num_validate))
                Messages::push([
                    'dup_id' => 'Found similar User ID Registered'
                ]);
            else 
                $ok_count++;
    
            /// check the same for the username... 
            if(!empty($username_validate))
                Messages::push([
                    'dup_username' => 'Found similar Username registered'
                ]);
            else 
                $ok_count++;
    
    
            if($ok_count >= 2){
                $ok_count = 0;
                $this->model('User')
                ->ready()
                ->create($user_fields)
                ->insert()
                ->go();
                
                Messages::push([
                    'prompt' => 'Successfully Registered'
                ]);
            }else{
                Messages::push([
                    'prompt' => 'Failed to Register'
                ]);
            }
        }
        return $this->view('register');
    }   

}