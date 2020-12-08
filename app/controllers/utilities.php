<?php

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
                    $_POST["department"]: 
                    "MY DAILY TIME RECORD";

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