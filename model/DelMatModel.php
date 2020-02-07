<?php

class DelMatModel
{
    private $bdd;
    
    public function __construct($bdd){
        $this->bdd = $bdd;
    }
    
    public function delete(){
        $bdd = $this->bdd;
        $id = $_SESSION['POST']['ID_MATTRESS'];
        
        // verifier qu'il n'y a pas de rdv pour se matela
        $req = $bdd->prepare("SELECT COUNT(*) AS N FROM rdv WHERE ID_MATTRESS = ?");
        $req->execute(array($id));
        $r = $req->fetch();
        
        if($r['N'] == 0){
            // suppression du patient
            $req = $bdd->prepare("DELETE FROM mattress WHERE ID = ?");
            $req->execute(array($id));
            return 'SUCCESS';
        }
        else{
            return 'FAILURE';
        }
    }
}
?>