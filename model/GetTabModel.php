<?php

class GetTabModel
{
    private $bdd;
    
    public function __construct($bdd){
        // Establishement of connexion to the database
        $this->bdd = $bdd;
    }
    
    public function getRdvs($mat){
        $bdd = $this->bdd;
        
        $req = $bdd->prepare('SELECT
                            patients.NAME AS NAME, patients.SURNAME AS SURNAME, patients.NIP AS NIP, DATE_SCANNER, DATE_MEP,
                            DATE_FTR, NB_SESSION, NOTE, mattress.NAME AS MATTRESS, users.USERNAME AS USER, rdv_state.NAME AS STATE, rdv.ID AS ID_RDV, patients.ID AS ID_PATIENT
                            FROM rdv
                            INNER JOIN mattress ON rdv.ID_MATTRESS = mattress.ID
                            INNER JOIN patients ON rdv.ID_PATIENT = patients.ID
                            INNER JOIN users ON rdv.ID_USER = users.ID
                            INNER JOIN rdv_state ON rdv.ID_STATE = rdv_state.ID
                            WHERE mattress.ID = ?');
        $req->execute(array($mat));
        $res = $req->fetchAll();
        $res = $this->bubbleSortRdv($res, '>');
        
        return $res;
    }
    
    public function getResearchTab(){
        $name = $_SESSION['POST']['NAME'];
        
        $bdd = $this->bdd;
        
        // r�cup�rer nom et prenom
        $names = explode(' ', $name, 2);
        $name1 = $names[0];
        $name2 = $name1;
        if(sizeof($names) > 1){
            $name2 = $names[1];
        }
        
        
        $req = $bdd->prepare('SELECT
                            patients.NAME AS NAME, patients.SURNAME AS SURNAME, patients.NIP AS NIP, DATE_SCANNER, DATE_MEP,
                            DATE_FTR, NB_SESSION, NOTE, mattress.NAME AS MATTRESS, users.USERNAME AS USER, rdv_state.NAME AS STATE, rdv.ID AS ID_RDV, patients.ID AS ID_PATIENT
                            FROM rdv
                            INNER JOIN mattress ON rdv.ID_MATTRESS = mattress.ID
                            INNER JOIN patients ON rdv.ID_PATIENT = patients.ID
                            INNER JOIN users ON rdv.ID_USER = users.ID
                            INNER JOIN rdv_state ON rdv.ID_STATE = rdv_state.ID
                            WHERE patients.NAME IN (?,?) OR patients.SURNAME IN (?,?) OR patients.NIP = ?');
        
        $req->execute(array($name1,$name2,$name1,$name2,$name1));
        $res = $req->fetchAll();
        $res = $this->bubbleSortRdv($res,'>');
        
        $_SESSION['RES'] = $res;
        
        return $res;
    }
    
    public function getTab($tab){
        $bdd = $this->bdd;
        
        $prop = 'SELECT * FROM '.$tab;
        $req = $bdd->prepare($prop);
        $req->execute();
        $res = $req->fetchAll();
        return $res;
    }
    
    public function bubbleSortRdv($allrdv,$tag){
        for($i=0; $i<sizeof($allrdv)-1; $i++){
            for($j=$i+1; $j<sizeof($allrdv); $j++){
                if($allrdv[$i]['DATE_SCANNER'] > $allrdv[$j]['DATE_SCANNER'] && $tag == '<'){
                    $tmp = $allrdv[$i];
                    $allrdv[$i] = $allrdv[$j];
                    $allrdv[$j] = $tmp;
                }
                elseif($allrdv[$i]['DATE_SCANNER'] < $allrdv[$j]['DATE_SCANNER'] && $tag == '>'){
                    $tmp = $allrdv[$i];
                    $allrdv[$i] = $allrdv[$j];
                    $allrdv[$j] = $tmp;
                }
            }
        }
        return $allrdv;
    }
    
    public function getAllMattress($type){
        $bdd = $this->bdd;
        
        if($type == '!'){
            $req = $bdd->prepare("SELECT
                            mattress.NAME AS NAME, mattress.ID_TYPE, type.NAME AS TYPE, mattress_state.NAME AS STATE, mattress.ID AS ID, mattress.URGENT AS URGENT
                            FROM mattress
                            INNER JOIN type ON mattress.ID_TYPE = type.ID
                            INNER JOIN mattress_state ON mattress.ID_STATE = mattress_state.ID");
            $req->execute();
        }
        else{
            $req = $bdd->prepare("SELECT mattress.NAME AS NAME, type.NAME AS TYPE, mattress_state.NAME AS STATE, mattress.ID AS ID, mattress.URGENT AS URGENT
                            FROM mattress
                            INNER JOIN mattress_state ON mattress.ID_STATE = mattress_state.ID
                            INNER JOIN type ON mattress.ID_TYPE = type.ID
                            WHERE type.NAME = ?");
            $req->execute(Array($type));
        }
        $ms = $req->fetchAll();
        
        for($i=0; $i<sizeof($ms); $i++){
            if($ms[$i]['STATE'] == 'Ne pas utiliser'){ // si le matelas est HS il n'est pas disponible
                $ms[$i]['DISPO'] = 0;
                $ms[$i]['DATE_DISPO'] = 'HS';
            }
            else{
                $req = $bdd->prepare("SELECT DATE_FTR, DATE_SCANNER FROM rdv
                                    INNER JOIN mattress ON mattress.ID = rdv.ID_MATTRESS
                                    WHERE rdv.ID_STATE = 1 AND ID_MATTRESS = ?"); // selectionner tout les rendez-vous normaux de ce matelas
                $req->execute(array($ms[$i]['ID']));
                $rdvs = $req->fetchAll();
                
                $today = date("Y-m-d");
                $gap = 20; // jours libre entre rdv
                $size = sizeof($rdvs);
                $ind = 0;
                
                $ms[$i]['DISPO'] = 1;
                $ms[$i]['DATE_DISPO'] = 'Libre';
                
                $rdvs = $this->bubbleSortRdv($rdvs,'<');
                
                while($ind < $size){
                    if($ms[$i]['DISPO'] == 0 || // si le rendezvous est en cour ou si un autre est en cour
                        ($rdvs[$ind]['DATE_FTR'] > $today && $rdvs[$ind]['DATE_SCANNER'] < date('Y-m-d',strtotime($today.' + '.$gap.' days')))){
                            $ms[$i]['DISPO'] = 0;
                            if($ind < $size-1){ // si on a un autre rdv aprés
                                // si on a un espace libre de taille $gap entre les 2 rdv
                                if(date('Y-m-d',strtotime(($rdvs[$ind]['DATE_FTR']).' + '.$gap.' days')) < $rdvs[$ind+1]['DATE_SCANNER']){
                                    $ms[$i]['DATE_DISPO'] = date('Y-m-d',strtotime(($rdvs[$ind]['DATE_FTR']).' + 5 days'));
                                    break;
                                }
                            }
                            $ms[$i]['DATE_DISPO'] = date('Y-m-d',strtotime(($rdvs[$ind]['DATE_FTR']).' + 5 days'));
                    }
                    elseif($rdvs[$ind]['DATE_FTR'] < $today){
                        if($ind < $size-1){
                            if(date('Y-m-d',strtotime($today.' + '.$gap.' days')) <= $rdvs[$ind+1]['DATE_SCANNER']){
                                $ms[$i]['DATE_DISPO'] = date('Y-m-d',strtotime(($rdvs[$ind+1]['DATE_SCANNER']).' - 5 days'));
                                break;
                            }
                        }
                    }
                    // cas special aucun rdv terminés
                    elseif($ind == 0 && date('Y-m-d',strtotime($today.' + '.$gap.' days')) <= $rdvs[$ind]['DATE_SCANNER']){
                        $ms[$i]['DATE_DISPO'] = date('Y-m-d',strtotime(($rdvs[$ind]['DATE_SCANNER']).' - 5 days'));
                        break;
                    }
                    $ind++;
                }
            }
        }
        return $ms;
    }
    
}
?>