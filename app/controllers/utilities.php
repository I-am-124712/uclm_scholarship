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
        if(isset($_POST['_req'])){
        }
        switch($docName){
            case "dtr":
            case "DTR":
                $this->dtr->prepare()->getGeneratedPDF();
                break;
            case "Summary":
                $this->summary->getGeneratedPDF();
        }
    }

}

class DTR extends Controller {

    private $args = [];

    public function prepare(){
        $this->args['wsName'] = "SAMPLE";
        return $this;
    }

    public function getGeneratedPDF(){
        if(isset($this->args) && !empty($this->args))
            return $this->view_custom("./app/views/pdfTemplates/dtr_linker.html", "/pdfTemplates/dtr-pdf-template", $this->args);
        else
            echo "MAMA! Just killed a man!";        
    }
}
class Summary extends Controller{

    public function prepare(){
        return $this;
    }

    public function getGeneratedPDF($args = []){
        if(isset($args) && !empty($args))
            return $this->view("./app/views/pdfTemplates/summary_linker.html", "/pdfTemplates/summary-pdf-template", $this->args);
        else
            echo "MAMA! Just killed a man!";
    }
}