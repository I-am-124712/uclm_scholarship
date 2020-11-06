<?php

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

                $dtr = array();
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
                    'idnumber' => $working->get_fields()['idnumber'],
                    'between' => [
                        'column' => 'recorddate',
                        'arg1' => $dateStart,
                        'arg2' => $dateEnd
                    ]
                ])
                ->go();

                if(!empty($result_dtr)){
                    foreach($result_dtr as $record){
                        array_push($dtr,$record->get_fields());
                    }

                }
                array_push($data,[
                    'idnumber' => $working->get_fields('idnumber'),
                    'wsName' => utf8_encode($working->get_fields('wsName')),
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
                    'deptId' => $dept->get_fields()['deptId'],
                    'departmentName' => $dept->get_fields()['departmentName'],
                ]);
            }

            echo json_encode($departments);
        }
    }

    
}