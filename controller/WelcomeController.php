<?php
include_once 'view/Welcome.php';
include_once 'model/MatStatisticModel.php';


class WelcomeController{
    private $view;
    private $type;
    private $model;

    function __construct($bdd){
        $this->model = new MatStatisticModel($bdd);
        $this -> view = new Welcome($bdd);
        $this->type = "!";
    }

    function setType($type){
        $this->type = $type;
    }

    function launch($message){
        $this->view->setMessage($message,'pagemessage');

        // add mat parameters
        if($this->type == "!"){
            $this->model->computeForAll();
        }
        else{

            $this->model->computeForType($this->type);
        }
        $this->view->setMatInfos($this->model->statePoints, $this->model->nbMatDisp, $this->model->nbMatTotal, $this->type);

        $this->view->launch();
    }
}
?>
