<?php

use view\LoginModal;
use view\InscriptionModal;

include_once 'controller/MattressTabController.php';
include_once 'LoginModal.php';
include_once 'InscriptionModal.php';
include_once 'Header.php';

class Welcome{
    
    // messages, default : empty array
    private $pagemessage = "";

    private $loginmodal;
    private $inscriptionmodal;
    private $mattresstab;

    private $statePoints;
    private $nbMatDisp;
    private $nbMatTotal;

    private $type;
    private $co;

    // if true inscription modal pops up
    private $inscriptionModalPopUp = false;

    public function __construct($bdd){
        $this->loginmodal = new LoginModal();
        $this->inscriptionmodal = new InscriptionModal();
        $this->mattresstab = new MattressTabController($bdd);
        $this->statePoints = 0;
        $this->nbMatDisp = 0;
        $this->nbMatTotal = 0;
        $this->type = "!";
        // Establishement of connexion to the database
        $this->co = new BddConnexion();
    }

    public function setMessage($msg, $flag){
        switch($flag){
            case 'pagemessage':
                $this->inscriptionModalPopUp = false;
                $this->pagemessage = $msg;
                break;
            case 'emailmessage':
                $this->loginmodal->setEmailmessage($msg);
                break;
            case 'pwmessage':
                $this->loginmodal->setPwmessage($msg);
                break;
            case 'subemailmessage':
                $this->inscriptionModalPopUp = true;
                $this->inscriptionmodal->setEmailmessage($msg);
                break;
            case 'matriculemessage':
                $this->inscriptionModalPopUp = true;
                $this->inscriptionmodal->setMatriculemessage($msg);
                break;
            case 'subpwmessage':
                $this->inscriptionModalPopUp = true;
                $this->inscriptionmodal->setPwmessage($msg);
                break;
            case 'subcopypwmessage':
                $this->inscriptionModalPopUp = true;
                $this->inscriptionmodal->setCopypwmessage($msg);
                break;
        }
    }

    public function setMatInfos($statePoints, $nbMatDisp, $nbMatTotal, $type){
        $this->type = $type;
        $this->statePoints = $statePoints;
        $this->nbMatDisp = $nbMatDisp;
        $this->nbMatTotal = $nbMatTotal;
    }

    public function launch(){

		// include the header to the view
		$header = new Header("CHU Gestion");
		$header->setStatePoints($this->statePoints);
		$header->launch();
			
        $bdd = $this->co->getBdd();

		// include login modal
		$this->loginmodal->launch();

		// include inscription modal
		$this->inscriptionmodal->launch();

		// List everybody connected if many people are connected
		if ($_SESSION['ONLINE']>1 && isset($_SESSION['USERNAME'])) {
		    $req = $bdd->prepare('SELECT USERNAME, users_online.ID_USER AS ID_USER FROM users INNER JOIN users_online ON users_online.ID_USER = users.ID');
		    $req->execute(array('USERNAME'));
		    $res = $req->fetchAll();
		    
		    echo '<div class="container-fluid text-center bg-danger text-light mt-2 mb-2 p-1">
                    <h5>Attention, d\'autre(s) utilisateur(s) sont connecté(s) : ( ';
		    $c = 0;
		    for($i=0; $i<sizeof($res); $i++) {
		        //on affiche les données
		        $name = $res[$i]['USERNAME'];
		        if($_SESSION['USER_ID'] != $res[$i]['ID_USER']){
    	            if($c!=0) echo " - "; echo "$name";
                    $c++;
		        }
		    }
		    echo(" )</h5></div>");
		}

		echo <<<VIEW
		<div class="container text-center">
		<h6>$this->pagemessage</h6>
VIEW;

		if(!isset($_SESSION['logged'])){
			echo "<h5> Veuillez vous connecter </h5>
				  <script> $('#loginModal').modal('show') </script>";
		}
		else{
			echo <<<VIEW
			<div class="btn-group" role="group" aria-label="Basic example">
				<button type="button" class="btn btn-dark" onclick="$('#Main_Form_TASK').val('Welcome'); $('#Main_Form').submit();">Tous</button>
				<button type="button" class="btn btn-dark" onclick="$('#Main_Form_TASK').val('MatCrane'); $('#Main_Form').submit();">Crane</button>
				<button type="button" class="btn btn-dark" onclick="$('#Main_Form_TASK').val('MatThorax'); $('#Main_Form').submit();">Thorax</button>
				<button type="button" class="btn btn-dark" onclick="$('#Main_Form_TASK').val('MatAbdomen'); $('#Main_Form').submit();">Abdomen</button>
			</div>
			<h5 class="text-left m-2"><strong>Matelas disponibles : $this->nbMatDisp / $this->nbMatTotal</strong></h5>

			<div class="container">
				<div class="row">
					<div class="col">
						<div id="stateChart" style="height: 370px; width: 100%;"></div>
						<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
					</div>
				</div>
			</div>

			<h5 class="text-left ">Liste des matelas :</h5>
VIEW;
			$this->mattresstab->launch($this->type);
		}

		if($this->inscriptionModalPopUp){
			echo"<script> $('#inscriptionModal').modal('show') </script>";
		}

		echo'</div></body></html>';
	}
}
?>
