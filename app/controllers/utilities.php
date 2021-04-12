<?php
require $_SERVER['DOCUMENT_ROOT'] . "/uclm_scholarship/app/core/UtilFunctions.php";
require $_SERVER['DOCUMENT_ROOT'] . "/uclm_scholarship/app/core/ConstantsAndVars.php";

class Utilities extends Controller {

    private DTR $dtr;
    private Summary $summary;

    public function __construct(){
        $this->dtr = new DTR();
        $this->summary = new Summary();
    }    

    public function index(){
        header('Location: /');
    }

    public function generate($format, $docName){
        session_start();

        $data = $_SESSION['data'];
        
        $schoolYear = $_SESSION['schoolYear'];
        $period = $_SESSION['period'];
        $month = $_SESSION['month'];
        $department = $_SESSION['department'];

        // Destroy these things
        unset($_SESSION['data']);
        unset($_SESSION['schoolYear']);
        unset($_SESSION['period']);
        unset($_SESSION['month']);
        unset($_SESSION['department']);

        $args = [
            'schoolYear' => $schoolYear,
            'period' => $period,
            'month' => $month,
            'department' => $department,
            'data' => $data,
            'dbFinder' => $this->model('Finder')
        ];

        switch($docName){
            case "dtr":
            case "DTR":
                $this->dtr->prepare($args)->getGeneratedPDF();
                break;
            case "Summary":
            case "summary":
                $this->summary->prepare($args)->getGeneratedPDF();
        }
    }

    /**
     * Utility function that returns a JSON encoded object containing
     * the time elapsed string.
     * 
     * @return string the time elapsed string from the given timestamp string.
     */
    public function updateTimeElapsedJSON(){
        $timeString = isset($_POST['timeString'])? $_POST['timeString'] : "";

        echo json_encode([
            'update' => time_elapsed_string($timeString)
        ]);
    }

    /**
     * Use a backend defined computation formula for computing total rendered overtime.
     * 
     * @return string JSON encoded result from the computation.
     */
    public function computeTotalOvertime() {
        if(!isset($_POST['ot-start']) || !isset($_POST['ot-end']))
            die;
        
        $overtimeStart = strtotime($_POST['ot-start']);
        $overtimeEnd = strtotime($_POST['ot-end']);
        
        if($overtimeStart === '' || $overtimeEnd === '')
            die;

        $totalOvertime = ($overtimeEnd - $overtimeStart) /3600;

        if($totalOvertime > 0.0){
            if((int)$totalOvertime + 0.5 <= $totalOvertime && $totalOvertime < ceil($totalOvertime))
                $totalOvertime = (int)$totalOvertime + 0.5;
            else if (floor($totalOvertime) <= $totalOvertime && $totalOvertime < (int)$totalOvertime + 0.5)
                $totalOvertime = floor($totalOvertime);
        }else
            $totalOvertime = 0;



        echo json_encode([
            'totalOvertime' => $totalOvertime
        ]);

    }

    public function hideSidebar(){

        session_start();

        if(isset($_POST['toggle-sidebar'])){
            $toggle = $_POST['toggle-sidebar'];
            echo "Toggled on backend";
            switch($toggle){
                case 'on':
                    $_SESSION['sidebar-visible'] = true;
                    break;
                case 'off':
                    $_SESSION['sidebar-visible'] = false;
                    break;
            }
        }
    }

    public function sendPost(){
        session_start();

        $data = isset($_POST["data"]) ? json_decode($_POST['data'], true):"";
        
        $schoolYear = $_POST['schoolYear'];
        $period = $_POST['period'];
        $month = $_POST['month'];
        $department = isset($_POST["department"]) ? 
                    $_POST["department"]: "MY DAILY TIME RECORD";

        $_SESSION['data'] = $data;
        $_SESSION['period'] = $period;
        $_SESSION['month'] = $month;
        $_SESSION['schoolYear'] = $schoolYear;
        $_SESSION['department'] = $department;

        
        $message = 'SUCCESS';
        echo json_encode([ 'status' => $message ]);
        die();
    }
}

class DTR extends Controller {

    private $args = [];

    public function prepare($args){
        $this->args = $args;
        return $this;
    }

    public function getGeneratedPDF(){
        if(isset($this->args) && !empty($this->args))
            return $this->view_custom("", "/pdfTemplates/dtr-pdf-template", $this->args);     
    }
}
class Summary extends Controller{

    private $args = [];

    public function prepare($args){
        $this->args = $args;
        return $this;
    }

    public function getGeneratedPDF(){
        if(isset($this->args) && !empty($this->args))
            return $this->view_custom("", "/pdfTemplates/summary-pdf-template", $this->args);
    }
}