<?php

use view\InscriptionModal;

include_once 'InscriptionModal.php';
include_once 'Header.php';


class AddMatView
{
    private $inscriptionmodal;
    private $namemessage;
    private $typemessage;
    private $statemessage;
    
    public function __construct(){
        $this->inscriptionmodal = new InscriptionModal();
    }
    
    public function setMessage($msg, $flag){
        switch($flag){
            case 'name':
                $this->namemessage = $msg;
                break;
            case 'type':
                $this->typemessage = $msg;
                break;
            case 'state':
                $this->statemessage = $msg;
                break;
        }
    }
    
    public function launch(){
        // include the header to the view
        $header = new Header("Ajout d'un matelas");
        $header->launch();
        
        // include inscription modal
        $this->inscriptionmodal->launch();
        
        echo '<div class="container p-3"> <h4>Nouveau matelas</h4>';
        
        echo <<<VIEW
        
        <form action="index.php" method="POST" class="mt-5 p-3">
            <input type="hidden" name="TASK" value="AddMat">
            <div class="form-group">
                <label>Nom du matelas</label>
                <input type="text" name="NAME" class="form-control" required>
                <small class="form-text text-danger">$this->namemessage</small>
            </div>
            <div class="form-group">
                <label>Type</label>
                <select name="TYPE" class="form-control">
                    <option value="-1">--Type--</option>
                    <option value="1">Crane</option>
                    <option value="2">Thorax</option>
                    <option value="3">Abdomen</option>
                </select>
                <small class="form-text text-danger">$this->typemessage</small>
            </div>
            <div class="form-group">
                <label>Etat</label>
                <select name="STATE" class="form-control">
                    <option value="-1">--Etat--</option>
                    <option value="1">Bon</option>
                    <option value="2">A remplacer</option>
                    <option value="3">Ne pas utiliser</option>
                </select>
                <small class="form-text text-danger">$this->statemessage</small>
            </div>
            <div class="form-group">
            <label>Matelas d'urgence</label>
            <select name="URGENT" class="form-control">
                <option value="0">Non</option>
                <option value="1">Oui</option>
            </select>
            </div>
            
            <button class="btn btn-primary" type="submit">Enregistrer le matelas</button>
            </form>
            
VIEW;
        
        echo '</div></body>';
    }
}
?>