<?php

include_once 'model/MatStatisticModel.php';
include_once 'view/Welcome.php';
include_once 'model/DelPatModel.php';

class DelPatController
{
    private $view;
    
    public function __construct($bdd){
        $this->view = new Welcome($bdd);
        
        $model = new MatStatisticModel($bdd);
        $model->computeForAll();
        $this->view->setMatInfos($model->statePoints, $model->nbMatDisp, $model->nbMatTotal, "!");

        $model = new DelPatModel($bdd);
        $ans = $model->delete();
        
        switch($ans){
            case 'SUCCESS':
                $this->view->setMessage("Traitement supprimé", "pagemessage");
                break;
            case 'SUCCESS_PAT':
                $this->view->setMessage("Traitement et patient supprimés", "pagemessage");
                break;
            case 'ECHEC_PAT':
                $this->view->setMessage("Traitement supprimé, échec de la suppression du patient", "pagemessage");
                break;
            default:
                $this->view->setMessage("-- ERROR IN DELETEPAT MODEL --", "pagemessage");
                break;
        }
    }
    
    public function launch(){
        $this->view->launch();
    }
}
?>