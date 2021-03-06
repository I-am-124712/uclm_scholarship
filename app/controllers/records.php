<?php

require './app/core/UtilFunctions.php';

class Records extends Controller {

    private $departments_obj;
    private $ws_obj;
    private $schedule_obj;
    private $record_obj;
    private $finder_obj;
    private $allowance_summary_obj;

    public function __construct(){
        $this->departments_obj = $this->model('Departments');
        $this->ws_obj = $this->model('WS');
        $this->schedule_obj = $this->model('Schedule');
        $this->allowance_summary_obj = $this->model('AllowanceSummary');
        $this->record_obj = $this->model('Record');
        $this->finder_obj = $this->model('Finder');
    }

    public function index(){
        session_start();
        $this->trap_no_user_session();
    }
    
    public function dtr($action = ''){
        session_start();
        $this->trap_no_user_session();



        // This method will return a specific view for users
        // with privilege = 3 (Working Scholars)
        if($_SESSION['user_privilege'] == 3)
            header('Location: /uclm_scholarship/records/my_dtr');

        // We make sure only authorized users could access the Upload
        // feature.
        if($action === 'upload'){
            $args = [];

            if(isset($_GET['uploadSuccess']))
                $args['uploadSuccess'] = true;

            return $this->view('dtr-upload', $args);
        }

        if(isset($_POST['req'])){
            $this->get_dtr_info();
        }
        else{
            return $this->view('dtr');
        }
    }

    public function uploadRaw(){
        if(isset($_POST['submit'])) {

            echo "UPLOADING...<br>";
            $file_dest = $_SERVER['DOCUMENT_ROOT'] . '/uploads/';
            $file_create = $file_dest . 'attlog_read.dat';

            if(file_exists($file_create)){
                echo "File Already Exists! ";
            }
            else{
                move_uploaded_file($_FILES['dat-file']['tmp_name'], $file_create);
                echo "UPLOADED! ";
            }
            
            echo "READING...<br>";

            $forSql = $this->model('Finder');
            $delete = 'DELETE FROM Record';

            // Delete everything first
            $forSql->ready()->customSql($delete)->go();

            // Then read uploaded file
            $datFile = file($file_create);

            
            foreach($datFile as $val) {
                $data = explode("\t", $val);

                $idnumber = $data[0];
                $timeStamp = explode(" ", $data[1]);

                $date = $timeStamp[0];
                $time = $timeStamp[1];

                $attype = $data[3];

                switch($attype){
                    // code 0 = Check-in
                    // Code 4 = Overtime-in
                    // Both are treated as "in".
                    case 0:
                    case 4:
                        $sqlString = 'INSERT INTO Record(idnumber, recorddate, timeIn) VALUES(?,?,?)';
                        $bindParams = [ $idnumber, $date, $time ];
                        break;
                    // code 1 = Check-out
                    // Code 5 = Overtime-out
                    // Both are treated as "out".
                    case 1:
                    case 5:
                        $sqlString = 'UPDATE Record SET [timeOut] = ? WHERE idnumber = ? AND recorddate = ?';
                        $bindParams = [ $time, $idnumber, $date ];
                }

                $forSql->ready()->customSql($sqlString)->setBindParams($bindParams)->go();
            }

            header('location: /uclm_scholarship/records/dtr/upload?uploadSuccess=true');           

        }
    }

    public function my_dtr(){
        session_start();
        $this->trap_no_user_session();

        // Users with higher access privilege should be redirected to
        // the general Records view. 
        if($_SESSION['user_privilege'] != 3)
            header('Location: /uclm_scholarship/records/dtr');

        if(isset($_GET['req'])){

            // Final result array ready for JSON encoding
            $result = []; 

            $school_year = isset($_GET['school-year'])?$_GET['school-year']:'';
            $semester = isset($_GET['semester'])?$_GET['semester']:'';
            $period = isset($_GET['period'])?$_GET['period']:'';
            $month = isset($_GET['month'])? $_GET['month']:'';
            $hide = isset($_GET['hide']);

            // Get the Working Scholar's ID number from their very own User ID.
            $user_id = $_SESSION['user_id'];

            // Get the Period bounds first...
            $period_bounds = $this->get_period_bounds($school_year, $period, $month);

            $date_start = $period_bounds['dateStart'];
            $date_end = $period_bounds['dateEnd'];


            // now get the schedule data next. We first create a WS Model object
            // having only 'idnumber' as property (or field).
            $ws = $this->ws_obj
            ->ready()
            ->find()
            ->where([
                'user_id' => $user_id
            ])
            ->result_set(['index' => 0]);

            // we then retrieve schedule...
            $schedule = $this->retrieve_schedule($ws, $school_year, $semester);

            // then the DTR entries...
            $result_dtr = $this->retrieve_dtr($ws, $date_start, $date_end, $hide);

            // Automatically plot DTR data since this is in view mode.
            $dtr_plotted = $this->plot_schedule_per_dtr($result_dtr, $schedule);


            // Fill the array and prepare for JSON encoding...
            $result['idnumber'] = $ws->get('idnumber');
            $result['wsName'] = $ws->get('wsName');
            $result['wsRecords'] = $dtr_plotted;

            echo json_encode($result);
        }
        else{
            return $this->view('dtr-ws');
        }
    }
    
    /**
     * Controller for Working Scholars Summary Report Admin View 
     */
    public function summary(){
        session_start();
        $this->trap_no_user_session();

        if($_SESSION['user_privilege'] == 3)
            header('Location: /uclm_scholarship/records/my_summary');

        return $this->view('summary-admin');
    }

    /**
     * Controller for Working Scholars' Summary Report WS view
     */
    public function my_summary(){
        session_start();
        $this->trap_no_user_session();

        if($_SESSION['user_privilege'] != 3)
            header('Location: /uclm_scholarship/records/summary');
        
        $idnumber = str_replace("ws", '', $_SESSION['user_id']);

        $sql = "SELECT * FROM AllowanceSummary WHERE ws_idnumber = ? ORDER BY school_year, dtr_month, dtr_period ASC";
        $bind = [ $idnumber ];

        $summaries = $this->model('Finder')
        ->ready()
        ->customSql($sql)
        ->setBindParams($bind)
        ->result_set();

        $recentSummary = $this->model('Finder')
        ->ready()
        ->customSql('SELECT * FROM AllowanceSummary where ws_idnumber = ? 
            AND CAST(CURRENT_TIMESTAMP - update_timestamp AS INT) <= (31)
            AND allowance_status = \'RELEASED\'')
        ->setBindParams([ $idnumber ])
        ->result_set();

        return $this->view('summary-ws', [
            'summary' => $summaries,
            'recent' => $recentSummary
        ]);
    }


    /**
     * Controller method for WS Overtime view
     */
    public function overtime($args = ''){
        session_start();
        $this->trap_no_user_session();
        
        if($_SESSION['user_privilege'] == 3){
            header('location: /uclm_scholarship/records/my_overtime');
        }

        if(isset($args) && $args === 'encode') {
            if(isset($_GET['department'])){
                $selectedDepartment = $_GET['department'];
                $wsQuery = "SELECT * FROM WS WHERE depAssigned = ? ORDER BY wsName ASC";
                $wsBindParams = [ $selectedDepartment ];

                $working_scholars = $this->model('Finder')
                ->ready()
                ->customSql($wsQuery)
                ->setBindParams($wsBindParams)
                ->result_set();
            }
            else{
                $selectedDepartment = $working_scholars = null;
            }
            if(isset($_GET['working-scholar'])){
                $selectedID = $_GET['working-scholar'];

                $sql = "SELECT Overtime.overtime_id, WS.idnumber, 
                        WS.wsName, Overtime.otdate, 
                        Overtime.ottimestart, Overtime.ottimeend,
                        Overtime.ottotal
                        from WS INNER JOIN OVERTIME on WS.idnumber = Overtime.idnumber
                        WHERE WS.idnumber = ?";
                $bindParams = [ $selectedID ];
                if($selectedID !== 'none') 
                    $wsInfo = $this->model('WS')
                    ->ready()
                    ->find()
                    ->where([
                        'idnumber' => $selectedID
                    ])
                    ->result_set()[0];
                
                else
                    $wsInfo = null;
                $savedOvertime = $this->model('Finder')
                ->ready()
                ->customSql($sql)
                ->setBindParams($bindParams)
                ->result_set();


            }
            else{
                $selectedID = $wsInfo = $savedOvertime = null;
            }

            $departments = $this->model('Departments')
            ->ready()
            ->find()
            ->result_set();

            return $this->view('overtime-encode', [ 
                'departments' => $departments,
                'ws' => $working_scholars,
                'ws-info' => $wsInfo,
                'savedOvertime' => $savedOvertime,
                'selectedDepartment' => $selectedDepartment,
                'selectedID' => $selectedID
            ]);
        }

        return $this->view('overtime', [
            'finderObj' => $this->model('Finder')
        ]);
    }

    public function my_overtime(){
        session_start();
        $this->trap_no_user_session();

        if($_SESSION['user_privilege'] == 999 || $_SESSION['user_privilege'] == 1){
            header('location: /uclm_scholarship/records/overtime');
        }
        return $this->view('overtime-ws', [
            'finderObj' => $this->model('Finder')
        ]);
    }

    /**
     * Responds to a given Working Scholar Overtime Request.
     */
    public function overtimeRequestResponse(){
        if(!isset($_POST['req']))
            die();
        session_start();

        $data = json_decode($_POST['data'], true);
        
        $requestId = isset($data['request_id'])? $data['request_id']:'';
        $response = isset($data['response'])? $data['response']:'';
        $userId = $_SESSION['user_id'];
        $feedbackMessage = $data['feedback'];

        $sql = "UPDATE OvertimeRequest SET request_status = ? WHERE request_id = ?;
            INSERT INTO OvertimeRequestFeedback VALUES(?, ?, ?, CURRENT_TIMESTAMP);
        ";
        $bindParams = [ $response, $requestId, $requestId, $userId, $feedbackMessage ];

        $success = $this->model('Finder')
        ->ready()
        ->customSql($sql)
        ->setBindParams($bindParams)
        ->go();

        // var_export([
        //     'requestId' => $requestId,
        //     'response' => $response,
        //     'userId' => $userId,
        //     'feedback' => $feedbackMessage 
        // ]);

        if($success)
            echo json_encode([ 'success' => 'Successfully '.$response.' Overtime Request' ]);
        else die('ERROR: Error processing request');
    }

    /**
     * Submits an overtime request for admin approval/rejection.
     */
    public function submitOvertimeRequest() {
        if(!isset($_POST['req']))
            die('ERROR: Invalid request');
        session_start();
        // default our timezone to Philippines
        date_default_timezone_set('Asia/Manila');

        $otRequestObject = json_decode($_POST['requestObject'], true);

        // Prepare everything for adding.
        $sqlObject = $this->model('Finder'); // SQL builder object

        $idnumber = str_replace('ws', '', $_SESSION['user_id']);
        $requestMessage = $otRequestObject['requestMessage'];
        $overtimeEntries = $otRequestObject['overtimeEntries'];

        // We should not accept a request with no specified Overtime information (i.e no Overtime entry).
        if(!isset($overtimeEntries) || count($overtimeEntries) <= 0)
            die('ERROR: Missing required parameters/data');

        // We first save the OvertimeRequest
        $addOvertimeRequest = "INSERT INTO OvertimeRequest 
            VALUES(?, ?, CURRENT_TIMESTAMP, 'PENDING');
        ";
        $bindParams = [ $idnumber, $requestMessage ];
        $sqlObject->ready()->customSql($addOvertimeRequest)->setBindParams($bindParams)->go();

        echo date('Y-m-d H:i:s');

        // Next we'd save our overtime entries. We will simultaneously map
        // them once they're added to our database. We should be declaring
        // these variables below since we need their addresses for reference 
        // passing.
        $overtimeDate;
        $overtimeStart;
        $overtimeEnd;
        $overtimeTotal;

        // Get the most recent Request ID (i.e the ID assigned to this current request.)
        $recentRequestId = $sqlObject
        ->ready()
        ->customSql("SELECT TOP 1 request_id FROM OvertimeRequest ORDER BY request_id DESC")
        ->result_set()[0]
        ->get('request_id');

        // sql for adding our entries. We will bind by reference since there could be more than one OT entries provided.
        $sqlForAddingEntries = 'INSERT INTO Overtime(idnumber, otdate, ottimestart, ottimeend, ottotal)
            VALUES(?, ?, ?, ?, ?);';
        $bindParamsForAdd = [ &$idnumber, &$overtimeDate, &$overtimeStart, &$overtimeEnd, &$overtimeTotal ];
        $addEntryObj = $this->model('Finder')->ready()->customSql($sqlForAddingEntries)->setBindParams($bindParamsForAdd);

        // sql for mapping the OT entries to the request.
        $sqlForMappingEntries = 'INSERT INTO OvertimeRequestMapper 
            SELECT ?, overtime_id FROM Overtime 
            WHERE idnumber = ? AND otdate = ? AND ottimestart = ?
            AND ottimeend = ? AND ottotal = ?';
        $bindParamsForMapping = [ 
            &$recentRequestId, 
            &$idnumber,
            &$overtimeDate, 
            &$overtimeStart, 
            &$overtimeEnd, 
            &$overtimeTotal ];
        $mapEntriesObj = $this->model('Finder')->ready()->customSql($sqlForMappingEntries)->setBindParams($bindParamsForMapping);

        // loop through each overtime entries
        foreach($overtimeEntries as $overtime){

            $overtimeDate = $overtime['overtimeDate'];
            $overtimeStart = $overtime['overtimeStart'];
            $overtimeEnd = $overtime['overtimeEnd'];
            $overtimeTotal = $overtime['overtimeTotal'];

            // Add entry first...
            $addEntryObj->executeNonQuery();

            // before mapping them.
            $mapEntriesObj->executeNonQuery();

        }

    }

    public function saveOvertime() {
        // session_start();
        // $this->trap_no_user_session();

        if(isset($_POST['req'])) {
            $overtimeData = json_decode($_POST['overtime-data'], true);

            $idnumber = $overtimeData['idnumber'];
            if(!empty($overtimeData['overtimeEntries'])) {
                foreach($overtimeData['overtimeEntries'] as $ot) {
                    $otdate = date('m-d-Y',strtotime($ot['overtimeDate']));
                    $ottimestart = $ot['overtimeStart'];
                    $ottimeend = $ot['overtimeEnd'];
                    $ottotal = $ot['overtimeTotal'];


                    $sql = "INSERT INTO Overtime(idnumber, otdate, ottimestart, ottimeend, ottotal)
                        VALUES(?,?,?,?,?);";
                    $bindParams = [ $idnumber, $otdate, $ottimestart, $ottimeend, $ottotal ];

                    $this->model('Finder')
                    ->ready()
                    ->customSql($sql)
                    ->setBindParams($bindParams)
                    ->executeNonQuery();
                }

                echo json_encode(['success' => 'Success!']);
            }
            else {
                header("HTTP/1.0 404 Not Found");
            }
        }
    }


    public function saveDtrData(){

        $record_id = isset($_POST['id'])? $_POST['id']:die;
        $late = isset($_POST['late'])? $_POST['late']:'';
        $undertime = isset($_POST['undertime'])? $_POST['undertime']:'';
        $total_data = isset($_POST['totalData'])? $_POST['totalData']:'';

        // save calculated lates, undertime and total of DTR entry to
        // our database. 

        $sql = "UPDATE Record SET late = ?, undertime = ?, hoursRendered = ?
        WHERE record_id = ?";

        $bindParams = [ $late, $undertime, $total_data, $record_id ];

        $this->finder_obj->ready()->customSql($sql)->setBindParams($bindParams)->executeNonQuery();
        
        // Notify save! Tada
        echo json_encode(array("save_success" => "Saved"));
    }



    public final function get_departments(){
        session_start();
        $this->trap_no_user_session();

        if(isset($_GET['req'])){

            $result = $this->departments_obj
            ->ready()
            ->find()
            ->go();

            $departments = array();

            foreach($result as $dept){
                array_push($departments,[
                    'deptId' => $dept->get()['deptId'],
                    'departmentName' => $dept->get()['departmentName'],
                ]);
            }

            echo json_encode($departments);
        }
    }

    
    // Method that returns a JSON encoded array of every DTR entry + schedules 
    // of the Working Scholars assigned in the selected Department

    private function get_dtr_info(){
        $data = [];     // for main result Object

        $school_year = isset($_POST['school-year'])? $_POST['school-year']:'';
        $department = isset($_POST['department'])? $_POST['department']+0:'';
        $semester = isset($_POST['semester'])? $_POST['semester']+0:'';
        $period = isset($_POST['period'])? $_POST['period']+0:'';
        $month = isset($_POST['month'])? $_POST['month']+0:'';
        $hide = isset($_POST['hide']);
        $load_method = isset($_POST['load-method'])? $_POST['load-method']:'auto';

        // Select working scholars first. We will be performing
        // separate queries for every Working Scholars.
        $working_scholars = $this->ws_obj
        ->ready()
        ->find()
        ->where([
            'depAssigned' => $department
        ])
        ->order_by([
            'wsName' => 'ASC'
        ])
        ->result_set();

        
        if(!empty($working_scholars)){

            foreach($working_scholars as $working){

                // for WS information (and Record data)
                $ws_data = [];

                // declare arrays for DTR entries and Schedule data...
                $dtr = [];
                $schedules = [];

                /*** Retrieve Records ***/

                // This one decides what year to use by deciding if the month
                // selected is inclusive to a given school year.
                $period_bounds = $this->get_period_bounds($school_year, $period, $month);

                $dateStart = $period_bounds['dateStart'];
                $dateEnd = $period_bounds['dateEnd'];

                // Query won't work if ID Number isn't Integer when bound. Idk why
                $idnumber = ($working->get()['idnumber'] + 0);


                // Retrieve DTR entries...
                $result_dtr = $this->retrieve_dtr($idnumber, $dateStart, $dateEnd, $hide);

                // Retrieve the schedules for the working Scholar
                $schedule = $this->retrieve_schedule($idnumber, $school_year, $semester);     

                // We will retrieve all the WS's schedule for
                // the manual adding of schedule for every
                // DTR entry if user chooses manual assignment.
                
                if(!empty($schedule)){
                    foreach($schedule as $sched){
                        array_push($schedules, $sched->get());
                    }
                }

                // We will start plotting each schedules in each of the WS's DTR data. 
                $dtr = $this->plot_schedule_per_dtr($result_dtr, $schedule, $load_method);


                // Prepare for JSON encoding
                array_push($data,[
                    'idnumber' => $working->get('idnumber'),
                    'wsName' => utf8_encode($working->get('wsName')),
                    'schedule' => $schedules,
                    'wsRecords' => $dtr
                ]);

            }
            echo json_encode($data);
        }

    }


    // Backend Deletion of our Records data
    public function delete(){
        $record_id = isset($_POST['record_id'])? $_POST['record_id']:'';
        if($record_id === '')
            return;
        
        $this->record_obj
        ->ready()
        ->delete()
        ->where([
            'record_id' => $record_id
        ])
        ->go();
    }

    // Backend updating of our Records data
    public function update(){

        if(!empty($_POST)){
            $record_id = $_POST['record_id']; 
            unset($_POST['record_id']);

            $this->record_obj
            ->ready()
            ->update($_POST)
            ->where([
                'record_id' => $record_id
            ])
            ->go();
        }
    }

    /** 
    *    Breaks down the Date string and determines the 
    *    Start and end date of a Period from a given School Year and
    *    Month then returns both date bounds as DateFromParts SQL String in
    *    an associative array.
    *
    *    Params:
    *    - $school_year - string representing the School Year
    *    - $period - an integer specifying the period
    *    - $month - an integer identifying the month
    *    
    *    Returns:
    *    - ['dateStart' => string, 'dateEnd' => string] - an assoc array 
    *        containing the SQL DateFromParts() string of both inclusive dates
    *        for the specified Period.
    */

    private function get_period_bounds($school_year, $period, $month){
        $record_year = ($month<5)? explode('-',$school_year)[1]:explode('-',$school_year)[0];
        $max_month_days = [31,29,31,30,31,30,31,31,30,31,30,31];
        $dateStart;
        $dateEnd;
        switch($period){
            case 1:// First Period
                $dateStart = getDateFromPartsString($record_year, ($month+1), 1);
                $dateEnd = getDateFromPartsString($record_year, ($month+1), 15);
                break;
            case 2: // Second Period
                $dateStart = getDateFromPartsString($record_year, ($month+1), 16);
                $dateEnd = getDateFromPartsString($record_year, ($month+1), $max_month_days[$month]);

        }
        return [ 'dateStart' => $dateStart, 'dateEnd' => $dateEnd];
    }


    /** 
    *    Retrieves the DTR Data for a given Working Scholar $ws
    *    on a given period bound $date_start and $date_end. 
    *
    *    Params:
    *    - $idnumber - Integer WS ID Number
    *    - $date_start - An SQL DateFromPart() method String for the starting inclusive date to retrieve.
    *    - $date_end - An SQL DateFromPart() method String for the closing inclusive date to retrieve.
    *    - $hide_nulls - Hide Record entries having NULL for times-in or times-out.
    *
    *    Returns:
    *    - $result_dtr - An Array of Record Model objects.
    */
    private function retrieve_dtr($idnumber, $date_start, $date_end, $hide_nulls){

        $hideNullsString = $hide_nulls? "AND timeIn IS NOT NULL
        AND [timeOut] IS NOT NULL;" : "";

        
        $sql = "SELECT record_id, recorddate, timeIn, [timeOut], hoursRendered
                FROM Record WHERE idnumber = ? AND recorddate between $date_start AND $date_end 
                $hideNullsString";

        $bindParams = [ $idnumber, $date_start, $date_end ];

        // var_export($bindParams);

        return $this->finder_obj->ready()->customSql($sql)->setBindParams($bindParams)->result_set();
    }


    /**  
     *   Retrieves the Schedules of the given Working Scholar $ws 
     *   during a given Semester on the given School Year.
     *
     *   Params:
     *   - $idnumber - Integer WS ID Number
     *   - $school_year - A string that represents the school year (must be two 4-digit year strings)
     *     separated by a dash with no whitespaces (eg. "2015-2016")
     *   - $semester - An integer representing the semester of the schedule to be loaded.
     *
     *   Returns:
     *   - $schedule - An Array of Schedule Model object retrieved from the performed query.
    */
    private function retrieve_schedule($idnumber, $school_year, $semester){
        $sql = "SELECT schedule_id, scheduleType, schedDay, tin, tout, totalHours from schedule where idnumber = ?
        and semester = ?
        and schoolYear = ?
        order by tin, tout asc,
        (CASE schedDay 
                    WHEN 'M' THEN 1 
                    WHEN 'Tu' THEN 2 
                    WHEN 'W' THEN 3 
                    WHEN 'Th' THEN 4 
                    WHEN 'F' THEN 5 
                    WHEN 'S' THEN 6 
                    END)";

        $bindParams = [ $idnumber, $semester, $school_year ];

             
        return $this->finder_obj->ready()->customSql($sql)->setBindParams($bindParams)->result_set();
    }

    /** 
     *   Plots each of the Working Scholar's respective
     *   Schedules in each of their DTR entries. The schedule to be
     *   associated for a DTR entry will depend on the DTR record's date
     *   and the day of week it belongs.
     *   
     *   Params:
     *   - $result_dtr - array of Record Model objects
     *   - $schedule - array of Schedule Model objects
     *   - $load_method - specifies the load method (accepted values: 'manual', 'auto')
     *
     *   Returns:
     *   - $dtr - new Array of DTR entries with corresponding Schedules.
    */
    private function plot_schedule_per_dtr($result_dtr, $schedule, $load_method = 'auto'){

		$dtr = [];


		if(!empty($result_dtr)){
		    $scheduleForRecord = [];
		    $spc_scheduleForRecord = [];
		    foreach($result_dtr as $record){

		        $dtr_entry = $record->get();
		        $day = [null,"M","Tu","W","Th","F","S"];

		        // Get the Day of week for the current DTR entry we are accessing...
		        $recordWeekDay = date('w', strtotime(date_format($record->get('recorddate'),'M-d-Y')));
		        
                // For Regular Schedule computation...
                $late = 0;
                $undertime = 0;
                $total = 0;
                
                // For Specific Schedule computation...
                $spc_late = 0;
                $spc_undertime = 0;
                $spc_total = 0;

		        if(!empty($schedule)){

		            $record_in = $dtr_entry['timeIn'];
                    $record_out = $dtr_entry['timeOut'];


		            $lates_undertimes = [];
		            foreach($schedule as $sched){
		                switch($sched->get('scheduleType')){
                            case 'REG':
                            default:
		                        $matchingSched = $sched->is_match([
		                            'schedDay' => $day[$recordWeekDay]
		                        ]);


		                        if($matchingSched != null) {
		                            array_push($scheduleForRecord,[
		                                'schedule_id' => $matchingSched->get('schedule_id'),
		                                'schedIn' => $matchingSched->get('tin'),
		                                'schedOut'=> $matchingSched->get('tout'),
		                                'totalHours' => $matchingSched->get('totalHours')
		                            ]);

		                            // we'll compute the Lates, Undertime and total Hours
		                            // if user chooses to automatically assign schedule
		                            // for every DTR entry.
		                            if($load_method === 'auto'){
		                                $tin = $matchingSched->get('tin');
		                                $tout = $matchingSched->get('tout');
		                                $expectedHours = $matchingSched->get('totalHours');

                                        $this->compute_late_undertime(
                                            $tin,
                                            $tout,
                                            $expectedHours,
                                            $record_in,
                                            $record_out,
                                            $late,
                                            $undertime,
                                            $total
                                        );

                                    }
		                        }
		                        break;
                            case 'SPC':
                                
                                $recorddate = strtotime($record->get('recorddate')->format('m/d/Y'));
                                $schedDay = strtotime($sched->get('schedDay'));

                                if($schedDay === $recorddate){

                                    array_push($spc_scheduleForRecord, [
                                        'schedule_id' => $sched->get('schedule_id'),
                                        'schedIn' => $sched->get('tin'),
                                        'schedOut' => $sched->get('tout'),
                                        'totalHours' => $sched->get('totalHours')
                                    ]);

                                    // we'll compute the Lates, Undertime and total Hours
                                    // if user chooses to automatically assign schedule
                                    // for every DTR entry.
                                    if($load_method === 'auto'){
                                        $spc_tin = $sched->get('tin');
                                        $spc_tout = $sched->get('tout');
                                        $spc_expectedHours = $sched->get('totalHours');
    
                                        $this->compute_late_undertime(
                                            $spc_tin,
                                            $spc_tout,
                                            $spc_expectedHours,
                                            $record_in,
                                            $record_out,
                                            $spc_late,
                                            $spc_undertime,
                                            $spc_total
                                        );
                                    }
                                }
                                break;
		                }
		            }
		        }

                $dtr_entry['dayOfDate'] = $day[$recordWeekDay];
                $dtr_entry['late'] = empty($spc_scheduleForRecord) ? $late : $spc_late;
                $dtr_entry['undertime'] = empty($spc_scheduleForRecord) ? $undertime : $spc_undertime;
                $dtr_entry['hoursRendered'] = empty($spc_scheduleForRecord) ? $total : $spc_total;

		        $dtr_entry['schedule'] = empty($spc_scheduleForRecord) ? $scheduleForRecord : $spc_scheduleForRecord;
		        array_push($dtr,$dtr_entry);
		        $scheduleForRecord = [];
		        $spc_scheduleForRecord = [];
		    }
		}

		return $dtr;
    }
    

    /**
     * Computes the number of hours late and undertime for a given DTR entry
     * from a given schedule information.
     * 
     * Params:
     * - $timeIn - Schedule's time-in 
     * - $imeOut - Schedule's time-out
     * - $expectedHours - Total renderable hours
     * - $recordIn - timestamp for Record Time-in
     * - $recordOut - timestamp for Record Time-out
     * - &$late - reference for storing computed lates
     * - &$undertime - reference for storing computed undertimes
     * - &$total - reference for storing total hours rendered
     */
    private function compute_late_undertime(
        $timeIn,
        $timeOut,
        $expectedHours,
        $recordIn,
        $recordOut,
        &$late,
        &$undertime,
        &$total
    ){
        
        $computedLate = ($recordIn==null)? $expectedHours : compute_tardiness($timeIn, $recordIn, $expectedHours);
        $computedUndertime = ($recordOut==null)? $expectedHours : compute_tardiness($recordOut, $timeOut, $expectedHours);

        $late += $computedLate;
        $undertime += $computedUndertime;
        $total += $expectedHours - ($computedLate + $computedUndertime);
        $total = $total <= 0 ? 0:$total;    // make sure we return a non-negative value.
    }

    /**
     * Loads the Summary Report for the Working Scholars in the selected Department.
     * This is achieved by summing up all renderable (gross) duty hours, computed lates, 
     * undertimes and Overall Total (with deductions from lates and undertimes).
     * 
     */
    public function loadSummary(){
        
        $school_year = isset($_POST['school-year'])? $_POST['school-year']:'';
        $period = isset($_POST['period'])? $_POST['period'] + 0:'';
        $month = isset($_POST['month'])? $_POST['month'] + 0:'';
        $department = isset($_POST['department'])? $_POST['department'] + 0:'';

        $date_bounds = $this->get_period_bounds($school_year, $period, $month);
        $dateStart = $date_bounds['dateStart'];
        $dateEnd = $date_bounds['dateEnd'];

        // For our result...
        $result = [];

        // We will have a custom SQL statement for this feature 

        $sql = "SELECT WS.idnumber, WS.wsName,
            sum(hoursRendered) + sum(late) + sum(undertime) AS 'gross_hours',
            sum(late) as 'lates',
            sum(undertime) as 'undertimes',
            sum(hoursRendered) AS 'hours_rendered'
            FROM Record INNER JOIN WS ON WS.idnumber = Record.idnumber
            WHERE WS.depAssigned = ? AND Record.recorddate BETWEEN 
            $dateStart AND $dateEnd 
            AND Record.timeIn IS NOT NULL
            AND Record.[timeOut] IS NOT NULL
            GROUP BY WS.idnumber, WS.wsName 
            ORDER BY WS.wsName ASC";

        $overtimeSql = "SELECT idnumber, sum(ottotal) as 'total_overtime' FROM Overtime
        WHERE otdate between $dateStart and $dateEnd
        GROUP BY idnumber";

        $params = [
            $department
        ];

        
        // We will use our Finder class to perform custom SQL.
        $summary_temp = $this->finder_obj
        ->ready()
        ->customSQL($sql)
        ->setBindParams($params)
        ->result_set();

        // We will select our Overtime for each Working Scholars.
        $otRes = [];

        foreach(($res = $this->finder_obj->ready()->customSql($overtimeSql)->result_set()) as $ot) {
            $otRes[$ot->get("idnumber")] = $ot->get("total_overtime") + 0;
        }


        foreach($summary_temp as $summ){
            $fields = $summ->get();

            $idnumber = $fields['idnumber']; 

            // This properly encodes WS Names having UTF characters like N-nye etc.
            $ws_name = utf8_encode($fields['wsName']);
            $fields['wsName'] = $ws_name;

            // We don't want to insert the idnumber since we will use this as index.
            unset($fields['idnumber']);

            // We will place the POSTed data as well. This makes INSERTing later faster.
            $fields['department'] = $department;
            $fields['schoolYear'] = $school_year;
            $fields['period'] = $period;
            $fields['month'] = $month;

            // We will add our overtime on top of the existing number of hours.
            if(!empty($otRes) && isset($otRes[$idnumber])){
                $overtime = $otRes[$idnumber];
                $fields['overtimes'] = $overtime;
                $fields['hours_rendered'] += $overtime; 
            }
            else
                $fields['overtimes'] = 0;

            // Save all in reult under index using the WS's ID Number. 
            $result[$idnumber] = $fields;
        }
        

        echo json_encode($result);
    }

    /**
     * Saves the Working Scholar's Allowance Summary in the given period and month
     * on the given school year.
     */
    public function saveSummary(){

        // We get all submitted data.
        $idnumber = isset($_POST['idnumber'])? $_POST['idnumber']:'';
        $school_year = isset($_POST['school-year'])? $_POST['school-year']:'';
        $period = isset($_POST['period'])? $_POST['period'] + 0:'';
        $month = isset($_POST['month'])? $_POST['month'] + 0:'';

        $grossHours = isset($_POST['grossHours'])? $_POST['grossHours'] + 0: 0.0;
        $overtime = isset($_POST['overtime'])? $_POST['overtime'] + 0: 0.0;
        $lates = isset($_POST['lates'])? $_POST['lates'] + 0: 0.0;
        $undertimes = isset($_POST['undertimes'])? $_POST['undertimes'] + 0: 0.0;
        $hoursRendered = isset($_POST['hoursRendered'])? $_POST['hoursRendered'] + 0: 0.0;

        // Prepare for insertion. Store all submitted data into array.
        $data = [
            'ws_idnumber' => $idnumber,
            'school_year' => $school_year,
            'dtr_period' => $period,
            'dtr_month' => $month,
            'gross_duty_hours' => $grossHours,
            'total_overtime' => $overtime,
            'total_late' => $lates,
            'total_undertime' => $undertimes,
            'overall_total' => $hoursRendered,
            'update_timestamp'=> 'CURRENT_TIMESTAMP'
        ];

        // Before INSERTing, we check first all our Summary entries (if any) for any 
        // duplications. Once a duplicate is found, we just simply UPDATE the entry.
        $summary_temp = $this->allowance_summary_obj
        ->ready()
        ->find()
        ->where([
            'ws_idnumber' => $idnumber,
            'school_year' => $school_year,
            'dtr_period' => $period,
            'dtr_month' => $month
        ])
        ->result_set();

        if(!empty($summary_temp)){
            foreach($summary_temp as $summary){
                if($summary->get("allowance_status") === 'RELEASED'){
                    echo "(RELEASED)";
                    return;
                }
                $this->allowance_summary_obj
                ->ready()
                ->update($data)
                ->where([
                    'id' => $summary->get('id')
                ])
                ->go();
            }
        }
        else{
            $this->allowance_summary_obj
            ->ready()
            ->create($data)
            ->insert()
            ->go();
        }
        
        $data['success'] = true;
        $data['status'] = "(SAVE SUCCESS)";
        echo json_encode($data);
    }

    public function retrieveScheduleAsJSON(){
        if(!isset($_POST['req']))
            die(400);

        session_start();

        $idnumber = str_replace("ws", "",$_SESSION['user_id']);
        $month = date('m') + 0;
        $year = date('Y') + 0;
        // First Semester
        if($month >= 6 && $month < 10){
            $semester = 1;
            $semesterWord = "FIRST SEMESTER";
        }
        // Second Semester
        else if($month >= 10 && $month <= 12 || $month < 3){
            $semester = 2;
            $semesterWord = "SECOND SEMESTER";
        }
        // Summer Class
        else{
            $semester = 3;
            $semesterWord = "SUMMER";
        }
        
        // decide the school year depending on the current month and year
        if($month > 6)
            $schoolYear = date('Y') . "-" . ((date('Y')+0) + 1);
        else
            $schoolYear = ((date('Y')+0) - 1) . "-" . date('Y');

        $schedules = $this->model('Finder')
        ->ready()
        ->select()
        ->from(['Schedule'])
        ->where([
            'idnumber'=>$idnumber,
            'semester'=>$semester,
            'schoolYear'=>$schoolYear,
            'scheduleType' => 'REG'
        ])
        ->order_by([
            "(CASE schedDay 
            WHEN 'M' THEN 1 
            WHEN 'Tu' THEN 2 
            WHEN 'W' THEN 3 
            WHEN 'Th' THEN 4 
            WHEN 'F' THEN 5 
            WHEN 'S' THEN 6 
            END)" => '',
            'tin'=>'',
            'tout'=>''
        ])
        // ->get_query_string()
        ->go();

        $res = [];
        $res['semester'] = $semesterWord;
        $res['schedule'] = [];

        $days = [
            'M' => "MONDAY",
            'Tu'=> "TUESDAY",
            'W' => "WEDNESDAY",
            'Th'=> "THURSDAY",
            'F' => "FRIDAY",
            'S' => "SATURDAY"
        ];

        $previousItem = null;
        
        if(!empty($schedules))
        foreach($schedules as $sched){
            $schedule = [];

            $schedule['day'] = $days[$sched->get('schedDay')];
            $schedule['rawTimeInValue'] = [];
            $schedule['rawTimeOutValue'] = [];
            $schedule['total'] = [];

            $timeIn = date_format($sched->get('tin'), "h:i A");
            $timeOut = date_format($sched->get('tout'), "h:i A");

            $timeValue = $timeIn . " - " . $timeOut;
            $times = $timeValue;

            // append the time schedule of the current iteration's time schedule
            // with an HTML line break.
            if(isset($previousItem) && $previousItem['day'] === $days[$sched->get('schedDay')]){
                $previousItem['time'] .= "<br>" .$times;
                array_push($previousItem['total'], $sched->get('totalHours'));

                array_push($previousItem['rawTimeInValue'], $timeIn);
                array_push($previousItem['rawTimeOutValue'], $timeOut);

                continue;
            }
            // We set our timezone first to "Asia/Manila"
            date_default_timezone_set('Asia/Manila'); 

            // check if this schedule's day matches today
            $schedule['isForToday'] = $days[$sched->get('schedDay')] === strtoupper(date('l'));

            $schedule['time'] = $times;
            array_push($schedule['rawTimeInValue'], $timeIn);
            array_push($schedule['rawTimeOutValue'], $timeOut);

            array_push($schedule['total'], $sched->get('totalHours'));
            array_push($res['schedule'], $schedule);

            $previousItem = &$res['schedule'][count($res['schedule'])-1];
        }
        
        foreach($res['schedule'] as $scheds){
            if($scheds['isForToday']){
                unset($scheds['isForToday'], $scheds['time']);
                $res['forToday'] = $scheds;
            }
        }
        echo json_encode($res);
    }

    /**
     * Marks the loaded summary from the given period, month and school year as RELEASED.
     */
    public function release(){
        if(!isset($_POST['req'])){
            echo json_encode([
                'err' => 'Invalid request handled'
            ]);
            return;
        }

        $department = $_POST['department'] + 0;
        $schoolYear = $_POST['school-year'];
        $period = $_POST['period'] + 0;
        $month = $_POST['month'] + 0;

        $customSql = "SELECT * FROM AllowanceSummary WHERE ws_idnumber in (SELECT idnumber FROM WS WHERE depAssigned = ?)
            AND school_year = ? AND dtr_period = ? AND dtr_month = ? AND
            allowance_status = 'UNRELEASED';";

        $updateReleased = "UPDATE AllowanceSummary SET allowance_status = 'RELEASED'
            WHERE ws_idnumber in (SELECT idnumber FROM WS WHERE depAssigned = ?)
            AND school_year = ? AND dtr_period = ? AND dtr_month = ?;
        ";

        $bindParams = [ $department, $schoolYear, $period, $month ];

        $res = $this->model('Finder')
        ->ready()
        ->customSql($customSql)
        ->setBindParams($bindParams)
        ->result_set();

        // Update to RELEASED status.
        if(count($res) > 0){
            $this->model('Finder')
            ->ready()
            ->customSql($updateReleased)
            ->setBindParams($bindParams)
            ->go();

            echo json_encode([
                'status' => "(RELEASE SUCCESSFUL)",
            ]);
        }else{
            echo json_encode([
                'status' => "(SUMMARY UNSAVED OR RELEASED)",
            ]);
        }
    }

}