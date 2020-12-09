<?php
require './app/core/UtilFunctions.php';

class Working_scholars extends Controller{

    public function index(){}
    
    /// this initiates the Adding process, not the adding process itself.
    public function add_ws($deptId){
        session_start();
        $this->trap_no_user_session();

        if($deptId == 0){
            echo json_encode(['errSelectDepartment'=>'No Department selected.']);
        }
        $matched_department = $this->model('Departments')
        ->ready()
        ->find()
        ->where([
            'deptId' => $deptId
        ])
        ->go()[0];

        return $this->view('add-ws',$matched_department);
    }

    public function add(){
        session_start();
        $this->trap_no_user_session();

        if(empty($_POST))
            return;

        $deptAssigned = isset($_POST['deptId'])? $_POST['deptId']:'0';
        $idnumber = isset($_POST['idnumber'])? $_POST['idnumber']:'';
        $lname = isset($_POST['lname'])? utf8_decode($_POST['lname']):'';
        $fname = isset($_POST['fname'])? utf8_decode($_POST['fname']):'';
        $date_of_hire = isset($_POST['date_of_hire'])? $_POST['date_of_hire']:'';
        $course = isset($_POST['course'])? $_POST['course']:'';

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
            'dateOfHire' => $date_of_hire,
            'course' => $course
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
                    Messages::update([
                        'err_idnum' => 'Duplicate WS with similar ID Number'
                    ]);
                    break;
                }
                if($ws->get_fields()['wsName'] === ($lname.', '.$fname)){
                    $err_count++;
                    Messages::push([
                        'err_lname' => 'Duplicate WS with similar Last Name',
                        'err_fname' => 'Duplicate WS with similar First Name'
                    ]);
                }
            }
        }
        
        // We will now insert the data if no errors were found upon all validations.
        if($err_count < 1){

            // we'll add them to the WS database...
            $this->model('WS')
            ->ready()
            ->create($ws_data)
            ->insert()
            ->go();

            // and register them as users as well.
            $this->model('User')
            ->ready()
            ->create([
                'user_id' => 'ws'.$idnumber,
                'username' => 'WS-'.$idnumber,
                'password' => md5(SALT.strtoupper($lname)),
                'user_lname' => $lname,
                'user_fname' => $fname,
                'user_privilege' => 3
            ])
            ->insert()
            ->go();

            // let us notify client that request is successful.
            Messages::push([
                "success" => "Successfully added Working Scholar"
            ]);
        }

        // we will return this page as JSONified string for all messages according to the response.
        echo json_encode(Messages::dump());

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
        $lname = utf8_decode(isset($_POST['lname'])? $_POST['lname']:'NONE');
        $fname = utf8_decode(isset($_POST['fname'])? $_POST['fname']:'NONE');
        $course = isset($_POST['course'])? $_POST['course']:'NONE';
        $date_of_hire = isset($_POST['date_of_hire'])? $_POST['date_of_hire']:'NONE';
        $is_dtr_in_charge = isset($_POST['inCharge'])? $_POST['inCharge']:'off';
        
        $user_privilege = $is_dtr_in_charge==='on'? 2:3;

        // re-format the date to comply with SQL Server Date format
        $break_date = explode("-",$date_of_hire);

        if(!empty($break_date) || $break_date !== null)
            if($break_date !== []){
                $date_of_hire = $break_date[1].'-'.$break_date[2].'-'.$break_date[0];
            }

        // Select department
        $dept = $this->model('Departments')
        ->ready()
        ->find()
        ->where([
            'deptId' => $department
        ])
        ->go()[0];

        // Select default matching user
        $match_user = $this->model('User')
        ->ready()
        ->find()
        ->where([
            'user_id' => 'ws'.$previous_idnumber
        ])
        ->go()[0];

        
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
            
            /// update Working Scholar's user account and re-select match as well...
            $this->model('User')
            ->ready()
            ->update([
                'user_id' => 'ws'.$idnumber,
                'user_privilege' => $user_privilege
            ])   
            ->where([
                'user_id' => 'ws'.$previous_idnumber
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



    private function register_as_users(){


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
            'schedule_id',
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
        ->group(['schedule_id','tin', 'tout','totalHours'])
        ->order_by([
            'schedule_id' => 'ASC'
        ])
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

        $schedule_id = isset($_POST['schedule_id'])? $_POST['schedule_id']:"";
        $idnumber = isset($_POST['selected-id'])? $_POST['selected-id']:"";
        $schedType = isset($_POST['schedType'])? $_POST['schedType']:"";
        $schoolYear = isset($_POST['school-year'])? $_POST['school-year']:"";
        $semester = isset($_POST['semester'])? $_POST['semester']:"";
        $schedDay = isset($_POST['schedDay'])? $_POST['schedDay']:"";
        $tin = isset($_POST['tin'])? $_POST['tin']:"";
        $tout = isset($_POST['tout'])? $_POST['tout']:"";
        $total = time_difference_abs($tin,$tout);


        $req = $this->model("Schedule")
        ->ready()
        ->create([
            "schedule_id" => $schedule_id,
            "idnumber" => $idnumber,
            'scheduleType' => $schedType,
            'schoolYear' => $schoolYear,
            'semester' => $semester,
            'schedDay' => $schedDay,
            'tin' => $tin,
            'tout' => $tout,
            'totalHours' => $total
        ])
        ->insert();
        $req->get_query_string();

        $req->go();
    }
     

    public function delete_schedule(){
        $schedule_id = isset($_POST['scheduleId'])? $_POST['scheduleId']:"";
        echo $schedule_id;

        if($schedule_id === '')
            return;

        $this->model("Schedule")
        ->ready()
        ->delete()
        ->where([
            'schedule_id' => $schedule_id
        ])
        ->go();
    }



    public function schedule_index(){

        $sched_id = array("schedId"=>0);
        $schedule_index = 
                $this
                ->model('Finder')
                ->ready()
                ->select([
                    'DISTINCT TOP 1 schedule_id'
                ])
                ->from([
                    'Schedule'
                ])
                ->order_by([
                    "schedule_id" => 'DESC'
                ])
                ->go();

        if(!empty($schedule_index)){
            $sched_index = $schedule_index[0];
            $sched_id['schedId'] = $sched_index->get_fields()['schedule_id'];
        }
        echo (json_encode($sched_id));
    }

    /**
     * Verify if this working scholar has submitted an attendance entry.
     */
    public function hasAttendance($idnumber='', $dateString='', $jsonify = false){
        if(!isset($_POST['req'])){
            echo json_encode([
                'err' => 'Invalid request'
            ]);
            return;
        }
        if(session_status() != PHP_SESSION_ACTIVE)
            session_start();

        if(isset($_POST['ajax'])){
            $idnumber = str_replace("ws", "", $_SESSION['user_id']);
            $dateString = date('m-d-Y');
        }

        $customSql = "SELECT * FROM Record WHERE idnumber = ? AND recorddate = ?
            AND timeIn IS NOT NULL AND [timeOut] IS NULL;
        ";

        $bindParams = [ $idnumber, $dateString ];

        $timeInRecord = $this->model('Finder')
        ->ready()
        ->customSql($customSql)
        ->setBindParams($bindParams)
        ->result_set();

        if($jsonify)
            echo json_encode(['hasAttendance' => count($timeInRecord) > 0]);

        return count($timeInRecord) > 0;
    }

    /**
     * Submits a time-in entry for the current Working Scholar.
     */
    public function submitAttendance(){
        if(!isset($_POST['req'])){
            echo json_encode([
                'err' => 'Invalid request'
            ]);
            return;
        }
        session_start();

        // We should set the timezone to our current country.
        date_default_timezone_set('Asia/Manila');

        $idnumber = str_replace("ws","",$_SESSION['user_id']);
        $dateNow = date_format(new DateTime(), 'M d, Y');
        $timeNow = new DateTime();
        $timeNowString = date_format($timeNow, 'h:i a');
        $type = $_POST['attype'];
        $totalHours = explode(",", $_POST['totalHours']);
        $scheduleIn = explode(",", $_POST['scheduleIn']);
        $scheduleOut = explode(",", $_POST['scheduleOut']);
        $tardiness = 0;

        $totalDutyHours = 0;

        // integrate schedule in one array
        $scheduleGrouped = [];
        for($i=0; $i < count($totalHours); ++$i){
            $scheduleGrouped[$i] = [];
            $scheduleGrouped[$i]['schedIn'] = $scheduleIn[$i];
            $scheduleGrouped[$i]['schedOut'] = $scheduleOut[$i];
            $scheduleGrouped[$i]['totalHours'] = $totalHours[$i];

            $totalDutyHours += $totalHours[$i];
        }
        
        // store the schedule we will use for bases in trapping unnecessary time-in.
        $timeInForChecking = null;

        // We pre-compute our tardiness either lates or undertime first despite not being able
        // to use it whenever attendance check fails.
        if(count($scheduleGrouped) >= 1){
            for($i = 0; $i < count($scheduleGrouped); ++$i){
                // here's where we determine our time for checking. We will check if the current
                // time is encompassed on the current schedule block.
                if(!isset($timeInForChecking) && strtotime($timeNowString) <= strtotime($scheduleGrouped[$i]['schedOut']))
                    $timeInForChecking = $scheduleGrouped[$i]['schedIn'];

                // separate our schedule in, out and total hours
                $scheduleInAsTime = date_create_from_format("h:i A",$scheduleGrouped[$i]['schedIn']);
                $scheduleOutAsTime = date_create_from_format("h:i A",$scheduleGrouped[$i]['schedOut']);
                $total = $scheduleGrouped[$i]['totalHours'];

                if($type === 'in'){
                    $computedTardiness = compute_tardiness($scheduleInAsTime, $timeNow, $total);
                }
                else if($type === 'out'){
                    $computedTardiness = compute_tardiness($timeNow, $scheduleOutAsTime, $total);
                }
                $tardiness += $computedTardiness;
            }
        }

        if($type === 'in'){
            // now we trap our user to not log in 15 minutes earlier than their schedule.
            if($timeInForChecking != null && strtotime($timeNowString) < strtotime($timeInForChecking) - (60 * 15)){
                echo json_encode([
                    'errTimeInEarly' => 'YOU CANNOT LOG YOUR TIME-IN EARLIER THAN 15 MINUTES FROM YOUR SCHEDULE'
                ]);
                return;
            }
            if($this->hasAttendance($idnumber, $dateNow)){
                echo json_encode(['errTimeInSubmitted'=>'ALREADY LOGGED TIME-IN FOR TODAY']);
            }else{
                $this->model('Record')
                ->ready()
                ->create([
                    'idnumber' => $idnumber,
                    'recorddate' => $dateNow,
                    'timeIn' => $timeNowString,
                    'dutyHours' => $totalDutyHours,
                    'late' => $tardiness,
                    'undertime' => 0,
                ])
                ->insert()
                ->go();
                echo json_encode([
                    'timeInSuccess' => 'SUCCESSFULLY LOGGED TIME-IN'
                ]);
            }
        }else if($type==='out'){
            if(!$this->hasAttendance($idnumber, $dateNow)){
                echo json_encode(['errTimeOutSubmitted'=>'NO NEWLY LOGGED TIME-IN FOUND']);
                return;
            }
            $customSql = "UPDATE Record SET [timeOut] = ?, undertime = ?
                WHERE idnumber = ? AND recorddate = ? and [timeOut] IS NULL;
            ";
            $bindParams = [ $timeNowString, $tardiness, $idnumber, $dateNow ];

            $this->model('Finder')
            ->ready()
            ->customSql($customSql)
            ->setBindParams($bindParams)
            ->go();
            echo json_encode([
                'timeOutSuccess' => 'SUCCESSFULLY LOGGED TIME-OUT'
            ]);
        }

    }

    /**
     * Returns a JSON-encoded response containing the current Working Scholar's record
     * during the current month.
     * 
     */
    public function getMyRecordForThisMonth(){
        if(!isset($_POST['req']))
            die(400);
        
        session_start();

        $maxDays = [31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        $idnumber = str_replace("ws", "", $_SESSION['user_id']);
        $month = date("M");
        $monthIndex = date('m') + 0;
        $year = date("Y");

        // Set SELECT bounds
        $dateStart = $month . " 1, " . $year;
        $dateEnd = $month . " ". $maxDays[$monthIndex - 1] .", " . $year; 

        $sql = "SELECT recorddate, timeIn, [timeOut], late, undertime,
            case when timeIn is not null and [timeOut] is not null then 
            case when dutyHours - (late + undertime) <= 0 then 0 else dutyHours - (late + undertime) 
            end else 0 end as 'hoursRendered'
            from Record where idnumber = ? and recorddate between ? and ?; 
        ";
        $bindParams = [ $idnumber, $dateStart, $dateEnd ];

        $records = $this->model('Finder')
        ->ready()
        ->customSql($sql)
        ->setBindParams($bindParams)
        ->result_set();

        $res = [];
        foreach($records as $rec) {
            $recordInfo = [];

            $recordInfo['recordDate'] = date_format($rec->get('recorddate'), 'M d, Y');
            $recordInfo['timeIn'] = $rec->get('timeIn') != null? date_format($rec->get('timeIn'), "h:i A"): ''; 
            $recordInfo['timeOut'] = $rec->get('timeOut') != null? date_format($rec->get('timeOut'), "h:i A"): ''; 
            $recordInfo['late'] = $rec->get('late');
            $recordInfo['undertime'] = $rec->get('undertime');
            $recordInfo['hoursRendered'] = $rec->get('hoursRendered');

            array_push($res, $recordInfo);
        }

        echo json_encode([
            'month' => $monthIndex,
            'year' => date('Y'),
            'dateStart' => $dateStart,
            'dateEnd' => $dateEnd,
            'records' => $res
        ]);
    }
}