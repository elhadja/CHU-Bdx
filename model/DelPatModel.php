<?php

class DelPatModel
{
    private $bdd;
    
    public function __construct($bdd){
        $this->bdd = $bdd;
    }
    
    public function delete(){
        $bdd = $this->bdd;
        
        $idrdv = -1;
        $idpat = -1;
        if(isset($_SESSION['POST']['ID_RDV'])){
            $ind = $_SESSION['POST']['ID_RDV'];
            $res = $_SESSION['RES'][$ind];
            $idrdv = $res['ID_RDV'];
            $idpat = $res['ID_PATIENT'];
        }
        else{
            return '!';
        }
        
        // suppression du rdv
        $req = $bdd->prepare("DELETE FROM rdv WHERE ID = ?");
        $req->execute(array($idrdv));
        
        // verifier que le patient n'a pas d'autre rdv avant de le supprimer
        if($_SESSION['POST']['DELETEPAT'] == 'YES'){
            $req = $bdd->prepare("SELECT COUNT(*) AS N FROM rdv WHERE ID_PATIENT = ?");
            $req->execute(array($idpat));
            $r = $req->fetch();
            
            if(sizeof($r['N']) == 0){
                // suppression du patient
                $req = $bdd->prepare("DELETE FROM patients WHERE ID = ?");
                $req->execute(array($idpat));
                return 'SUCCESS_PAT';
            }
            else{
                return 'ECHEC_PAT';
            }
        }
        else{
            return 'SUCCESS';
        }
    }
}
?>