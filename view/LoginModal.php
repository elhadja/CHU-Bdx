<?php
namespace view;

class LoginModal
{
    private $emailmessage;
    private $pwmessage;
    
    public function setEmailmessage($msg){
        $this->emailmessage = $msg;
    }
    
    public function setPwmessage($msg){
        $this->pwmessage = $msg;
    }
    
    public function launch(){
       
        echo <<<VIEW
<!-- Modal login-->
<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog" role="document">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title">Connexion</h5>
<button type="button" class="close" data-dismiss="modal" aria-label="Close">
<span aria-hidden="true">&times;</span>
</button>
</div>
<div class="modal-body">
<form action="index.php" method="POST">
<input type="hidden" name="TASK" value="CheckLogin">
<div class="form-group">
<label for="exampleInputUsername1">Nom</label>
<input type="text" name="USERNAME" class="form-control" id="exampleInputUsername1">
<small class="form-text text-danger">$this->emailmessage</small>
</div>
<div class="form-group">
<label for="exampleInputPassword1">Mot de passe</label>
<input type="password" name="PW" class="form-control" id="exampleInputPassword1">
<small class="form-text text-danger">$this->pwmessage</small>
</div>
<input type="submit" value="Se connecter">
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