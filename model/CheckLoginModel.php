<?php

include_once 'BddConnexion.php';

class CheckLoginModel{

    public function checkLogin(){
        $post = $_SESSION['POST'];

        // Establishement of connexion to the database
        $co = new BddConnexion();
        $bdd = $co->getBdd();

        //On rÃ©cupÃ¨re l'USERNAME et le PW que l'utilisateur a tapÃ© dans le formulaire
        $USERNAME = $post['USERNAME'];
        $PW = $post['PW'];


        if (empty($USERNAME)){
            return 'EMPTY_USER';
        }

        if(empty($PW)){
            return 'EMPTY_PASSWORD';
        }

        //Recherche du mot de passe dans la BDD SI la connexion a marchÃ© en PDO
        $req = $bdd->prepare('SELECT ID,PWD,USERNAME FROM users WHERE USERNAME = ?');
        $req->execute(array($USERNAME));
        $res = $req->fetch();

        //On test si le PW de la BDD correspond au PW de l'utilisateur

         if(password_verify($PW,$res['PWD'])){
             // verifier que le compte n'est pas déjà utilisé
             $req = $bdd->prepare("SELECT COUNT(*) AS N FROM users_online WHERE ID_USER=?");
             $req->execute(array($res['ID']));
             $r = $req->fetch();
             if($r['N'] != 0){
                 return 'ALREADY_ONLINE';
             }
             
            if($PW = $res['PWD']){
                $message = 'SUCCESS';
                $_SESSION['logged'] = 1;
                $_SESSION['USERNAME'] = $res['USERNAME'];
                $_SESSION['USER_ID'] = $res['ID'];
                $_SESSION['last_action'] = time();
				
				//Insertion dans la BDD
				$req = $bdd->prepare("INSERT INTO users_online (ID_USER,TIME) VALUES(:USER,:TIME)");
				if($req){
				    $now = date('Y-m-d h:i:s');
					$req->execute(array('USER' => $_SESSION['USER_ID'], 'TIME' => $now));
				}else{
					$req->errorInfo();
					return 'ERROR_BDD';
				}
            }else{
                //Incorrect password
                $message = 'INCORRECT_PASSWORD';
            }
        }else{
            //The username does not exist
            $message = 'INCORRECT_USER';
        }

        return $message;
    }

}
?>
