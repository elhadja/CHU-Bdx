<?php
namespace view;

class InscriptionModal
{
    private $emailmessage;
    private $matriculemessage;
    private $pwmessage;
    private $copypwmessage;
    
    public function setEmailmessage($msg){
        $this->emailmessage = $msg;
    }
    
    public function setPwmessage($msg){
        $this->pwmessage = $msg;
    }
    
    public function setMatriculemessage($msg){
        $this->matriculemessage = $msg;
    }
    
    public function setCopypwmessage($msg){
        $this->copypwmessage = $msg;
    }
    
    public function launch(){
        
        echo <<<VIEW
<!-- Modal inscription-->
<div class="modal fade" id="inscriptionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog" role="document">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title">Enregistrer un nouvel utilisateur</h5>
<button type="button" class="close" data-dismiss="modal" aria-label="Close">
<span aria-hidden="true">&times;</span>
</button>
</div>
<div class="modal-body">
<form action="index.php" method="POST">
<input type="hidden" name="TASK" value="AdminSignUpNewUser">
<div class="form-group">
<label>Nom d'utilisateur</label>
<input type="text" name="USERNAME_USER" class="form-control">
<small class="form-text text-danger">$this->emailmessage</small>
</div>
<div class="form-group">
<label>Matricule</label>
<input type="text" name="MATRICULE_USER" class="form-control">
<small class="form-text text-danger">$this->matriculemessage</small>
</div>
<div class="form-group">
<label>Mot de passe</label>
<input type="password" name="PW_USER" class="form-control">
<small class="form-text text-danger">$this->pwmessage</small>
</div>
<div class="form-group">
<label>Vérifier le mot de passe</label>
<input type="password" name="PW_USER_REPEAT" class="form-control"
<small class="form-text text-danger">$this->copypwmessage</small>
</div>
<input type="submit" value="Enregistrer l'utilisateur">
</form>
</div>
<div class="modal-footer">
<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>
</div>
</div>
</div>
VIEW;
    }

}
?>