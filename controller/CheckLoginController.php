<?php
include_once 'model/CheckLoginModel.php';
include_once 'view/Welcome.php';
include_once 'model/MatStatisticModel.php';

class CheckLoginController{

    private $view;

    function __construct($bdd){
        $model = new CheckLoginModel($bdd);
        $this->view = new Welcome($bdd);

        $resp = $model->checkLogin();
        switch($resp){
            case 'EMPTY_USER':
                $this->view->setMessage("Veuillez entrer votre email pour vous connecter",'emailmessage');
                break;
            case 'EMPTY_PASSWORD':
                $this->view->setMessage("Veuillez entrer votre mot de passe pour vous connecter",'pwmessage');
                break;
            case 'SUCCESS':
                $this->view->setMessage("Vous êtes connecté",'pagemessage');
                break;
            case 'INCORRECT_PASSWORD':
                $this->view->setMessage("Le mot de passe est incorrect",'pwmessage');
                break;
            case 'INCORRECT_USER':
                $this->view->setMessage("Email non trouvé dans la base de données",'emailmessage');
                break;
            case 'ERROR_BDD':
                $this->view->setMessage("-- DATABASE ERROR --",'emailmessage');
                break;
            case 'ALREADY_ONLINE':
                $this->view->setMessage("Utilisateur déjà connecté",'emailmessage');
                break;
        }
        
        $model = new MatStatisticModel($bdd);
        $model->computeForAll();
        $this->view->setMatInfos($model->statePoints, $model->nbMatDisp, $model->nbMatTotal, "!");
    }


    function launch(){
        $this->view->launch();
    }
}
?>
