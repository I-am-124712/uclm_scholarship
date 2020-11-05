<?php

class Records extends Controller {

    public function index(){
        session_start();
        $this->trap_no_user_session();
    }

    public function dtr(){
        session_start();
        $this->trap_no_user_session();
        if(isset($_GET['req'])){
            $this->get_dtr_info();
        }
        else
            return $this->view('dtr');
    }

    private function get_dtr_info(){
        $school_year = isset($_GET['school-year'])? $_GET['school-year']:'';
        $department = isset($_GET['department'])? $_GET['department']+0:'';
        $semester = isset($_GET['semester'])? $_GET['semester']+0:'';
        $period = isset($_GET['period'])? $_GET['period']:'';
        $month = isset($_GET['month'])? $_GET['month']:'';
        $hide = isset($_GET['hide']);

        // Select working scholars first. We will be performing
        // separate queries for every Working Scholars.
        $working_scholars = $this->model('WS')
        ->ready()
        ->find([
            'columns' => ['idnumber', 'wsName']
        ])
        ->where([
            'depAssigned' => $department
        ])
        ->go();

        
        if(!empty($working_scholars)){
            
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