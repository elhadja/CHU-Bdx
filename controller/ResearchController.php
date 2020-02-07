<?php

include_once 'model/GetTabModel.php';
include_once 'view/ResearchView.php';

class ResearchController
{
    private $view;
    private $model;

    public function __construct($bdd){

        $this->model = new GetTabModel($bdd);
        $this->view = new ResearchView();
    }

    public function launch(){
        $results = $this->model->getResearchTab();

        $this->view->launch($results);
    }
}
?>