<?php

class InfoMatModel{
    
    private $bdd;
    private $mat;
    
    public function __construct($bdd){
        $this->mat = $_SESSION['POST']['ID_MATTRESS'];
        $this->bdd = $bdd;
    }
    
    public function getMat(){
        $bdd = $this->bdd;
        
        $req = $bdd->prepare("SELECT mattress.ID AS ID, mattress.NAME AS NAME, type.NAME AS TYPE, mattress_state.NAME AS STATE, URGENT
                                FROM mattress
                                INNER JOIN type ON type.ID = mattress.ID_TYPE
                                INNER JOIN mattress_state on mattress_state.ID = mattress.ID_STATE
                                WHERE mattress.ID=?");
        $req->execute(Array($this->mat));
        return $req->fetch();
    }
  
    public function modify(){
        $bdd = $this->bdd;
    	
    	if(isset($_SESSION['POST']['NEW_NAME'])){
    	    $name = $_SESSION['POST']['NEW_NAME'];
    	    //V�rifier si le nom est d�ja dans la base de donn�e
    	    $req = $bdd->prepare("SELECT NAME FROM mattress WHERE NAME = ?");
    	    $req->execute(array($name));
    	    $res = $req->fetch();
    	    if($res['NAME']){
    	        return 'INVALID_NAME';
    	    }
    	    $req = $bdd->prepare("UPDATE mattress SET NAME=? WHERE ID=?");
    	    $req->execute([$name,$this->mat]);
    	    return 'SUCCESS';
    	}
    	elseif(isset($_SESSION['POST']['NEW_TYPE'])){
    	    $type = $_SESSION['POST']['NEW_TYPE'];
    	    if($type == -1){
    	        return 'TYPE_EMPTY';
    	    }
    	    $req = $bdd->prepare("UPDATE mattress SET ID_TYPE=? WHERE ID=?");
    	    $req->execute([$type,$this->mat]);
    	    return 'SUCCESS';
    	}
    	elseif(isset($_SESSION['POST']['NEW_STATE'])){
    	    $state = $_SESSION['POST']['NEW_STATE'];
    	    if($state == -1){
    	        return 'STATE_EMPTY';
    	    }
    	    $req = $bdd->prepare("UPDATE mattress SET ID_STATE=? WHERE ID=?");
    	    $req->execute([$state,$this->mat]);
    	    return 'SUCCESS';
    	}
    	elseif(isset($_SESSION['POST']['NEW_URGENT'])){
    	    $req = $bdd->prepare("UPDATE mattress SET URGENT=? WHERE ID=?");
    	    $req->execute([$_SESSION['POST']['NEW_URGENT'],$this->mat]);
    	    return 'SUCCESS';
    	}
    	return '!';
    }

}
?>