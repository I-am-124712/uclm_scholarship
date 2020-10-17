<?php
class Messages {

    private static $message = [];

    /**
     * Pushes an associative array of messages having the message identifier as key.
     */
    public static function push($msg = []){
        if($msg !== null){
            Messages::$message = array_merge(Messages::$message,$msg);
        }
    }

    /**
     * Prepares the saved messages everytime a session is started.
     * Should be called anytime a session is started (i.e after every session_start())
     */
    public static function prepare(){
        if(Messages::$message !== null){
            foreach(Messages::$message as $k => $v){
                $_SESSION[$k] = $v;
            }
        }
    }

    /**
     * Updates an existing message with the given key in the provided assoc array argument.
     */
    public static function update($msg = []){
        
        if($msg !== []){
            foreach($msg as $k => $v){
                if(isset(Messages::$message[$k]))
                    Messages::$message[$k] = $v;
                else
                    Messages::push($msg);
            }
        }
    }

    /**
     * Dumps the message associated by the given argument.
     * @param $arg the message key to be dumped.
     */
    public static function dump($arg = ''){
        if($arg == null || $arg === '')
            return Messages::$message;
        return Messages::$message !== null &&
               isset(Messages::$message[$arg])? 
                    Messages::$message[$arg] : '';
    }
}
Messages::push([
    'ok' => 'Okay'
]);
// Messages::push([
//     'ok' => 'Okay',
//     'err_idnum' => 'Wrong ID Number',
//     'err_lname' => 'First Name Not Allowed',
//     'err_fname' => 'Last Name Not Allowed',
//     'err_date' => 'Date Not Allowed'
// ]);

// var_export(json_encode(Messages::dump()));