<?php
require './app/core/UtilFunctions.php';

class Dash extends Controller {

    public function index(){
        session_start();
        
        $this->trap_no_user_session();

        if(isset($_SESSION['user_privilege']))
        {
            if($_SESSION['user_privilege'] !== 4){
                $data = $this->load_dashboard_data();
                $finder = $this->model('Finder');
                
                $dataPanel = $this->view('dashboard',[
                    'data' => $data,
                    'finder' => $finder
                ]);
                return $dataPanel;
            }
                
            // Show only USO adverts for guests users.
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

    public function ws($args = null){
        session_start();
        $this->trap_no_user_session();

        // We should remove the generalView session token first just to make sure.
        unset($_SESSION['generalView']);    
        
        // Block Users with User Privilege = 3 (WS), 85 (Departmental Account)
        if($_SESSION['user_privilege']==3 || $_SESSION['user_privilege']==85){
            header('Location: /uclm_scholarship/home');
        }

        if($args === 'requests'){
            $this->wsRequest();
        }

        switch($args){
            case 'requests':
                $this->wsRequest();
                break;
            case 'general':
                $_SESSION['generalView'] = $args; 
                if($_SESSION['generalView'])
                    $this->wsViewGeneral();
                break;
            default:
                $this->wsViewGrouped();
        }

    }


    private function wsViewGeneral(){
        if(session_status() !== PHP_SESSION_ACTIVE)
            session_start();
        
        // We should remove the selected departmentID saved into our SESSION when we
        // are in general view.
        unset($_SESSION['department']);

        $working_scholars = $this->model("WS")
        ->ready()
        ->find()
        ->result_set();

        return $this->view('ws',[
            'ws' => $working_scholars,
            'allow_edit' => isset($_GET['allow_edit']),
            'generalView' => true
        ]);
    } 


    public function wsViewGrouped(){
        if(session_status() !== PHP_SESSION_ACTIVE)
            session_start();


        // Three-way search if we have a department ID passed either through
        // URL or post, or if we stored it in session. Lol this is sh*t
        $deptId = isset($_GET['department'])? $_GET['department']:(
                    isset($_GET['selected-department'])? $_GET['selected-department']:(
                        isset($_SESSION['department'])? $_SESSION['department']:0));
                            
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

    private function wsRequest(){
        return $this->view('ws-request');
    }

    public function ws_information($idnumber = ''){

        session_start();
        $this->trap_no_user_session();
        
        // Block Users with User Privilege = 3 (WS), 85 (Departmental Account)
        if($_SESSION['user_privilege']== 3 || $_SESSION['user_privilege']== 85){
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

        // We will then query all departments for the selectionbox
        $departmentsList = $this->model('Departments')
        ->ready()
        ->find()
        ->result_set();

        return $this->view('ws-information',[
            'ws' => $selected_ws, 
            'user' => $ws_user,
            'department' => $dept_assigned, 
            'success' => false,
            'departmentsList' => $departmentsList
        ]);
    }

    /// dashboard data and statistics chu2x loading here aron chuy tan-awn
    private function load_dashboard_data(){

        // Number of Departments
        $departments = $this->model('Departments')
        ->ready()
        ->find()
        ->result_set();

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
            'ws_with_ot' => $ws_with_ot,
            'departmentCount'=> count($departments)
        ];

    }

    /**
     * Returns a JSON array of the posts made by the System/Department Admin.
     * 
     * @return string json encoded array of messages
     */
    public function retrieveAdminPosts(){
        if(!isset($_POST['req_id']))
            return;
        $sql = "SELECT [User].user_fname, [User].user_lname, [User].user_id, [User].user_photo,
            Posts.post_content, Posts.post_timestamp from [User]
            INNER JOIN Posts ON Posts.post_sender_user_id = [User].user_id
            ORDER BY Posts.post_timestamp DESC";

        $result = $this->model('Finder')
        ->ready()
        ->customSql($sql)
        ->result_set();

        $arrayResult = [];

        foreach($result as $post){
            $userFullName = $post->get('user_fname'). " ". $post->get('user_lname');
            $userId = $post->get('user_id');
            $userPhoto = $post->get('user_photo');
            $postText = $post->get('post_content');
            $postTimestamp = time_elapsed_string($post->get('post_timestamp')->format("Y-m-d H:i:s"));

            array_push($arrayResult, [
                'userFullName' => $userFullName,
                'userId'=> $userId,
                'userPhoto'=>$userPhoto,
                'postText'=> $postText,
                'postTimestamp'=> $postTimestamp
            ]);
        }

        echo json_encode($arrayResult);
    }

}