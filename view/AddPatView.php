<?php

use view\ InscriptionModal;

include_once 'InscriptionModal.php';
include_once 'Header.php';


class AddPatView {
    private $loginmodal;
    private $inscriptionmodal;
    
    private $pagemessage;
    private $nipmessage;
    private $timemessage;
    private $typemessage;
    
    public function __construct() {
        $this->inscriptionmodal = new InscriptionModal();
    }
    
    public function setMessage($msg,$flag){
        switch($flag){
            case 'page':
                $this->pagemessage = $msg;
                break;
            case 'nip':
                $this->nipmessage = $msg;
                break;
            case 'time':
                $this->timemessage = $msg;
                break;
            case 'type':
                $this->typemessage = $msg;
                break;
                
        }
    }
    
    public function launch() {
        // include the header to the view
        $header = new Header( "Ajout d'un traitement" );
        $header->launch();
        
        // include inscription modal
        $this->inscriptionmodal->launch();
        
        echo <<<VIEW
			<div class="container p-3"> <h6 class="align-center">$this->pagemessage</h6> <h4>Nouveau traitement</h4>
				<form action="index.php" method="POST" class="mt-5 p-3">
					  <input type="hidden" name="TASK" value="AddPat">
					  
					  <div class="form-group">
						  <label>Nom</label>
						  <input type="text" name="NAME" class="form-control">
					  </div>
					  
					  <div class="form-group">
						  <label>Prénom</label>
						  <input type="text" name="SURNAME" class="form-control">
					  </div>
					  
					  <div class="form-group">
						  <label>NIP</label>
						  <input type="text" name="NIP" class="form-control" required>
                          <small class="form-text text-danger">$this->nipmessage</small>
					  </div>
					  
                      <div class="form-group">
						  <label>Durée du traitement</label>
						  <input type="number" name="TIME" class="form-control" placeholder="jours" required>
                          <small class="form-text text-danger">$this->timemessage</small>
					  </div>
					  
					  <div class="form-group">
						  <label for="TYPE">Type de matelas nécessaire au traitement</label>
						  <select name="TYPE"  class="form-control">
							  <option value="-1">--Type--</option>
							  <option value="1">Crane</option>
							  <option value="2">Thorax</option>
							  <option value="3">Abdomen</option>
						  </select>
                          <small class="form-text text-danger">$this->typemessage</small>
					  </div>
					  
					  <div class="form-group">
						  <label>Nombre de séances</label>
						  <input type="text" name="NB_SESSION" class="form-control" required>
					  </div>
					  
					  <div class="form-group">
						  <label>Notes</label>
						  <input type="text" name="NOTE" class="form-control">
					  </div>
					  
                      <div class="form-group">
						  <label for="URGENT">Utiliser un matelas d'urgence</label>
						  <select name="URGENT"  class="form-control">
							  <option value="0">Non</option>
							  <option value="1">Oui</option>
						  </select>
					  </div>
					  
					  <button class="btn btn-primary" type="submit">Enregistrer le traitement</button>
				</form>
        	</div>
		</body>;
VIEW;
    }
}
?>