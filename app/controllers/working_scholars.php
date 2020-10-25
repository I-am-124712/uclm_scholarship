<?php

class Working_scholars extends Controller{

    public function index(){}

    public function add(){
        session_start();
        $this->trap_no_user_session();

        if(empty($_GET))
            return;

        $deptAssigned = $_GET['deptId'];
        $idnumber = isset($_GET['idnumber'])? $_GET['idnumber']:'';
        $lname = isset($_GET['lname'])? $_GET['lname']:'';
        $fname = isset($_GET['fname'])? $_GET['fname']:'';
        $date_of_hire = isset($_GET['date_of_hire'])? $_GET['date_of_hire']:'';

        $break_date = explode("-",$date_of_hire);
        if($date_of_hire !== ''){
            if(!empty($break_date) || $break_date != null)
                if($break_date !== []){
                    $date_of_hire = $break_date[1].'-'.$break_date[2].'-'.$break_date[0];
                }
        }

        $ws_data = [
            'idnumber' => $idnumber,
            'wsName' => ($lname.', '.$fname),
            'depAssigned' => $deptAssigned+0,
            'dateOfHire' => $date_of_hire
        ];


        $err_count = 0;


        /// First check for valid input. 
        /// discovered October 12, 2020 (Happy Birthday, Batsheba Inihao!)
        
        if(strlen($idnumber) < 8 || strlen($idnumber) > 8){
            $err_count++;
            Messages::push([
                'err_idnum' => 'Invalid ID Number'
            ]);
        }

        /// Now do validations for repeating records
        $all_ws = $this->model('WS')
        ->ready()
        ->find()
        ->where([
            'logic' => 'OR',
            'idnumber' => $idnumber,
            'wsName' => ($lname.', '.$fname)
        ])
        ->go();

        if(!empty($all_ws)){
            foreach($all_ws as $ws){
                if($ws->get_fields()['idnumber'] === $idnumber){
                    $err_count++;
                    Messages::push([
                        'err_idnum' => 'Found Working Scholar with similar ID Number'
                    ]);
                    break;
                }
                if($ws->get_fields()['wsName'] === ($lname.', '.$fname)){
                    $err_count++;
                    Messages::push([
                        'err_lname' => 'Found Working Scholar with similar Last Name',
                        'err_fname' => 'Found Working Scholar with similar First Name'
                    ]);
                }
            }
        }
        
        if($err_count > 0){
            $matched_department = $this->model('Departments')
            ->ready()
            ->find()
            ->where([
                'deptId' => $deptAssigned
            ])
            ->go()[0];
            return $this->view('add-ws',$matched_department);
        }
        else{

            // TADA! Insert time.
            $this->model('WS')
            ->ready()
            ->create($ws_data)
            ->insert()
            ->go();

        }
    }

    public function delete(){
        session_start();
        $this->trap_no_user_session();

        $idnumber = isset($_GET['idnumber'])? $_GET['idnumber']:'';
        if($idnumber === '')
            return;
        

        $this->model('WS')
        ->ready()
        ->delete()
        ->where([
            'idnumber' => $idnumber
        ])
        ->go();

        /// Make sure to delete the associated user account as well.
        $this->model('User')
        ->ready()
        ->delete()
        ->where([
            'user_id' => 'ws'.$idnumber
        ])
        ->go();

    }

    public function update(){
        session_start();
        $this->trap_no_user_session();

        $err_count = 0;
        $success = false;

        $department = isset($_POST['department']) ? $_POST['department']+0:1;
        $previous_idnumber = isset($_POST['selected-id'])? $_POST['selected-id']:'NONE';
        $idnumber = isset($_POST['idnumber'])? $_POST['idnumber']:'NONE';
        $lname = isset($_POST['lname'])? $_POST['lname']:'NONE';
        $fname = isset($_POST['fname'])? $_POST['fname']:'NONE';
        $course = isset($_POST['course'])? $_POST['course']:'NONE';
        $date_of_hire = isset($_POST['date_of_hire'])? $_POST['date_of_hire']:'NONE';
        $is_dtr_in_charge = isset($_POST['inCharge'])? $_POST['inCharge']:'off';
        
        $user_privilege = $is_dtr_in_charge==='on'? 2:3;

        echo $is_dtr_in_charge;


        // Select department
        $dept = $this->model('Departments')
        ->ready()
        ->find()
        ->where([
            'deptId' => $department
        ])
        ->go()[0];


        // re-format the date to comply with SQL Server Date format
        $break_date = explode("-",$date_of_hire);

        if(!empty($break_date) || $break_date !== null)
            if($break_date !== []){
                $date_of_hire = $break_date[1].'-'.$break_date[2].'-'.$break_date[0];
            }
        
        //// we have to select first one data for comparison in our UPDATE's WHERE clause.
        $ws_match = $this->model('WS')
        ->ready()
        ->find()
        ->where([
            'idnumber' => $previous_idnumber         /// we'll only check for ID Number
        ])
        ->go()[0];

        if($ws_match === null){
            echo 'Debug: no match detected. No UPDATE to perform';
            return;
        }


        // Have to check whether newly supplied idnumber is a duplicate. 
        // Do this only if the supplied ID Number is different from the original ID Number
        // I will find a better solution soon
        if($idnumber !== $previous_idnumber){
            $ws_duplicate = $this->model('WS')
            ->ready()
            ->find()
            ->where([
                'idnumber' => $idnumber
            ])
            ->go();

            
            if(!empty($ws_duplicate)){
                $err_count++;
                Messages::push(['err_idnum'=>'Found duplicate ID Number', 'edit-status'=>'EDIT FAILED']);
            }
        }

        // proceed to update if nothing wrong is found
        if($err_count <= 0){

            /// update Working Scholar's Information...
            $this->model('WS')
            ->ready()
            ->update([
                'idnumber' => $idnumber,
                'wsName' => $lname.', '.$fname,
                'dateOfHire' => $date_of_hire,
                'course' => $course
            ])
            ->where([
                'idnumber' => $ws_match->get_fields()['idnumber']
            ])
            ->go();
            
            /// update Working Scholar's user account and select match as well...
            $this->model('User')
            ->ready()
            ->update([
                'user_privilege' => $user_privilege
            ])   
            ->where([
                'user_id' => 'ws'.$idnumber
            ])
            ->go();

            $match_user = $this->model('User')
            ->ready()
            ->find()
            ->where([
                'user_id' => 'ws'.$idnumber
            ])
            ->go()[0];

            /// re-select matching item to view updated data
            $ws_match = $this->model('WS')
            ->ready()
            ->find()
            ->where([
                'idnumber' => $idnumber
            ])
            ->go()[0];
            
            $success = true;

            Messages::push(['edit-status'=>'EDIT SUCCESSFUL']);
        }
        
        return $this->view('ws-information',[
            'ws' => $ws_match, 
            'department' => $dept, 
            'user' => $match_user, 
            'success' => $success
        ]);
    }



    public function register_as_users(){


        $working_scholars = $this->model('WS')
        ->ready()
        ->find()
        ->go();
        $user_register = $this->model('User');
        
        foreach($working_scholars as $ws){
            $lastname = explode(',',$ws->get_fields()['wsName'])[0];
            $firstname = ltrim(rtrim(explode(',',$ws->get_fields()['wsName'])[1]));

            // echo App::$salt.'<br>';utf8_encode($lastname)
            echo $ws->get_fields()['idnumber'].'<br>';
            echo 'WS-'.$ws->get_fields()['idnumber'].'<br>';
            echo md5(App::$salt.strtoupper(str_replace('ñ','Ñ',utf8_encode($lastname)))).'<br>';
            echo '<br>';

            $user_register
            ->ready()
            ->create([
                'user_id' => 'ws'.$ws->get_fields()['idnumber'],
                'username' => 'WS-'.$ws->get_fields()['idnumber'],
                'password' => md5(App::$salt.strtoupper(str_replace('ñ','Ñ',utf8_encode($lastname)))),
                'user_privilege' => 3,
                'user_lname' => $lastname,
                'user_fname' => $firstname,
                'user_photo' => '/uclm_scholarship/public/sources/users/user_default.png'
            ])
            ->insert()
            ->go();

            echo 'Successfully added all WS as User <br><br>';
        }

    }

    public function view_schedules(){
        session_start();
        $this->trap_no_user_session();

        $idnumber = isset($_POST['selected-id'])? $_POST['selected-id']:"";
        $schedType = isset($_POST['schedType'])? $_POST['schedType']:"REG";
        $schoolYear = isset($_POST['school-year'])? $_POST['school-year']:"";
        $semester = isset($_POST['semester'])? $_POST['semester']:"";

        echo $idnumber.';'.$schoolYear.';'.$semester;

        /// select WS
        $selected_ws = $this->model('WS')
        ->ready()
        ->find()
        ->where([
            'idnumber' => $idnumber
        ])
        ->go()[0];

        /// select departments
        $dept_assigned = $this->model('Departments')
        ->ready()
        ->find()
        ->where([
            'deptId' => $selected_ws->get_fields()['depAssigned']
        ])
        ->go()[0];

        /// select WS's User Account
        $user = $this->model('User')
        ->ready()
        ->find()
        ->where([
            'user_id' => 'ws'.$idnumber
        ])
        ->go()[0];


        /// select the WS's schedule
        $schedule = $this->model('Finder')
        ->ready()
        ->select([
            "STRING_AGG(schedDay,', ') within group(ORDER BY("
            ." CASE schedDay "
            ."WHEN 'M' THEN 1 "
            ."WHEN 'Tu' THEN 2 "
            ."WHEN 'W' THEN 3 "
            ."WHEN 'Th' THEN 4 "
            ."WHEN 'F' THEN 5 "
            ."WHEN 'S' THEN 6 "
            ."END )) "
            ."as [schedDay]",
            // 'schedDay',
            'tin',
            'tout',
            'totalHours'
        ])
        ->from(['Schedule'])
        ->where([
            'idnumber' => $idnumber,
            'scheduleType' => $schedType,
            'schoolYear' => $schoolYear,
            'semester' => $semester+0
        ])
        ->group(['tin', 'tout','totalHours'])
        ->go();

        // var_export($schedule);

        return $this->view('ws-information',[
            'ws' => $selected_ws, 
            'department' => $dept_assigned, 
            'schedule' => $schedule,
            'user' => $user,
            'success' => false
        ]);
    }

    public function add_schedule(){
        session_start();
        $this->trap_no_user_session();

        $idnumber = isset($_POST['selected-id'])? $_POST['selected-id']:"";
        $schedType = isset($_POST['schedType'])? $_POST['schedType']:"";
        $schoolYear = isset($_POST['school-year'])? $_POST['school-year']:"";
        $semester = isset($_POST['semester'])? $_POST['semester']:"";
        $schedDay = isset($_POST['schedDay'])? $_POST['schedDay']:"";
        $tin = isset($_POST['tin'])? $_POST['tin']:"";
        $tout = isset($_POST['tout'])? $_POST['tout']:"";
        $total = $this->differenceInHours($tin,$tout);

        echo "ID Number: ".$idnumber."<br>";
        echo "Schedule Type: ".$schedType."<br>";
        echo "School Year: ".$schoolYear."<br>";
        echo "Semester: ".$semester."<br>";
        echo "Schedule Day: ".$schedDay."<br>";
        echo "Schedule Time: ".$tin.'-'.$tout."<br>";
        echo "Total Hours: ".$total."<br>";

        
        $this->model("Schedule")
        ->ready()
        ->create([
            "idnumber" => $idnumber,
            'scheduleType' => $schedType,
            'schoolYear' => $schoolYear,
            'semester' => $semester,
            'schedDay' => $schedDay,
            'tin' => $tin,
            'tout' => $tout,
            'totalHours' => $total
        ])
        ->insert()
        ->go();



    }
    private function differenceInHours($startdate,$enddate){
        $starttimestamp = strtotime($startdate);
        $endtimestamp = strtotime($enddate);
        $difference = abs($endtimestamp - $starttimestamp)/3600;
        return $difference;
    }



}