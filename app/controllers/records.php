<?php

require './app/core/UtilFunctions.php';

class Records extends Controller {

    private $departments_obj;
    private $ws_obj;
    private $schedule_obj;
    private $record_obj;

    public function __construct(){
        $this->departments_obj = $this->model('Departments');
        $this->ws_obj = $this->model('WS');
        $this->schedule_obj = $this->model('Schedule');
        $this->record_obj = $this->model('Record');
    }

    public function index(){
        session_start();
        $this->trap_no_user_session();
    }
    
    public function dtr(){
        session_start();
        $this->trap_no_user_session();

        // This method will return a specific view for users
        // with privilege = 3 (Working Scholars)
        if($_SESSION['user_privilege'] == 3)
            header('Location: /uclm_scholarship/records/my_dtr');

        if(isset($_POST['req'])){
            $this->get_dtr_info();
        }
        else{
            return $this->view('dtr');
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
            $result['records'] = $dtr_plotted;

            echo json_encode($result);
        }
        else{
            return $this->view('dtr-ws');
        }
    }
    
    public function summary(){
        session_start();
        $this->trap_no_user_session();
    }

    public function overtime(){
        session_start();
        $this->trap_no_user_session();
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
        ->go();

        
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
                $period_bounds = $this->get_period_bounds($school_year,$period,$month);

                $dateStart = $period_bounds['dateStart'];
                $dateEnd = $period_bounds['dateEnd'];


                // Retrieve DTR entries...
                $result_dtr = $this->retrieve_dtr($working, $dateStart, $dateEnd, $hide);

                // Retrieve the schedules for the working Scholar
                $schedule = $this->retrieve_schedule($working, $school_year, $semester);     

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
        This function breaks down the Date string and determines the 
        Start and end date of a Period from a given school year and
        Month.

        Params:
            - $school_year = string representing the School Year
            - $period = an integer specifying the period
            - $month = an integer identifying the month
        
        Returns:
            - ['dateStart' => string, 'dateEnd' => string] = an assoc array 
            containing the SQL DateFromParts() string of both inclusive dates
            for the specified Period.
    */

    private function get_period_bounds($school_year, $period, $month){
        $record_year = ($month<5)? explode('-',$school_year)[1]:explode('-',$school_year)[0];
        $max_month_days = [31,29,31,30,31,30,31,31,30,31,30,31];
        $dateStart;
        $dateEnd;
        switch($period){
            case 1:// First Period
                $dateStart = 'DateFromParts(' . $record_year . ','. ($month+1) . ', 1)';
                $dateEnd = 'DateFromParts(' . $record_year . ','. ($month+1) . ', 15)';
                break;
            case 2: // Second Period
                $dateStart = 'DateFromParts(' . $record_year . ','. ($month+1) . ', 16)';
                $dateEnd = 'DateFromParts(' . $record_year . ','. ($month+1) . ', '.$max_month_days[$month].')';

        }
        return [ 'dateStart' => $dateStart, 'dateEnd' => $dateEnd];
    }


    /** 
        This function retrieves the DTR Data for a given Working Scholar $ws
        on a given period bound $date_start and $date_end. 

        Params:
            - $ws = A WS Model Object for basis using its 'idnumber'
            - $date_start = An SQL DateFromPart() method String for the starting inclusive date to retrieve.
            - $date_end = An SQL DateFromPart() method String for the closing inclusive date to retrieve.
            - $hide_nulls = Hide Record entries having NULL for times-in or times-out.

        Returns:
            - $result_dtr = An Array of Record Model objects.
    */
    private function retrieve_dtr($ws, $date_start, $date_end, $hide_nulls){

        if($hide_nulls){
            $result_dtr = $this->record_obj
            ->ready()
            ->find([
                'columns' => [
                    'record_id',
                    'recorddate',
                    'timeIn',
                    'timeOut',
                    'hoursRendered'
                ]
            ])
            ->where([
                'idnumber' => $ws->get('idnumber'),
                'between' => [
                    'column' => 'recorddate',
                    'arg1' => $date_start,
                    'arg2' => $date_end
                ],
                'NOT NULL' => [
                    'logic' => 'AND',
                    'timeIn',
                    'timeOut'
                ]
            ])
            ->result_set();
        }
        else{
            $result_dtr = $this->record_obj
            ->ready()
            ->find([
                'columns' => [
                    'record_id',
                    'recorddate',
                    'timeIn',
                    'timeOut',
                    'hoursRendered'
                ]
            ])
            ->where([
                'idnumber' => $ws->get('idnumber'),
                'between' => [
                    'column' => 'recorddate',
                    'arg1' => $date_start,
                    'arg2' => $date_end
                ],
            ])
            ->result_set();
        }
        return $result_dtr;
    }


    /**  
        This function retrieves the Schedules of the given Working Scholar $ws 
        during a given Semester on the given School Year.

        Params:
            - $ws = A WS Model object 
            - $school_year = A string that represents the school year (must be two 4-digit year strings)
                separated by a dash with no whitespaces (eg. "2015-2016")
            - $semester = An integer representing the semester of the schedule to be loaded.

        Returns:
            - $schedule = An Array of Schedule Model object retrieved from the performed query.
    */
    private function retrieve_schedule($ws, $school_year, $semester){

        $schedule = $this->schedule_obj
        ->ready()
        ->find([
            'columns' => [
                'schedule_id',
                'scheduleType',
                'schedDay',
                'tin', 'tout',
                'totalHours'
            ]
        ])
        ->where([
            'idnumber' => $ws->get('idnumber'),
            'schoolYear' => $school_year,
            'semester' => $semester
        ])
        ->result_set();

             
        return $schedule;
    }

    /** 
        This function plots each of the Working Scholar's respective
        Schedules in each of their DTR entries. The schedule to be
        associated for a DTR entry will depend on the DTR record's date
        and the day of week it belongs.
        
        Params:
            - $result_dtr = array of Record Model objects
            - $schedule = array of Schedule Model objects
            - $load_method = specifies the load method (accepted values: 'manual', 'auto')

        Returns:
            - $dtr = new Array of DTR entries with corresponding Schedules.
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
		        
		        if(!empty($schedule)){
                    // For Regular Schedule computation...
		            $late = 0;
		            $undertime = 0;
                    $total = 0;
                    
                    // For Specific Schedule computation...
                    $spc_late = 0;
                    $spc_undertime = 0;
                    $spc_total = 0;

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

		                                $late += ($record_in==null)? $matchingSched->get('totalHours') : compute_tardiness($tin, $record_in, $expectedHours);
		                                $undertime += ($record_out==null)? $matchingSched->get('totalHours') : compute_tardiness($record_out, $tout, $expectedHours);
		                                $total += $matchingSched->get('totalHours') - ($late + $undertime);
		                                $total = $total <= 0 ? 0:$total;    // Normalize
		                            }
		                        }
		                        break;
                            case 'SPC':
                                $recorddate = date_format($record->get('recorddate'),'m/d/Y');

                                // echo "Entered SPC case...\n";
                                // echo "Record Date: ".$recorddate."\n";
                                // echo "Schedule Date: ".$sched->get('schedDay')."\n";

                                if($sched->get('schedDay') === $recorddate){
                                    // echo "Entered Conditional::\n";
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
    
                                        $spc_late += ($record_in==null)? $sched->get('totalHours') : compute_tardiness($spc_tin, $record_in, $spc_expectedHours);
                                        $spc_undertime += ($record_out==null)? $sched->get('totalHours') : compute_tardiness($record_out, $spc_tout, $spc_expectedHours);
                                        $spc_total += $sched->get('totalHours') - ($spc_late + $spc_undertime);
                                        $spc_total = $spc_total <= 0 ? 0:$spc_total;    // Normalize
                                    }
                                }
                                break;
		                }
		            }


		            $dtr_entry['late'] = empty($spc_scheduleForRecord) ? $late : $spc_late;
		            $dtr_entry['undertime'] = empty($spc_scheduleForRecord) ? $undertime : $spc_undertime;
		            $dtr_entry['hoursRendered'] = empty($spc_scheduleForRecord) ? $total : $spc_total;

		        }
		        $dtr_entry['schedule'] = empty($spc_scheduleForRecord) ? $scheduleForRecord : $spc_scheduleForRecord;
		        array_push($dtr,$dtr_entry);
		        $scheduleForRecord = [];
		        $spc_scheduleForRecord = [];
		    }
		}

		return $dtr;
    }
    

    private function compute_late_undertime(){
        
    }
}