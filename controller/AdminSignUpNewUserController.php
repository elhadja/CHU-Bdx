<?php
include_once 'model/AdminSignUpNewUserModel.php';
include_once 'view/Welcome.php';
include_once 'model/MatStatisticModel.php';


class AdminSignUpNewUserController{
    private $view;
    private $model;

    function __construct($bdd){
        $this->model = new AdminSignUpNewUserModel($bdd);
        $this->view = new Welcome($bdd);
        
        $rep = $this->model->SignUpUser();
        switch($rep){
            case 'EMPTY_USER':
                $this->view->setMessage("Veuillez donner une adresse email",'subemailmessage');
                break;
            case 'EMPTY_MATRICULE':
                $this->view->setMessage("Veuillez donner un matricule",'matriculemessage');
                break;
            case 'EMPTY_PASSWORD':
                $this->view->setMessage("Veuillez donner un mot de passe",'subpwmessage');
                break;
            case 'EMPTY_PASSWORDREP':
                $this->view->setMessage("Veuillez répéter le mot de passe",'subcopypwmessage');
                break;
            case 'DIFFERENT_PASSWORDS':
                $this->view->setMessage("Les mots de passes ne correspondent pas",'subcopypwmessage');
                break;
            case 'INVALID_USER':
                $this->view->setMessage("Cette adresse email existe déjà",'subemailmessage');
                break;
            case 'INVALID_MATRICULE':
                $this->view->setMessage("Ce matricule existe déjà",'matriculemessage');
                break;
            case 'ERROR_BDD':
                $this->view->setMessage("-- DATABASE ERROR --",'pagemessage');
                break;
            case 'SUCCESS':
                $this->view->setMessage("Un nouvel utilisateur à été ajouté", 'pagemessage');
        }
    }

    function launch(){
        $model = new MatStatisticModel();
        $model->computeForAll();
        $this->view->setMatInfos($model->statePoints, $model->nbMatDisp, $model->nbMatTotal, "!");
        $this->view->launch();
    }
}
?>
