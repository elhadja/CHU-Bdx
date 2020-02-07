<?php
include_once 'view/MattressTabView.php';
include_once 'model/GetTabModel.php';

class MattressTabController
{
    private $model;
    private $view;
    
    public function __construct($bdd){
        $this->model = new GetTabModel($bdd);
        $this->view = new MattressTabView();
    }
    
    public function launch($type){
        $this->view->setType($type);
        $allMattress = $this->model->getAllMattress('!');
        
        $this->view->addAllMattress($allMattress);
        
        $this->view->launch();
    }
    
}
?>