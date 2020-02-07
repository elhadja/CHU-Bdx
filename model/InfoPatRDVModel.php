<?php

class InfoPatRDVModel{
    
    private $bdd;
    
    public function __construct($bdd){
        $this->bdd = $bdd;
    }
    
    public function getType($mat){
        $bdd = $this->bdd;
        $req = $bdd->prepare("SELECT ID_TYPE FROM mattress WHERE NAME=?");
        $req->execute(Array($mat));
        $r = $req->fetch();
        return $r['ID_TYPE'];
    }
    
    public function bubbleSortRdv($allrdv){
        for($i=0; $i<sizeof($allrdv)-1; $i++){
            for($j=$i+1; $j<sizeof($allrdv); $j++){
                if($allrdv[$i]['SCN'] > $allrdv[$j]['SCN']){
                    $tmp = $allrdv[$i];
                    $allrdv[$i] = $allrdv[$j];
                    $allrdv[$j] = $tmp;
                }
            }
        }
        return $allrdv;
    }
    
    public function computeOp($ms){
        
        While(true){
            $breakfor = false;
            for($i = 0; $i < sizeof($ms)-1; $i++){
                for($j = $i+1; $j < sizeof($ms); $j++){
                    
                    //DEBUG
                    echo'<h3>BEFORE</h3>';
                    echo $i.' - ID : '.$ms[$i]['ID'].' - START : '.$ms[$i]['START'].'<br>';
                    if($ms[$i]['RDV'] != null){
                        for($x=0; $x<sizeof($ms[$i]['RDV']); $x++){
                            echo $i.' - RDV - '.$x.' - ID : '.$ms[$i]['RDV'][$x]['ID'].' - SCN : '.$ms[$i]['RDV'][$x]['SCN'].' - FTR : '.$ms[$i]['RDV'][$x]['FTR'].'<br>';
                        }
                        echo '<br>';
                    }
                    echo $j.' - ID : '.$ms[$j]['ID'].' - START : '.$ms[$j]['START'].'<br>';
                    if($ms[$j]['RDV'] != null){
                        for($x=0; $x<sizeof($ms[$j]['RDV']); $x++){
                            echo $j.' - RDV - '.$x.' - ID : '.$ms[$j]['RDV'][$x]['ID'].' - SCN : '.$ms[$j]['RDV'][$x]['SCN'].' - FTR : '.$ms[$j]['RDV'][$x]['FTR'].'<br>';
                        }
                        echo '<br>';
                    }
                    
                    $switch = $this->matSwitch($ms[$i],$ms[$j]);
                    if($switch == null){
                        echo 'next try<br>';
                        $switch = $this->matSwitch($ms[$j], $ms[$i]);
                    }
                    
                    //DEBUG
                    echo'<h3>AFTER</h3>';
                    echo '0 - ID : '.$switch[0]['ID'].' - START : '.$switch[0]['START'].'<br>';
                    if($switch[0]['RDV'] != null){
                        for($x=0; $x<sizeof($switch[0]['RDV']); $x++){
                            echo $i.' - RDV - '.$x.' - ID : '.$switch[0]['RDV'][$x]['ID'].' - SCN : '.$switch[0]['RDV'][$x]['SCN'].' - FTR : '.$switch[0]['RDV'][$x]['FTR'].'<br>';
                        }
                        echo '<br>';
                    }
                    echo '1 - ID : '.$switch[1]['ID'].' - START : '.$switch[1]['START'].'<br>';
                    if($switch[1]['RDV'] != null){
                        for($x=0; $x<sizeof($switch[1]['RDV']); $x++){
                            echo $j.' - RDV - '.$x.' - ID : '.$switch[1]['RDV'][$x]['ID'].' - SCN : '.$switch[1]['RDV'][$x]['SCN'].' - FTR : '.$switch[1]['RDV'][$x]['FTR'].'<br>';
                        }
                        echo '<br>';
                    }
                    
                    if($switch != null){
                        $ms[$i] = $switch[0];
                        $ms[$j] = $switch[1];
                        $breakfor = true;
                        break;
                    }
                }
                if($breakfor){
                    break;
                }
            }
            if(!$breakfor){
                break;
            }
        }
        
        // DEBUG
        echo '<h3> CHANGED ! </h3>';
        for($i=0; $i<sizeof($ms); $i++){
            echo $i.' - ID : '.$ms[$i]['ID'].' - START : '.$ms[$i]['START'].'<br>';
            if($ms[$i]['RDV'] != null){
                for($j=0; $j<sizeof($ms[$i]['RDV']); $j++){
                    echo $i.' - RDV - '.$j.' - ID : '.$ms[$i]['RDV'][$j]['ID'].' - SCN : '.$ms[$i]['RDV'][$j]['SCN'].' - FTR : '.$ms[$i]['RDV'][$j]['FTR'].'<br>';
                }
            }
            echo '<br>';
        }
        
        return $ms;
    }
    
    public function getUpperRdv($id_rdv){
        // find the mattress id of the rdv
        $req = $this->bdd->prepare("SELECT DATE_FTR AS FTR, DATE_SCANNER AS SCN, rdv.ID AS ID, ID_MATTRESS FROM rdv
                                    INNER JOIN mattress ON mattress.ID = rdv.ID_MATTRESS
                                    WHERE rdv.ID_STATE = 1 AND mattress.NAME = ?"); // selectionner tout les rendez-vous type = normal de ce matelas
        $req->execute(array($_SESSION['RES'][$id_rdv]['MATTRESS']));
        $rdvs = $req->fetchAll();
        
        $ftr = $_SESSION['POST']['NEW_DATE_FTR'];
        foreach($rdvs as $rdv){
            
            if(date('Y-m-d',strtotime($ftr.' + 5 days')) > $rdv['SCN'] && $rdv['SCN'] >= date('Y-m-d',strtotime(date('Y-m-d').' + 3 days'))){
                return $rdv;
            }
        }
        return null;
    }
    
    public function getMS($id_rdv, $rdvtarget){
        $bdd = $this->bdd;
        
        // get the mattress type and urgence
        $req = $bdd->prepare("SELECT ID_TYPE, URGENT FROM mattress WHERE NAME=?");
        $req->execute(Array($_SESSION['RES'][$id_rdv]['MATTRESS']));
        $r = $req->fetch();
        $type = $r['ID_TYPE'];
        $urgent = $r['URGENT'];
        
        // select all mattress of the same type
        if($urgent == 0){
            $req = $bdd->prepare("SELECT mattress.ID AS ID
                            FROM mattress
                            INNER JOIN mattress_state ON mattress.ID_STATE = mattress_state.ID
                            WHERE ID_TYPE = ? AND mattress_state.NAME != 'Ne pas utiliser' AND URGENT = 0");
            $req->execute(Array($type));
        }
        else{
            $req = $bdd->prepare("SELECT mattress.ID AS ID
                            FROM mattress
                            INNER JOIN mattress_state ON mattress.ID_STATE = mattress_state.ID
                            WHERE ID_TYPE = ? AND mattress_state.NAME != 'Ne pas utiliser'");
            $req->execute(Array($type));
        }
        $ms = $req->fetchAll();
        
        for($i=0; $i<sizeof($ms); $i++){
            $today = date("Y-m-d");
            $ms[$i]['START'] = date('Y-m-d',strtotime($today.' + 5 days')); // START = today par default
            
            if($rdvtarget != null){
                $req = $bdd->prepare("SELECT DATE_FTR, DATE_SCANNER AS SCN, rdv.ID AS ID FROM rdv
                                    INNER JOIN mattress ON mattress.ID = rdv.ID_MATTRESS
                                    WHERE rdv.ID_STATE = 1 AND ID_MATTRESS = ? AND rdv.ID != ?"); // selectionner tout les rendez-vous type = normal de ce matelas sauf rdvtarget
                $req->execute(array($ms[$i]['ID'],$rdvtarget));
            }
            else{
                $req = $bdd->prepare("SELECT DATE_FTR, DATE_SCANNER AS SCN, rdv.ID AS ID FROM rdv
                                    INNER JOIN mattress ON mattress.ID = rdv.ID_MATTRESS
                                    WHERE rdv.ID_STATE = 1 AND ID_MATTRESS = ?"); // selectionner tout les rendez-vous type = normal de ce matelas
                $req->execute(array($ms[$i]['ID']));
            }
            $rdvs = $req->fetchAll();
            // trier les rdvs
            $rdvs = $this->bubbleSortRdv($rdvs);
            
            $j = 0;
            for($ind=0; $ind<sizeof($rdvs); $ind++){
                $date_scanner = $rdvs[$ind]['SCN'];
                $date_ftr = $rdvs[$ind]['DATE_FTR'];
                if($date_ftr > $today){
                    if($date_scanner < date('Y-m-d',strtotime($today.' + 3 days'))){ // rdv en cour, initialisation de START
                        $ms[$i]['START'] = date('Y-m-d',strtotime($date_ftr.' + 5 days'));
                    }
                    else{ // rdv a venir, on le place dans la structure ms
                        $ms[$i]['RDV'][$j]['SELECT'] = false;
                        $ms[$i]['RDV'][$j]['ID'] = $rdvs[$ind]['ID'];
                        $ms[$i]['RDV'][$j]['SCN'] = $date_scanner;
                        $ms[$i]['RDV'][$j]['FTR'] = $date_ftr;
                        $j++;
                    }
                }
            }
            if(!isset($ms[$i]['RDV'])){
                $ms[$i]['RDV'] = null;
            }
        }
        
        // DEBUG
        for($i=0; $i<sizeof($ms); $i++){
            echo $i.' - ID : '.$ms[$i]['ID'].' - START : '.$ms[$i]['START'].'<br>';
            if($ms[$i]['RDV'] != null){
                for($j=0; $j<sizeof($ms[$i]['RDV']); $j++){
                    echo $i.' - RDV - '.$j.' - ID : '.$ms[$i]['RDV'][$j]['ID'].' - SCN : '.$ms[$i]['RDV'][$j]['SCN'].' - FTR : '.$ms[$i]['RDV'][$j]['FTR'].'<br>';
                }
            }
        }
        
        return $ms;
    }
    
    public function updateAllRdv($ms){
        $bdd = $this->bdd;
        foreach($ms as $m){
            if($m['RDV'] != null){
                foreach ($m['RDV'] as $rdv){
                    $req = $bdd->prepare("UPDATE rdv SET ID_MATTRESS=? WHERE ID=?");
                    $req->execute([$m['ID'],$rdv['ID']]);
                }
            }
        }
    }
    
    public function matSwitch($m1, $m2){
        if($m1['RDV'] == null) return null;
        
        $switch = null;
        
        for($i=0; $i<sizeof($m1['RDV']); $i++){
            
            echo 'mat switch for : i = '.$i.'<br>';
            
            $cm1 = $m1;
            $cm1['RDV'][$i]['SELECT'] = true;
            $valid = true;
            
            $res = $this->rdvFight($cm1['RDV'][$i],$m2);
            if($res == null){
                echo 'valid = false<br>';
                $valid = false;
            }
            
            $tmp = $cm1;
            $cm1 = $res['MAT'];
            $cm2 = $tmp;
            
            // Select all the rdv to switch
            while( ($res['UP']!=null || $res['DOWN']!=null) && $valid){
                if($res['UP']!=null){
                    $resaux = $this->rdvFight($res['UP'],$cm2);
                    if($resaux == null){
                        echo 'valid = false (up)<br>';
                        $valid = false;
                        break;
                    }
                    $cm2 = $resaux['MAT'];
                }
                if($res['DOWN']!=null){
                    $resaux = $this->rdvFight($res['DOWN'],$cm2);
                    if($resaux == null){
                        echo 'valid = false (down)<br>';
                        $valid = false;
                        break;
                    }
                    $cm2 = $resaux['MAT'];
                }
                // invert cm1 and cm2 for the next loop
                $tmp = $cm1;
                $cm1 = $cm2;
                $cm2 = $tmp;
                
                $res = $resaux;
            }
            // return the mattresses if the switch worth it
            if($valid && $this->isWorth($cm1,$cm2)){
                echo 'switch worth it !<br>';
                // operate switch of selected rdvs
				$ind1 = 0;
				$ind2 = 0;
                $tmp1 = $cm1;
                $tmp2 = $cm2;
                $cm1['RDV'] = null;
                $cm2['RDV'] = null;
                
                // ajout de tout les rdv select de m2 dans m1
                for($x=0; $x<sizeof($tmp2['RDV']); $x++){
                    if($tmp2['RDV'][$x]['SELECT']){
                        echo 'add select rdv'.$tmp2['RDV'][$x]['ID'].' to '.$cm1['ID'].'<br>';
                        $cm1['RDV'][$ind1] = $tmp2['RDV'][$x];
                        $cm1['RDV'][$ind1]['SELECT'] = false;
						$ind1++;
                    }
                }
                
                if($tmp1['RDV'] != null){
                    // ajout des rdv de m1 non selectionné
                    for($x=0; $x<sizeof($tmp1['RDV']); $x++){
                        if(!$tmp1['RDV'][$x]['SELECT']){
							echo 'add not select rdv'.$tmp1['RDV'][$x]['ID'].' to '.$cm1['ID'].'<br>';
                            $cm1['RDV'][$ind1] = $tmp1['RDV'][$x];
							$ind1++;
                        }
                    }
                    // ajout de tout les rdv select de m1 dans m2
                    for($x=0; $x<sizeof($tmp1['RDV']); $x++){
                        if($tmp1['RDV'][$x]['SELECT']){
							echo 'add select rdv'.$tmp1['RDV'][$x]['ID'].' to '.$cm2['ID'].'<br>';
                            $cm2['RDV'][$ind2] = $tmp1['RDV'][$x];
                            $cm2['RDV'][$ind2]['SELECT'] = false;
							$ind2++;
                        }
                    }
                }
                
                // ajout des rdv de m2 non selectionné
                for($x=0; $x<sizeof($tmp2['RDV']); $x++){
                    if(!$tmp2['RDV'][$x]['SELECT']){
						echo 'add select rdv'.$cm1['RDV'][$x]['ID'].' to '.$cm2['ID'].'<br>';
                        $cm2['RDV'][$ind2] = $tmp2['RDV'][$x];
						$ind2++;
                    }
                }
                // trier a nouveau les rdvs
                $cm1['RDV'] = $this->bubbleSortRdv($cm1['RDV']);
                $cm2['RDV'] = $this->bubbleSortRdv($cm2['RDV']);
                
                $switch[0] = $cm1;
                $switch[1] = $cm2;
                return $switch;
            }
            else{
                
            }
        }
        return $switch;
    }
    
    public function rdvFight($rdv,$m){
        if($m['START'] > $rdv['SCN']){
            return null;
        }
        
        $res = null;
        $res['MAT'] = $m;
        $res['UP'] = null;
        $res['DOWN'] = null;
        
        if($m['RDV'] == null) return $res;
        
        for($i=0; $i<sizeof($m['RDV']); $i++){
            echo 'check rdv in fight : '.$m['RDV'][$i]['ID'].' - '.$m['RDV'][$i]['SELECT'].'<br>';
            // si le rdv n'a pas deja été selectionné
            if(!$m['RDV'][$i]['SELECT']){
                // si le select rdv est pendant le rdv
                if($m['RDV'][$i]['SCN'] >= $rdv['SCN'] && $m['RDV'][$i]['FTR'] <= $rdv['FTR']){
                    echo 'fight - inside - rdv : '.$m['RDV'][$i]['ID'].'<br>';
                    $res['MAT']['RDV'][$i]['SELECT'] = true;
                }
                // si il depasse en bas
                elseif($m['RDV'][$i]['SCN'] < $rdv['SCN'] && date('Y-m-d',strtotime($m['RDV'][$i]['FTR'].' + 5 days')) > $rdv['SCN']){
                    echo 'fight - down - rdv : '.$m['RDV'][$i]['ID'].'<br>';
                    $res['MAT']['RDV'][$i]['SELECT'] = true;
                    $res['DOWN'] = $m['RDV'][$i];
                }
                // si il depasse en haut
                elseif($m['RDV'][$i]['SCN'] < date('Y-m-d',strtotime($rdv['FTR'].' + 5 days')) && $m['RDV'][$i]['FTR'] > $rdv['FTR']){
                    echo 'fight - up - rdv : '.$m['RDV'][$i]['ID'].'<br>';
                    $res['MAT']['RDV'][$i]['SELECT'] = true;
                    $res['UP'] = $m['RDV'][$i];
                }
            }
        }
        return $res;
    }
    
    public function findPlace($ms,$scn,$ftr,$id){
        $res = null;
        for($i=0; $i<sizeof($ms); $i++){ // parcour des matelas
            $m = $ms[$i];
            $free = true;
            if($scn < $m['START']){
//                 echo 'find place - free = false <br>';
                $free = false;
            }
            else{
                for($j=0; $j<sizeof($m['RDV']); $j++){ // parcour des rdvs
                    $rdv = $m['RDV'][$j];
                    if( ( $scn >= $rdv['SCN'] && $ftr <= $rdv['FTR'] ) ||
                        ( $scn < $rdv['SCN'] && date('Y-m-d',strtotime($ftr.' + 5 days')) > $rdv['SCN'] ) ||
                        ( $scn < date('Y-m-d',strtotime($rdv['FTR'].' + 5 days')) && $ftr > $rdv['FTR'] )){
                            echo 'find place - free = false <br>';
                            $free = false;
                            break;
                    }
                }
            }
            
            if($free){ // si le matelas a une place pour le rdv on l'incére
                echo 'find place - free = true <br>';
                $res = $ms;
                $size = sizeof($res[$i]['RDV']);
                $res[$i]['RDV'][$size]['SELECT'] = false;
                $res[$i]['RDV'][$size]['ID'] = $id;
                $res[$i]['RDV'][$size]['SCN'] = $scn;
                $res[$i]['RDV'][$size]['FTR'] = $ftr;
                $res[$i]['RDV'] = $this->bubbleSortRdv($res[$i]['RDV']);
                
                for($i=0; $i<sizeof($res); $i++){
                    echo $i.' - ID : '.$res[$i]['ID'].' - START : '.$res[$i]['START'].'<br>';
                    if($res[$i]['RDV'] != null){
                        for($j=0; $j<sizeof($res[$i]['RDV']); $j++){
                            echo $i.' - RDV - '.$j.' - ID : '.$res[$i]['RDV'][$j]['ID'].' - SCN : '.$res[$i]['RDV'][$j]['SCN'].' - FTR : '.$res[$i]['RDV'][$j]['FTR'].'<br>';
                        }
                    }
                }
                
                break;
            }
        }
        return $res;
    }
    
    public function isWorth($m2,$m1){
        
        if($m2['RDV'] == null){// cas particulier
            $select = false;
            $bottom1 = $m1['START'];
            for($x=0; $x<sizeof($m1['RDV']); $x++){
                if($m1['RDV'][$x]['SELECT']){
                    $select = true;
                    break;
                }
                $bottom1 = date('Y-m-d',strtotime($m1['RDV'][$x]['FTR'].' + 5 days'));
            }
            if($bottom1 < $m2['START'] && $select) return true;
        }
        else{
            $scn1 = null;
            $scn2 = null;
            $ftr1 = null;
            $ftr2 = null;
            $bottom1 = $m1['START'];
            $bottom2 = $m2['START'];
            
            for($x=0; $x<sizeof($m1['RDV']); $x++){
                if($m1['RDV'][$x]['SELECT']){
                    $ftr1 = $m1['RDV'][$x]['FTR'];
                    $scn1 = $m1['RDV'][$x]['SCN'];
                    break;
                }
                $bottom1 = date('Y-m-d',strtotime($m1['RDV'][$x]['FTR'].' + 5 days'));
            }
            for($x=0; $x<sizeof($m2['RDV']); $x++){
                if($m2['RDV'][$x]['SELECT']){
                    $ftr1 = $m2['RDV'][$x]['FTR'];
                    $scn2 = $m2['RDV'][$x]['SCN'];
                    break;
                }
                $bottom2 = date('Y-m-d',strtotime($m2['RDV'][$x]['FTR'].' + 5 days'));
            }
            
            if($scn1 != null && $scn2 != null){
                echo 'is worth - bottom1 : '.$bottom1.' - bottom2 : '.$bottom2.' - scn1 : '.$scn1.' - scn2 : '.$scn2.'<br>';
                if(($bottom1 < $bottom2 && $scn1 < $scn2) || ($bottom1 > $bottom2 && $scn1 > $scn2)) return true;
            }
            elseif($scn1 != null){
                // trouver le bon bottom2 quand scn2 est null
                $bottom2 = $m2['START'];
                for($x=0; $x<sizeof($m2['RDV']); $x++){
                    if($m2['RDV'][$x]['SCN'] > $ftr1) break;
                    $bottom2 = date('Y-m-d',strtotime($m2['RDV'][$x]['FTR'].' + 5 days'));
                }
                echo 'is worth - bottom1 : '.$bottom1.' - bottom2 : '.$bottom2.' - scn1 : '.$scn1.'<br>';
                if($bottom1 < $bottom2) return true;
            }
            elseif($scn2 != null){
                // trouver le bon bottom1
                $bottom1 = $m1['START'];
                for($x=0; $x<sizeof($m1['RDV']); $x++){
                    if($m1['RDV'][$x]['SCN'] > $ftr2) break;
                    $bottom1 = date('Y-m-d',strtotime($m1['RDV'][$x]['FTR'].' + 5 days'));
                }
                echo 'is worth - bottom1 : '.$bottom1.' - bottom2 : '.$bottom2.' - scn2 : '.$scn2.'<br>';
                if($bottom2 < $bottom1) return true;
            }
           
        }
        return false;
    }
    
    public function modify(){
        $bdd = $this->bdd;
        
        $id_rdv = 0;
        $res = null;
        if(isset($_SESSION['POST']['ID_RDV'])){
            $id_rdv = $_SESSION['POST']['ID_RDV'];
            $res = $_SESSION['RES'][$id_rdv];
        }
        
        // -- UPDATES --
        // NAME
        if(isset($_SESSION['POST']['NEW_NAME'])){
            $id = $res['ID_PATIENT'];
            $name = $_SESSION['POST']['NEW_NAME'];
            $req = $bdd->prepare("UPDATE patients SET NAME=? WHERE ID=?");
            $req->execute([$name,$id]);
            
            $_SESSION['RES'][$id_rdv]['NAME'] = $name;
            return 'SUCCESS';
            
        }
        // SURNAME
        elseif(isset($_SESSION['POST']['NEW_SURNAME'])){
            $id = $res['ID_PATIENT'];
            $surname = $_SESSION['POST']['NEW_SURNAME'];
            $req = $bdd->prepare("UPDATE patients SET SURNAME=? WHERE ID=?");
            $req->execute([$surname,$id]);
            
            $_SESSION['RES'][$id_rdv]['SURNAME'] = $surname;
            return 'SUCCESS';
        }
        // NIP
        elseif(isset($_SESSION['POST']['NEW_NIP'])){
            $id = $res['ID_PATIENT'];
            $nip = $_SESSION['POST']['NEW_NIP'];
            if($nip > 9999999999){
                return 'BIG_NIP';
            }
            $req = $bdd->prepare("UPDATE patients SET NIP=? WHERE ID=?");
            $req->execute([$nip,$id]);
            
            $_SESSION['RES'][$id_rdv]['NIP'] = $_SESSION['POST']['NEW_NIP'];
            return 'SUCCESS';
        }
        // DATE_SCANNER
        elseif(isset($_SESSION['POST']['NEW_DATE_SCANNER'])){
            $id = $res['ID_RDV'];
            $date_scanner = $_SESSION['POST']['NEW_DATE_SCANNER'];
            $mep = $res['DATE_MEP'];
            
            if($date_scanner >= $mep){
                return 'UNVALID_SCN';
            }
            
            // si la date est inferieur a j+3 alors on n'effectu aucune vérification
            if($date_scanner >= date('Y-m-d',strtotime(date('Y-m-d').' + 3 days'))){
                // build structure for optimisation
                $ms = $this->getMS($id_rdv, $id);
                $msfound = $this->findPlace($ms, $date_scanner, $res['DATE_FTR'], $id);
                // if there's no place for the rdv try optimisation and look again for places
                if($msfound == null){
                    $ms = $this->computeOp($ms);
                    $msfound = $this->findPlace($ms, $date_scanner, $res['DATE_FTR'], $id);
                    if($msfound == null){
                        return 'UNVALID_SCN';
                    }
                }
                $this->updateAllRdv($msfound);
            }
            
            $req = $bdd->prepare("UPDATE rdv SET DATE_SCANNER=? WHERE ID=?");
            $req->execute([$date_scanner,$id]);
            
            $req = $bdd->prepare("SELECT mattress.NAME FROM rdv
                                    INNER JOIN mattress ON mattress.ID = rdv.ID_MATTRESS
                                    WHERE rdv.ID=?");
            $req->execute(Array($id));
            $r = $req->fetch();
            $m = $r['NAME'];
            
            $_SESSION['RES'][$id_rdv]['MATTRESS'] = $m;
            $_SESSION['RES'][$id_rdv]['DATE_SCANNER'] = $date_scanner;
            return 'SUCCESS';
        }
        // DATE_MEP
        elseif(isset($_SESSION['POST']['NEW_DATE_MEP'])){
            $id = $res['ID_RDV'];
            $scn = $res['DATE_SCANNER'];
            $ftr = $res['DATE_FTR'];
            $date_mep = $_SESSION['POST']['NEW_DATE_MEP'];
            if($date_mep <= $scn || $date_mep >= $ftr) return 'UNVALID_MEP';
            $req = $bdd->prepare("UPDATE rdv SET DATE_MEP=? WHERE ID=?");
            $req->execute([$date_mep,$id]);
            
            $_SESSION['RES'][$id_rdv]['DATE_MEP'] = $date_mep;
            return 'SUCCESS';
        }
        // DATE_FTR
        elseif(isset($_SESSION['POST']['NEW_DATE_FTR'])){
            $id = $res['ID_RDV'];
            $date_ftr = $_SESSION['POST']['NEW_DATE_FTR'];
            $mep = $res['DATE_MEP'];
            $date_scanner = $res['DATE_SCANNER'];
            
            if($date_ftr <= $mep){
                return 'UNVALID_FTR';
            }
            
            // si la date ftr est inferieur ou egual a j alors on n'effectu aucune vérification
            if($date_ftr > date('Y-m-d')){
                // si la date scn est supperieur on peut deplacer le rdv
                if($date_scanner >= date('Y-m-d',strtotime(date('Y-m-d').' + 3 days'))){
                    $ms = $this->getMS($id_rdv, $id);
                    $msfound = $this->findPlace($ms, $date_scanner, $date_ftr, $id);
                    
                    if($msfound == null){
                        $ms = $this->computeOp($ms);
                        $msfound = $this->findPlace($ms, $date_scanner, $date_ftr, $id);
                        if($msfound == null){
                            return 'UNVALID_FTR';
                        }
                    }
                    
                    $this->updateAllRdv($msfound);
                }
                // le rdv ne peux pas bouger, on dois bouger le rdv du dessus
                else{
                    $rdv = $this->getUpperRdv($id_rdv);
                    
                    if($rdv!=null){
                        $ms = $this->getMS($id_rdv, $rdv['ID']);
                        
                        // decaler la date START au bon endrois de la structure MS
                        for($i=0; $i<sizeof($ms); $i++){
                            // trouver le bon matelas
                            if($ms[$i]['ID'] == $rdv['ID_MATTRESS']){
                                // changer la date START
                                $ms[$i]['START'] = date('Y-m-d',strtotime($date_ftr.' + 5 days'));
                            }
                        }
                        // try adding the upper rdv after changes
                        $msfound = $this->findPlace($ms, $rdv['SCN'], $rdv['FTR'], $rdv['ID']);
                        
                        if($msfound == null){
                            $ms = $this->computeOp($ms);
                            $msfound = $this->findPlace($ms, $rdv['SCN'], $rdv['FTR'], $rdv['ID']);
                            if($msfound == null){
                                return 'UNVALID_FTR';
                            }
                        }
                        
                        $this->updateAllRdv($msfound);
                    }
                }
            }
            $req = $bdd->prepare("UPDATE rdv SET DATE_FTR=? WHERE ID=?");
            $req->execute([$date_ftr,$id]);
            
            $req = $bdd->prepare("SELECT mattress.NAME FROM rdv
                                    INNER JOIN mattress ON mattress.ID = rdv.ID_MATTRESS
                                    WHERE rdv.ID=?");
            $req->execute(Array($id));
            $r = $req->fetch();
            $m = $r['NAME'];
            
            $_SESSION['RES'][$id_rdv]['MATTRESS'] = $m;
            
            $_SESSION['RES'][$id_rdv]['DATE_FTR'] = $date_ftr;
            return 'SUCCESS';
        }
        // NB_SESSION
        elseif(isset($_SESSION['POST']['NEW_NB_SESSION'])){
            $id = $res['ID_RDV'];
            $nb_session = $_SESSION['POST']['NEW_NB_SESSION'];
            $req = $bdd->prepare("UPDATE rdv SET NB_SESSION=? WHERE ID=?");
            $req->execute([$nb_session,$id]);
            
            $_SESSION['RES'][$id_rdv]['NB_SESSION'] = $nb_session;
            return 'SUCCESS';
        }
        // MATTRESS
        elseif(isset($_SESSION['POST']['NEW_MATTRESS'])){
            
            $id = $res['ID_RDV'];
            $mattress = $_SESSION['POST']['NEW_MATTRESS'];
            
            // find mattress type
            $type = $this->getType($res['MATTRESS']);
            
            // find mattress id
            $req = $bdd->prepare("SELECT ID FROM mattress WHERE NAME=? AND ID_TYPE=? AND ID_STATE != 3");
            $req->execute(array($mattress,$type));
            $r = $req->fetch();
            if(!isset($r['ID'])){
                return 'INVALID_MATTRESS';
            }
            $id_mattress = $r['ID'];
            
            // find all rdv for this mattress
            $req = $bdd->prepare("SELECT DATE_FTR AS FTR, DATE_SCANNER AS SCN FROM rdv
                                    INNER JOIN mattress ON mattress.ID = rdv.ID_MATTRESS
                                    WHERE rdv.ID_STATE = 1 AND ID_MATTRESS = ?");
            $req->execute(array($id_mattress));
            $rdv = $req->fetchAll();
            
            $scn = $res['DATE_SCANNER'];
            $ftr = $res['DATE_FTR'];
            for($i=0; $i<sizeof($rdv); $i++){ // parcour des rdvs
                if( ( $scn >= $rdv[$i]['SCN'] && $ftr <= $rdv[$i]['FTR'] ) ||
                    ( $scn < $rdv[$i]['SCN'] && date('Y-m-d',strtotime($ftr.' + 5 days')) > $rdv[$i]['SCN'] ) ||
                    ( $scn < date('Y-m-d',strtotime($rdv[$i]['FTR'].' + 5 days')) && $ftr > $rdv[$i]['FTR'] )){
                    return 'UNDISP_MATTRESS'; // matelas occupé
                }
            }
            
            $req = $bdd->prepare("UPDATE rdv SET ID_MATTRESS=? WHERE ID=?");
            $req->execute([$id_mattress,$id]);
            
            $_SESSION['RES'][$id_rdv]['MATTRESS'] = $mattress;
            return 'SUCCESS';
        }
        // NOTE
        elseif(isset($_SESSION['POST']['NEW_NOTES'])){
            $id = $res['ID_RDV'];
            $notes = $_SESSION['POST']['NEW_NOTES'];
            $req = $bdd->prepare("UPDATE rdv SET NOTE=? WHERE ID=?");
            $req->execute([$notes,$id]);
            
            $_SESSION['RES'][$id_rdv]['NOTE'] = $notes;
            return 'SUCCESS';
        }
        // STATE
        elseif(isset($_SESSION['POST']['NEW_STATE'])){
            
            $id = $res['ID_RDV'];
            $id_state = $_SESSION['POST']['NEW_STATE'];
            if($id_state == -1){
                return 'EMPTY_STATE';
            }
            
            $req = $bdd->prepare("UPDATE rdv SET ID_STATE=? WHERE ID=?");
            $req->execute([$id_state,$id]);
            
            switch($id_state){
                case 1:
                    $_SESSION['RES'][$id_rdv]['STATE'] = 'Normal';
                    break;
                case 2:
                    $_SESSION['RES'][$id_rdv]['STATE'] = 'Reporté';
                    break;
                case 3:
                    $_SESSION['RES'][$id_rdv]['STATE'] = 'Annulé';
                    break;
            }
            return 'SUCCESS';
        }
        else{
            return '!';
        }
    }
    
}
?>