<?php
include_once 'view/AddMatView.php';
include_once 'view/Welcome.php';
include_once 'model/InsertRequestModel.php';
include_once 'model/MatStatisticModel.php';

class AddMatController
{
    private $view;

    function __construct($bdd){
        $this->view = new AddMatView();
        
        $welcomeView = new Welcome($bdd);
        $model = new MatStatisticModel($bdd);
        $model->computeForAll();
        $welcomeView->setMatInfos($model->statePoints, $model->nbMatDisp, $model->nbMatTotal, "!");

        $request = new InsertRequestModel($bdd);
        $msg = $request->insertMattress();

        switch($msg){
            case 'NAME_EMPTY':
                $this->view->setMessage('Veuillez saisir un nom', 'name');
                break;
            case 'TYPE_EMPTY':
                $this->view->setMessage('Veuillez choisir un type', 'type');
                break;
            case 'STATE_EMPTY':
                $this->view->setMessage('Veuillez choisir un état', 'state');
                break;
            case 'INVALID_NAME':
                $this->view->setMessage('Un matelas porte déjà ce nom', 'name');
                break;
            case 'ERROR_BDD':
                $this->view = $welcomeView;
                $this->view->setMessage('-- DATABASE ERROR --', 'pagemessage');
                break;
            case 'SUCCESS':
                $this->view = $welcomeView;
                $this->view->setMessage('Matelas ajouté', 'pagemessage');
                break;
        }
    }

    function launch(){
        $this->view->launch();
    }
}
?>