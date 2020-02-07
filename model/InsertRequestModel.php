<?php

class InsertRequestModel
{
    private $bdd;
    
    public function __construct($bdd){
        // Establishement of connexion to the database
        $this->bdd = $bdd;
    }
    
    public function insertPatient(){
        
        if(!isset($_SESSION['POST']['NIP'])){
            return '!';
        }
        
        $bdd = $this->bdd;
        
        $name = $_SESSION['POST']['NAME'];
        $surname = $_SESSION['POST']['SURNAME'];
        $nip = $_SESSION['POST']['NIP'];
        if($nip > 9999999999){
            return 'BIG_NIP';
        }
        
        $time = $_SESSION['POST']['TIME'];
        if($time <= 0){
            return 'UNVALID_TIME';
        }
        
        $type = $_SESSION['POST']['TYPE'];
        if($type == -1){
            return 'EMPTY_TYPE';
        }
        
        $nbsession = $_SESSION['POST']['NB_SESSION'];
        $note = $_SESSION['POST']['NOTE'];
        $urgent = $_SESSION['POST']['URGENT'];
        
        $req = $bdd->prepare("SELECT * FROM patients WHERE NIP = ?");
        $req->execute(array($nip));
        $res = $req->fetch();
        
        $patid = -1;
        // si un champ n'est pas remplis on r�cup�re le patient de la base de donn�e
        if(empty($name) || empty($surname)){
            // erreur si le patient n'existe pas
            if(!$res['NAME']){
                return 'UNKNOWN_NIP';
            }
            $name = $res['NAME'];
            $surname = $res['SURNAME'];
            $patid = $res['ID'];
        }
        // sinon on v�rifie que le nip est libre
        else{
            if($res['NAME']){
                return 'UNVALID_NIP';
            }
            // et on ajoute le nouveau patient
            $req = $bdd->prepare("INSERT INTO patients(NAME, SURNAME, NIP) VALUES(:NAME, :SURNAME, :NIP)");
            if($req){
                $req->execute(array('NAME'=>$name,'SURNAME'=>$surname,'NIP'=>$nip));
            }
            else{
                $req->errorInfo();
                return 'ERROR_BDD';
            }
            // on recupere l'id du patient cree
            $req = $bdd->prepare("SELECT * FROM patients WHERE NIP = ?");
            $req->execute(array($nip));
            $res = $req->fetch();
            $patid = $res['ID'];
        }
        
        // recuperer la liste des matelas du bon type
        $req = $bdd->prepare("SELECT mattress.* FROM mattress WHERE ID_TYPE = ? AND ID_STATE != 3 AND URGENT = ?");
        $req->execute(array($type,$urgent));
        $ms = $req->fetchAll();
        
        $today = date("Y-m-d");
        $todayplus = date('Y-m-d',strtotime($today.' + 5 days'));
        
        $bestdatescanner = $today;
        $bestdateftr = $today;
        $selectedmattress = -1;
        
        foreach($ms as $m){
            
            $req = $bdd->prepare("SELECT DATE_SCANNER, DATE_FTR FROM rdv
                                INNER JOIN mattress ON mattress.ID = rdv.ID_MATTRESS
                                WHERE rdv.ID_STATE = 1 AND rdv.ID_MATTRESS = ?"); // selectionner tout les rendez-vous normaux de se matelas
            $req->execute(array($m['ID']));
            $rdvs = $req->fetchAll();
            
            // aucun rdv sur le matelas
            if(sizeof($rdvs)==0){
                $bestdatescanner = $todayplus;
                $bestdateftr = date('Y-m-d',strtotime($bestdatescanner.' + '.$time.' days'));
                $selectedmattress = $m['ID'];
                break;
            }
            
            // sort all of the rdv
            $rdvs = $this->bubbleSortRdv($rdvs);
            
            $ind = 0;
            $found = false;
            // cas special ou il n'y a pas de rdv terminé
            if($rdvs[0]['DATE_SCANNER'] >= date('Y-m-d',strtotime($todayplus.' + '.$time.' days'))){
                $bestdatescanner = $todayplus;
                $bestdateftr = date('Y-m-d',strtotime($todayplus.' + '.$time.' days'));
                $selectedmattress = $m['ID'];
                $found = true;
            }
            while($ind<sizeof($rdvs)-1 && !$found){ // tant qu' il y a des rdv qui suivent
                
                // select a date scanner
                if($rdvs[$ind]['DATE_FTR'] < $today){
                    $datescanner = $todayplus;
                }
                else{
                    $datescanner = date('Y-m-d',strtotime(($rdvs[$ind]['DATE_FTR']).' + 5 days'));
                }
                
                // find date ftr, date scanner + time
                $dateftr = date('Y-m-d',strtotime($datescanner.' + '.$time.' days'));
                
                // check with the other rdv
                if(date('Y-m-d',strtotime($dateftr.' + 5 days')) < $rdvs[$ind+1]['DATE_SCANNER']){
                    if($selectedmattress == -1 || $datescanner < $bestdatescanner){
                        $bestdatescanner = $datescanner;
                        $bestdateftr = $dateftr;
                        $selectedmattress = $m['ID'];
                        $found = true;
                        break;
                    }
                }
                $ind++;
            }
            
            if(!$found){
                if($rdvs[$ind]['DATE_FTR'] < $today){
                    $datescanner = $todayplus;
                }
                else{
                    $datescanner = date('Y-m-d',strtotime(($rdvs[$ind]['DATE_FTR']).' + 5 days'));
                }
                
                if($selectedmattress == -1 || $datescanner < $bestdatescanner){
                    $bestdatescanner = $datescanner;
                    $bestdateftr = date('Y-m-d',strtotime($datescanner.' + '.$time.' days'));
                    $selectedmattress = $m['ID'];
                }
            }
        }
        
        if($selectedmattress == -1){
            return 'ERROR_MATTRESS';
        }
        
        //Insertion dans la BDD
        $req = $bdd->prepare("INSERT INTO rdv(DATE_SCANNER, DATE_MEP, DATE_FTR, NB_SESSION, NOTE, ID_MATTRESS, ID_USER, ID_STATE, ID_PATIENT)
                            VALUES(:DATE_SCANNER, :DATE_SCANNER, :DATE_FTR, :NB_SESSION, :NOTE, :ID_MATTRESS, :ID_USER, 1, :ID_PATIENT)");
        if($req){
            
            $req->execute(array('DATE_SCANNER' => $bestdatescanner, 'DATE_FTR' => $bestdateftr, 'NB_SESSION' => $nbsession,
                'NOTE' => $note, 'ID_MATTRESS' => $selectedmattress, 'ID_USER' => $_SESSION['USER_ID'], 'ID_PATIENT' => $patid));
            
            // create array for infoMat
            $req = $bdd->prepare('SELECT
                                patients.NAME AS NAME, patients.SURNAME AS SURNAME, patients.NIP AS NIP, DATE_SCANNER, DATE_MEP,
                                DATE_FTR, NB_SESSION, NOTE, mattress.NAME AS MATTRESS, users.USERNAME AS USER, rdv_state.NAME AS STATE, rdv.ID AS ID_RDV, patients.ID AS ID_PATIENT
                                FROM rdv
                                INNER JOIN mattress ON rdv.ID_MATTRESS = mattress.ID
                                INNER JOIN patients ON rdv.ID_PATIENT = patients.ID
                                INNER JOIN users ON rdv.ID_USER = users.ID
                                INNER JOIN rdv_state ON rdv.ID_STATE = rdv_state.ID
                                WHERE patients.NIP = ? AND rdv.DATE_SCANNER = ?');
            
            $req->execute(array($nip,$bestdatescanner));
            $res = $req->fetch();
            $_SESSION['RES'][0] = $res;
            
            return 'SUCCESS';
        }else{
            $req->errorInfo();
            return 'ERROR_BDD';
        }
        
    }
    
    public function bubbleSortRdv($allrdv){
        for($i=0; $i<sizeof($allrdv)-1; $i++){
            for($j=$i+1; $j<sizeof($allrdv); $j++){
                if($allrdv[$i]['DATE_SCANNER'] > $allrdv[$j]['DATE_SCANNER']){
                    $tmp = $allrdv[$i];
                    $allrdv[$i] = $allrdv[$j];
                    $allrdv[$j] = $tmp;
                }
            }
        }
        return $allrdv;
    }
    
    public function insertMattress(){
        
        if(!isset($_SESSION['POST']['NAME'])){
            return '!';
        }
        
        $name = $_SESSION['POST']['NAME'];
        $type = $_SESSION['POST']['TYPE'];
        $dispo = 1;
        $state = $_SESSION['POST']['STATE'];
        $urgent = $_SESSION['POST']['URGENT'];
        
        if(empty($name)){
            return 'NAME_EMPTY';
        }
        
        if($type == -1){
            return 'TYPE_EMPTY';
        }
        
        if($state == -1){
            return 'STATE_EMPTY';
        }
        
        $bdd = $this->bdd;
        
        //V�rifier si le nom est d�ja dans la base de donn�e
        $req = $bdd->prepare("SELECT NAME FROM mattress WHERE NAME = ?");
        $req->execute(array($name));
        $res = $req->fetch();
        if($res['NAME']){
            return 'INVALID_NAME';
        }
        
        //Insertion dans la BDD
        $req = $bdd->prepare("INSERT INTO mattress(NAME, ID_TYPE, ID_DISPO, ID_STATE, URGENT) VALUES(:NAME, :TYPE, :DISPO, :STATE, :URGENT)");
        if($req){
            $req->execute(array('NAME' => $name, 'TYPE' => $type, 'DISPO' => $dispo, 'STATE' => $state, 'URGENT' => $urgent));
            return 'SUCCESS';
        }else{
            $req->errorInfo();
            return 'ERROR_BDD';
        }
    }
}
?>