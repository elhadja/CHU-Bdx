<?php
$TIMEOUT = 600;

define('SESSION_MAXLIFETIME', $TIMEOUT); // 10 minutes avant déconnexion
session_start();
header('Content-Type: text/html;');
include_once './model/BddConnexion.php';
?>


<?php
$message = "";
$welcomeType = "!";

////Décommentez lors d'un bug :
// session_destroy();

if(isset($_SESSION['POST']['NIP'])){
    $_SESSION['NIP_TO_DISPLAY'] = $_SESSION['POST']['NIP'];
}


if($_SERVER['REQUEST_METHOD'] == 'GET'){ // arrivé sur la page sans methode post
    
    // Connection to PDO
    $co = new BddConnexion();
    $bdd = $co->getBdd();
    

    if(isset($_SESSION['POST'])){ // Si on revient sur la page aprés un POST TO GET on selectionne le controller
        $task = $_SESSION['POST']['TASK'];

        switch($task){

            case 'Deconnexion':
                // echo 'deconexion<br>';
                $class_name = 'WelcomeController';
                // Update the online user's table
                if(isset($_SESSION['USER_ID'])){
                    $req = $bdd->prepare("DELETE FROM users_online WHERE ID_USER = ".$_SESSION['USER_ID'] );
                    $req->execute();
                    session_unset();
                    session_destroy();
                }
                break;

            case 'CheckLogin':
                // echo 'checklogin<br>';
                $class_name = 'CheckLoginController';
                break;

            case 'AdminSignUpNewUser':
                // echo 'newuser<br>';
                $class_name = 'AdminSignUpNewUserController';
                break;

            case 'AddPat':
                // echo 'addpat<br>';
                $class_name = 'AddPatController';
                break;

            case 'DelPat':
                // echo 'delpat<br>';
                $class_name = 'DelPatController';
                break;

            case 'AddMat':
                // echo 'addmat<br>';
                $class_name = 'AddMatController';
                break;

            case 'DelMat':
                // echo 'delmat<br>';
                $class_name = 'DelMatController';
                break;

            case 'Research':
                // echo 'research<br>';
                $class_name = 'ResearchController';
                break;

            case 'MatCrane':
                // echo 'crane<br>';
               $welcomeType = "Crane";
               $class_name = 'WelcomeController';
               break;

            case 'MatAbdomen':
                // echo 'adbomen<br>';
               $welcomeType = "Abdomen";
               $class_name = 'WelcomeController';
               break;

            case 'MatThorax':
                // echo 'thorax<br>';
               $welcomeType = "Thorax";
               $class_name = 'WelcomeController';
               break;

            case 'InfoPatRDV':
                // echo 'infopat<br>';
                $class_name = 'InfoPatRDVController';
                break;

            case 'InfoMat':
                // echo 'infomat<br>';
                $class_name = 'InfoMatController';
                break;

            default:
                // echo 'default<br>';
                $class_name = 'WelcomeController';
                break;
        }


    }

    else{ // si on revient sur la page (refresh ou premiére connexion)
        // echo 'refresh<br>';
        $class_name = 'WelcomeController';
    }

    // déconnexion aprés 10 minutes
    if(isset($_SESSION['logged']) and $_SESSION['logged'] == 1){
        if( (time() - $_SESSION['last_action']) > SESSION_MAXLIFETIME ){
            $class_name = 'WelcomeController';
            // Update the online user's table
            $req = $bdd->prepare("DELETE FROM users_online WHERE ID_USER = ".$_SESSION['USER_ID'] );
            $req->execute();
            session_unset();
            session_destroy();
        }else{
            $_SESSION['last_action'] = time();
        }
    }

}
else{ // arrivé sur la page avec methode post -> POST TO GET
    $_SESSION['POST'] = $_POST;
    header('Location: index.php');
    die;
}


include_once 'controller/'.$class_name.'.php';
$controller = new $class_name($bdd);

// Count online users
$req = $bdd->prepare("SELECT * FROM users_online");
$req->execute();
$res = $req->fetchAll();

// remove old users from online_users
$now = date('Y-m-d h:i:s');
foreach($res as $user){
	// echo 'now : '.$now. ' - rdv : '.date('Y-m-d h:i:s',strtotime($user['TIME'].' + '.$TIMEOUT.' seconds')).'<br>';
    if(date('Y-m-d h:i:s',strtotime($user['TIME'].' + '.$TIMEOUT.' seconds')) < $now){
		// echo 'old connexion deleted<br>';
        $req = $bdd->prepare("DELETE FROM users_online WHERE ID = ?");
        $req->execute(array($user['ID']));
    }
}

// refresh online_users tab with current time
if(isset($_SESSION['USER_ID'])){
    $req = $bdd->prepare("UPDATE users_online SET TIME = ? WHERE ID_USER = ?");
    $req->execute(array($now,$_SESSION['USER_ID']));
}

$_SESSION['ONLINE'] = sizeof($res);

if($class_name == 'WelcomeController'){
    unset($_SESSION['RES']);
    $controller->setType($welcomeType);
    $controller->launch($message);
}
else{
    $controller->launch();
}

unset($_SESSION['POST']);
?>
