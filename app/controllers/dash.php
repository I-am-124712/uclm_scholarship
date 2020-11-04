<?php

class Dash extends Controller {

    public function index(){
        session_start();
        
        $this->trap_no_user_session();

        if(isset($_SESSION['user_privilege']))
        {
            if($_SESSION['user_privilege'] !== 4){
                $data = $this->load_dashboard_data();
                
                $dataPanel = $this->view('dashboard',$data);
                return $dataPanel;
            }
                
            // Show only USO adverts for guests
            else{
                return $this->view('guest');
            }
        }
    }
    public function departments(){
        session_start();
        $this->trap_no_user_session();
        
        // Block Users with User Privilege = 3 (WS)
        if($_SESSION['user_privilege']==3){
            header('Location: /uclm_scholarship/home');
        }
            
        $departments = $this->model('Finder')
        ->ready()
        ->select([
            'Departments.deptId',
            'Departments.departmentName',
            "count(WS.depAssigned) as 'wsCount'"
        ])
        ->from([
            'Departments'
        ])
        ->join([
            'join-type' => 'INNER',
            'table' => 'WS',
            'on' =>[
                'conditions' => [
                    'WS.depAssigned = Departments.deptId'
                ]
            ]
        ])
        ->group([
            'WS.depAssigned',
            'Departments.deptId',
            'Departments.departmentName'
        ])
        ->having([
            'logic' => 'OR',
            'WS.depAssigned = Departments.deptId',
            'count(WS.depAssigned) >= 0'
        ])
        ->go();
        
        $department_details = [];
        foreach($departments as $department){
            array_push($department_details,[
                "deptId" => $department->get_fields()['deptId'],
                "departmentName" => $department->get_fields()['departmentName'],
                "wsCount" => $department->get_fields()['wsCount'],
            ]);
        }

        return $this->view('departments',$department_details);
    }

    public function ws(){
        session_start();
        $this->trap_no_user_session();
        
        // Block Users with User Privilege = 3 (WS)
        if($_SESSION['user_privilege']==3){
            header('Location: /uclm_scholarship/home');
        }

        $this->ws_view_only();
    }

    public function ws_view_only(){
        if(session_status() !== PHP_SESSION_ACTIVE)
            session_start();

        $deptId = isset($_GET['department'])? $_GET['department']:
                            (isset($_SESSION['department'])? $_SESSION['department']:0);
        $_SESSION['department'] = $deptId;

        $working_scholars = $this->model('WS')
        ->ready()
        ->find()
        ->where([
            'depAssigned' => $deptId
        ])
        ->order_by([
            'wsName' => 'ASC'
        ])
        ->go();

        return $this->view('ws',[
            'ws' => $working_scholars,
            'depAssigned' => $deptId,
            'allow_edit' => isset($_GET['allow_edit'])
        ]);
    }

    public function ws_information($idnumber = ''){

        session_start();
        $this->trap_no_user_session();
        
        // Block Users with User Privilege = 3 (WS)
        if($_SESSION['user_privilege']==3){
            header('Location: /uclm_scholarship/home');
        }

        if($idnumber === '')
            return;

        /// retrieve Working Scholar's data
        $selected_ws = $this->model('WS')
        ->ready()
        ->find()
        ->where([
            'idnumber' => $idnumber
        ])
        ->go()[0];

        /// we will need the department's name
        $dept_assigned = $this->model('Departments')
        ->ready()
        ->find()
        ->where([
            'deptId' => $selected_ws->get_fields()['depAssigned']
        ])
        ->go()[0];
        
        /// we will also need the working scholar's user account
        $ws_user = $this->model('User')
        ->ready()
        ->find()
        ->where([
            'user_id' => 'ws'.$idnumber
        ])
        ->go()[0];

        return $this->view('ws-information',[
            'ws' => $selected_ws, 
            'user' => $ws_user,
            'department' => $dept_assigned, 
            'success' => false
        ]);
    }

    /// dashboard data and statistics chu2x loading here aron chuy tan-awn
    private function load_dashboard_data(){

        // Current Number of Working Scholars
        $no_of_ws = $this->model('WS')
        ->ready()
        ->find([
            'columns' => ['count(*) as \'wsCount\'']
        ])
        ->go()[0]->get_fields()['wsCount'];


        // Working Scholars with Overtime
        $ws_with_ot = $this->model('Overtime')
        ->ready()
        ->find([
            'distinct' => true,
            'columns' => ['count(idnumber) as \'ws_with_ot\'']
        ])
        ->go()[0]->get_fields()['ws_with_ot'];

        return [
            'no_of_ws' => $no_of_ws,
            'ws_with_ot' => $ws_with_ot
        ];

    }
}