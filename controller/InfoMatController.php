<?php
include_once 'view/InfoMatView.php';
include_once 'model/InfoMatModel.php';
include_once 'model/GetTabModel.php';

class InfoMatController{
    private $view;

    function __construct($bdd){
        $mat = $_SESSION['POST']['ID_MATTRESS'];
        $model = new InfoMatModel($bdd);
        $ans = $model->modify($mat);
        
        $matTab = $model->getMat($mat);
        $getrdvs = new GetTabModel($bdd);
        $rdvs = $getrdvs->getRdvs($mat);
        
        $this->view = new InfoMatView();
        $this->view->setMat($matTab);
        $this->view->setRdvs($rdvs);
        
        switch($ans){
            case 'SUCCESS':
                $this->view->setmessage('Modification réussite');
                break;
            case 'INVALID_NAME':
                $this->view->setmessage('Echec : nom déjà utilisé');
                break;
            case 'TYPE_EMPTY':
                $this->view->setmessage('Echec : choisir un type');
                break;
            case 'STATE_EMPTY':
                $this->view->setmessage('Echec : choisir un état');
                break;
            default:
                break;
        }
    }

    function launch(){
        $this->view->launch();
    }
}
?>
