<?php
include_once 'view/InfoPatRDVView.php';
include_once 'model/InfoPatRDVModel.php';


class InfoPatRDVController{
    private $view;
    private $model;
    
    function __construct($bdd){
        $this->view = new InfoPatRDVView($bdd);
        $this->model = new InfoPatRDVModel($bdd);
        
        $id_rdv = 0;
        if(isset($_SESSION['POST']['ID_RDV'])){
            $id_rdv = $_SESSION['POST']['ID_RDV'];
        }
        
        $this->view->setType($this->model->getType($_SESSION['RES'][$id_rdv]['MATTRESS']));
        
        $answer = $this->model->modify();
        switch($answer){
            case 'SUCCESS':
                $this->view->setmessage('Modification réussi');
                break;
            case 'INVALID_MATTRESS':
                $this->view->setmessage('Echec : Aucun matelas compatible de ce nom');
                break;
            case 'UNDISP_MATTRESS':
                $this->view->setmessage('Echec : Matelas occupé');
                break;
            case 'EMPTY_STATE':
                $this->view->setmessage('Echec : Aucun état sélectionné');
                break;
            case 'BIG_NIP':
                $this->view->setmessage('Echec : NIP non conforme');
                break;
            case 'UNVALID_SCN':
                $this->view->setmessage('Echec : Impossible de changer la date de scanner à la date donnée');
                break;
            case 'UNVALID_MEP':
                $this->view->setmessage('Echec : La date MEP doit être entre la date de scanner et la date FTR');
                break;
            case 'UNVALID_FTR':
                $this->view->setmessage('Echec : Impossible de changer la date FTR à la date donnée');
                break;
            default: // DO NOTHING
                break;
        }
    }
    
    function launch(){
        $this->view->launch();
    }
}
?>
