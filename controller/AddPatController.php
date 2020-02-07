<?php
include_once 'view/AddPatView.php';
include_once 'view/InfoPatRDVView.php';
include_once 'model/InfoPatRDVModel.php';
include_once 'model/InsertRequestModel.php';


class AddPatController {

	private $view;

	function __construct($bdd) {
	    $this->view = new AddPatView();
	    
	    $model = new InsertRequestModel($bdd);
	    $resp = $model->insertPatient();
	    
		switch ( $resp ) {
			case 'UNVALID_TIME':
				$this->view->setMessage( "La durée est en jour, un nombre strictement positif", 'time' );
				break;
			case 'EMPTY_TYPE':
				$this->view->setMessage( "Veuillez renseigner le type de matelas", 'type' );
				break;
			case 'UNKNOWN_NIP':
				$this->view->setMessage( "Le patient avec ce NIP n'a pas été trouvé dans la base de données", 'nip' );
				$this->view->setMessage( "Veuillez remplir nom et prénom pour ajouter un patient à la base de donnees", 'page' );
				break;
			case 'UNVALID_NIP':
			    $this->view->setMessage( "Un patient avec ce NIP existe déjà", 'nip' );
			    $this->view->setMessage( "Veuillez ne pas remplir nom et prénom pour utiliser un patient déjà enregistré", 'page' );
				break;
			case 'BIG_NIP':
			    $this->view->setMessage( "Le NIP est composé de 10 chiffres", 'nip' );
			    break;
			case 'ERROR_MATTRESS':
			    $this->view->setMessage( "-- NO MATTRESS ERROR --", 'page' );
			    break;
			case 'ERROR_BDD':
				$this->view->setMessage( "-- DATABASE ERROR --", 'page' );
				break;
			case 'SUCCESS':
			    $this->view = new InfoPatRDVView($bdd);
			    $model = new InfoPatRDVModel($bdd);
			    $id_rdv = 0;
			    if(isset($_SESSION['POST']['ID_RDV'])){
			        $id_rdv = $_SESSION['POST']['ID_RDV'];
			    }
			    $this->view->setType($model->getType($_SESSION['RES'][$id_rdv]['MATTRESS']));
			    break;
		    default: // DO NOTHING ( en attente du remplissage du formulaire )
			    break;
		}
	}

	function launch() {
		$this->view->launch();
	}
}
?>
