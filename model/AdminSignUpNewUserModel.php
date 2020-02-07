<?php

class AdminSignUpNewUserModel{

    private $PW_HASH;
    private $EMAIL_HASH;
    private $bdd;
    
    public function __construct($bdd){
        $this->bdd = $bdd;
    }

    public function SignUpUser(){

        $post = $_SESSION['POST'];

        //On récupère l'USERNAME, l'E-Mail et le PW que l'utilisateur a tapé dans le formulaire
        $USERNAME = $post['USERNAME_USER'];
        $MATRICULE = $post['MATRICULE_USER'];
        $PW = $post['PW_USER'];
        $PW_REPEAT = $post['PW_USER_REPEAT'];

        //Connexion raté car USERNAME ou PW ou MAIL est vide :
        if(empty($USERNAME)){
            return 'EMPTY_USER';
        }

        if(empty($MATRICULE)){
            return 'EMPTY_MATRICULE';
        }

        if(empty($PW)){
            return 'EMPTY_PASSWORD';
        }

        if(empty($PW_REPEAT)){
            return 'EMPTY_PASSWORDREP';
        }

        if ($PW != $PW_REPEAT){
            return 'DIFFERENT_PASSWORDS';
        }

        //Connection to PDO
        $bdd = $this->bdd;

        //Hash
        $options = [
          'cost' => 12,
        ];
        $this->PW_HASH = password_hash($PW, PASSWORD_BCRYPT, $options);

        //Vérification si l'USERNAME n'est pas déjà dans la BDD :
        $req = $bdd->prepare("SELECT USERNAME FROM users WHERE USERNAME = ?");
        $req->execute(array($USERNAME));
        $res = $req->fetch();
        if($res['USERNAME']){
            return 'INVALID_USER';
        }

        //V�rification si le matricule n'est pas déjà dans la BDD :
        $req = $bdd->prepare("SELECT MATRICULE FROM users WHERE MATRICULE = ?");
        $req->execute(array($MATRICULE));
        $res = $req->fetch();
        if($res['MATRICULE']){
            return 'INVALIDE_MATRICULE';
        }


        //Insertion dans la BD
        $req = $bdd->prepare("INSERT INTO users(USERNAME, MATRICULE, PWD)VALUES(:USERNAME, :MATRICULE, :PWD)");
        if($req){
            $req->execute(array('USERNAME' => $USERNAME, 'MATRICULE' => $MATRICULE, 'PWD' => $this->PW_HASH));

            return 'SUCCESS';
        }else{
            $req->errorInfo();
            return 'ERROR_BDD';
        }

    }
}
?>
