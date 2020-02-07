<?php

define("HOST", "localhost"); // The host to connect to
define("USER", "root"); // The database username
define("PASSWORD", ""); // The database password // The database password
define("DATABASE", "tmandallena"); // The database name

class BddConnexion
{
    private $bdd;

    // Establishement of connexion to the database
    public function __construct(){
        try {
            $dsn = "mysql:host=".HOST.";dbname=".DATABASE;
            $this->bdd = new PDO($dsn, USER, PASSWORD);
            $this->bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $req = $this->bdd->prepare('SET NAMES "utf8"');
            $req->execute();

            // uncomment to add 1 admin session
//             //Hash
//             $options = [
//                 'cost' => 12,
//             ];
//             $hash = password_hash("admin", PASSWORD_BCRYPT, $options);

//             $req = $this->bdd->prepare('INSERT INTO users (USERNAME, PWD) VALUES ("admin", :PWD)');
//             $req->execute(array('PWD' => $hash));


        } catch (PDOException $e) {
            echo "La connexion à la base de données a echoué".$e->getMessage();
            exit();
        }
    }

    public function getBdd(){
        return $this->bdd;
    }
}
?>