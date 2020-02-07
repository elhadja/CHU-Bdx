<?php

include_once 'model/MatStatisticModel.php';
include_once 'view/Welcome.php';
include_once 'model/DelMatModel.php';
include_once 'view/InfoMatView.php';
include_once 'model/InfoMatModel.php';
include_once 'model/GetTabModel.php';

class DelMatController
{
    private $view;
    
    public function __construct($bdd){
        $delete = new DelMatModel($bdd);
        $ans = $delete->delete();
        
        switch($ans){
            case 'SUCCESS':
                
                $this->view = new Welcome($bdd);
                $model = new MatStatisticModel($bdd);
                $model->computeForAll();
                $this->view->setMatInfos($model->statePoints, $model->nbMatDisp, $model->nbMatTotal, "!");
                $this->view->setMessage("Matelas supprimé", "pagemessage");
                
                break;
            default:
                $mat = $_SESSION['POST']['ID_MATTRESS'];
                $model = new InfoMatModel($bdd);
                $matTab = $model->getMat($mat);
                $getrdvs = new GetTabModel($bdd);
                $rdvs = $getrdvs->getRdvs($mat);
                
                $this->view = new InfoMatView();
                $this->view->setMat($matTab);
                $this->view->setRdvs($rdvs);
                $this->view->setmessage("Le matelas n'a pas pu être supprimé");
                break;
        }
    }
    
    public function launch(){
        $this->view->launch();
    }
}
?>