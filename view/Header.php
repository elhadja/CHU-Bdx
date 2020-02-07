<?php

class Header
{
    private $title;
    private $statePoints;

    public function __construct($s){
        $this->title = $s;
        $this->statePoints = 0;
    }

    public function setStatePoints($points){
        $this->statePoints = $points;
    }

    public function launch(){
        echo <<< VIEW
<!DOCTYPE HTML>
<html>

<head>
    <title>$this->title</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css">

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <link rel="stylesheet" type="text/css" href="css/projet.css">

VIEW;
        if($this->statePoints != 0){
            $nbBon = $this->statePoints['BON'];
            $nbAR = $this->statePoints['AR'];
            $nbNPU = $this->statePoints['NPU'];
            echo '
            <script>
            window.onload = function() {

            var stateChart = new CanvasJS.Chart("stateChart", {
            	theme: "light2",
            	animationEnabled: true,
                animationDuration: 600,
            	title: {
            		text: "Etat des matelas",
                    fontSize: 18
            	},
            	data: [{
            		type: "pie",
            		startAngle: 25,
            		legendText: "{label}",
            		indexLabelFontSize: 16,
            		indexLabel: "{label} - {y}%",
            		dataPoints: [
						';if($nbBon!=0) echo'{ y: '.$nbBon.', label: "Bon" },';
						if($nbAR!=0) echo '{ y: '.$nbAR.', label: "A remplacer" },';
						if($nbNPU!=0) echo '{ y: '.$nbNPU.', label: "Ne pas utiliser" }';
						echo'
            		]
            	}]
            });
            stateChart.render();
            }
            </script>
';
        }
        echo <<<VIEW
</head>
<body>

    <nav class="navbar navbar-dark bg-primary sticky-top navbar-expand-xl p-0">
        <div class="container-fluid p-0">

            <a href="index.php" class="navbar-brand p-2 pl-4 mb-0 mr-2 h1 bg-dark">CHU Gestion</a>

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#content" aria-controls="content" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="content">

VIEW;

        // if connected
        if(isset($_SESSION['logged'])){
            echo <<<VIEW

            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <form class="form-inline m-0 mr-4" method="POST" action="index.php">
                        <input type="hidden" name="TASK" value="Research">
                        <input type="search" name="NAME" class="form-control mr-1" placeholder="Nom, Prénom, NIP" aria-label="Search">
                        <button class="btn btn-info " type="submit">
                        <i class="fas fa-search" style="font-size: 22px"></i>
                        </button>
                    </form>
                </li>
VIEW;
            $username = $_SESSION['USERNAME'];
            if($username == 'Admin'){
                echo <<<VIEW

                    <li class="btn-group" role="group" aria-label="Actions">
                        <button type="button" class="btn btn-outline-light" onclick="$('#Main_Form_TASK').val('AddPat'); $('#Main_Form').submit();">Ajouter un traitement</button>
                        <button type="button" class="btn btn-outline-light" onclick="$('#Main_Form_TASK').val('AddMat'); $('#Main_Form').submit();">Ajouter un matelas</button>
                    </li>
                </ul>
                <ul class="navbar-nav mr-3 ml-auto">
                    <li><a class="nav-link ml-1 mr-1" data-target="#inscriptionModal" href="#inscriptionModal" data-toggle="modal">Inscrire un utilisateur</a></li>
VIEW;
            }
            else{
                echo <<<VIEW
                    <button type="button" class="btn btn-outline-light" onclick="$('#Main_Form_TASK').val('AddPat'); $('#Main_Form').submit();">Ajouter un traitement</button>
                </ul>
                <ul class="navbar-nav mr-3 ml-auto">
VIEW;
            }
            echo <<< VIEW

            <li class="nav-item">
                <div class="row ml-1 mr-1">
                    <p class="navbar-text text-white mb-0">$username (</p>
                    <a class="nav-link pl-0 pr-0" href="#" onclick="$('#Main_Form_TASK').val('Deconnexion'); $('#Main_Form').submit();">Déconnexion</a>
                    <p class="navbar-text text-white mb-0">)</p>
                </div>
            </li>

            <form id="Main_Form" action="index.php" method="POST">
                <input id="Main_Form_TASK" type="hidden" name="TASK" value="">
                <input id="Rdv_Id" type="hidden" name="ID_RDV" value="">
                <input id="Mattress_Id" type="hidden" name="ID_MATTRESS" value="">
            </form>
VIEW;
        }
        else{
            echo '<ul class="navbar-nav mr-3 ml-auto"><li><a class="nav-link ml-1 mr-1" data-target="#loginModal" href="#loginModal" data-toggle="modal">Connexion</a></li>';
        }

        echo "</ul></div></div></nav>";

    }
}
?>
