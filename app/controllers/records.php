<?php

require './app/core/UtilFunctions.php';

class Records extends Controller {

    public function index(){
        session_start();
        $this->trap_no_user_session();
    }

    public function dtr(){
        session_start();
        $this->trap_no_user_session();
        if(isset($_POST['req'])){
            $this->get_dtr_info();
        }
        else{
            return $this->view('dtr');
        }
    }

    private function get_dtr_info(){
        $max_month_days = [31,29,31,30,31,30,31,31,30,31,30,31];
        $data = array();     // for main result Object

        $school_year = isset($_POST['school-year'])? $_POST['school-year']:'';
        $department = isset($_POST['department'])? $_POST['department']+0:'';
        $semester = isset($_POST['semester'])? $_POST['semester']+0:'';
        $period = isset($_POST['period'])? $_POST['period']+0:'';
        $month = isset($_POST['month'])? $_POST['month']+0:'';
        $hide = isset($_POST['hide']);
        $load_method = isset($_POST['load-method'])? $_POST['load-method']:'auto';

        // Select working scholars first. We will be performing
        // separate queries for every Working Scholars.
        $working_scholars = $this->model('WS')
        ->ready()
        ->find()
        ->where([
            'depAssigned' => $department
        ])
        ->go();

        
        if(!empty($working_scholars)){

            foreach($working_scholars as $working){

                // for WS information (and Record data)
                $ws_data = array();


                /* Retrieve Records */

                // This one decides what year to use by deciding if the month
                // selected is inclusive to a given school year.
                $record_year = ($month<5)? explode('-',$school_year)[1]:explode('-',$school_year)[0];
                $dateStart = '';
                $dateEnd = '';

                switch($period){
                    case 1:// First Period
                        $dateStart = 'DateFromParts(' . $record_year . ','. ($month+1) . ', 1)';
                        $dateEnd = 'DateFromParts(' . $record_year . ','. ($month+1) . ', 15)';
                        break;
                    case 2: // Second Period
                        $dateStart = 'DateFromParts(' . $record_year . ','. ($month+1) . ', 16)';
                        $dateEnd = 'DateFromParts(' . $record_year . ','. ($month+1) . ', '.$max_month_days[$month].')';

                }

                // declare arrays for DTR entries and Schedule data...
                $dtr = array();
                $schedules = array();

                if($hide){
                    $result_dtr = $this->model('Record')
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
                        'idnumber' => $working->get()['idnumber'],
                        'between' => [
                            'column' => 'recorddate',
                            'arg1' => $dateStart,
                            'arg2' => $dateEnd
                        ],
                        'NOT NULL' => [
                            'logic' => 'AND',
                            'timeIn',
                            'timeOut'
                        ]
                    ])
                    ->go();
                    // ->get_query_string();
                    // echo($result_dtr);
                }
                else{
                    $result_dtr = $this->model('Record')
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
                        'idnumber' => $working->get('idnumber'),
                        'between' => [
                            'column' => 'recorddate',
                            'arg1' => $dateStart,
                            'arg2' => $dateEnd
                        ],
                    ])
                    ->go();
                }

                $schedule = $this->model('Schedule')
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
                    'idnumber' => $working->get('idnumber'),
                    'schoolYear' => $school_year,
                    'semester' => $semester
                ])
                ->go();

                
                // We will retrieve all the WS's schedule for
                // the manual adding of schedule for every
                // DTR entry if user chooses manual assignment.
                
                if(!empty($schedule)){
                    foreach($schedule as $sched){
                        array_push($schedules, $sched->get());
                    }
                }


                if(!empty($result_dtr)){
                    $scheduleForRecord = [];
                    foreach($result_dtr as $record){

                        $dtr_entry = $record->get();
                        $day = [null,"M","Tu","W","Th","F","S"];

                        // Get the Day of week for the current DTR entry we are accessing...
                        $recordWeekDay = date('w', strtotime(date_format($record->get('recorddate'),'M-d-Y')));
                        // echo $recordWeekDay;
                        if(!empty($schedule)){
                            $late = 0;
                            $undertime = 0;
                            $total = 0;
                            $record_in = $dtr_entry['timeIn'];
                            $record_out = $dtr_entry['timeOut'];


                            $lates_undertimes = [];
                            foreach($schedule as $sched){
                                switch($sched->get('scheduleType')){
                                    case 'REG':
                                        $matchingSched = $sched->is_match([
                                            'schedDay' => $day[$recordWeekDay]
                                        ]);


                                        // we'll use shortcut single-line if-statements
                                        if($matchingSched != null) {
                                            array_push($scheduleForRecord,[
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
                                                // $record_out_string = "";


                                                $late += ($record_in==null)? $matchingSched->get('totalHours') : compute_tardiness($tin, $record_in, $expectedHours);
                                                $undertime += ($record_out==null)? $matchingSched->get('totalHours') : compute_tardiness($record_out, $tout, $expectedHours);
                                                $total += $matchingSched->get('totalHours') - ($late + $undertime);
                                                $total = $total <= 0 ? 0:$total;
                                            }
                                            // echo var_export($record->get())."\n";
                                        }
                                        else{
                                            // ($scheduleForRecord = []);
                                        }
                                        break;
                                    case 'SPC':
                                }
                            }

                            $dtr_entry = array_merge($dtr_entry, [
                                'late' => $late,
                                'undertime' => $undertime,
                                'hoursRendered' => $total
                            ]);
                        }
                        $dtr_entry = array_merge($dtr_entry, ['schedule' => $scheduleForRecord]);
                        array_push($dtr,$dtr_entry);
                        $scheduleForRecord = [];
                    }
                    // var_export($scheduleForRecord);

                }

                

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

    public function summary(){
        session_start();
        $this->trap_no_user_session();
    }

    public function overtime(){
        session_start();
        $this->trap_no_user_session();
    }


    public function get_departments(){
        session_start();
        $this->trap_no_user_session();

        if(isset($_GET['req'])){

            $result = $this->model('Departments')
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

    
}