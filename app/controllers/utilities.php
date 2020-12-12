<?php
require $_SERVER['DOCUMENT_ROOT'] . "/uclm_scholarship/app/core/UtilFunctions.php";

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
        $data = isset($_POST["data"]) ? json_decode($_POST['data'], true):"";
        $department = (isset($_POST["department"]) && $_POST["department"] !== "") ? 
                    $_POST["department"]: "MY DAILY TIME RECORD";

        $args = [
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
}

class DTR extends Controller {

    private $args = [];

    public function prepare($args){
        $this->args = $args;
        return $this;
    }

    public function getGeneratedPDF(){
        if(isset($this->args) && !empty($this->args))
            return $this->view_custom("./app/views/pdfTemplates/dtr_linker.html", "/pdfTemplates/dtr-pdf-template", $this->args);     
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
            return $this->view_custom("./app/views/pdfTemplates/summary_linker.html", "/pdfTemplates/summary-pdf-template", $this->args);
    }
}