<?php

class MatStatisticModel{
    public $statePoints;
    public $nbMatDisp;
    public $nbMatTotal;

    private $bdd;

    public function __construct($bdd){
        // Establishement of connexion to the database
        $this->bdd = $bdd;
    }

    public function computeForAll(){
        $bdd = $this->bdd;
        
        $model = new GetTabModel($bdd);
        
        $ms = $model->getAllMattress('!');
        $nbMatDisp = 0;
        foreach($ms as $m){
            if($m['DISPO'] == 1){
                $nbMatDisp++;
            }
        }
        $this->nbMatDisp = $nbMatDisp;

        // find nb all mattress
        $req = $bdd->prepare("SELECT COUNT(*) AS NB_MATTRESS
                            FROM mattress");
        $req->execute();
        $res = $req->fetch();
        $this->nbMatTotal = $res['NB_MATTRESS'];

        // find nb of BON mattress
        $req = $bdd->prepare("SELECT COUNT(*) AS NB_MATTRESS_BON
                            FROM mattress
                            INNER JOIN mattress_state ON mattress.ID_STATE = mattress_state.ID
                            WHERE mattress_state.NAME = 'Bon'");
        $req->execute();
        $res = $req->fetch();
        $nbBon = $res['NB_MATTRESS_BON'];

        // find nb of A Remplacer mattress
        $req = $bdd->prepare("SELECT COUNT(*) AS NB_MATTRESS_AR
                            FROM mattress
                            INNER JOIN mattress_state ON mattress.ID_STATE = mattress_state.ID
                            WHERE mattress_state.NAME = 'A remplacer'");
        $req->execute();
        $res = $req->fetch();
        $nbAR = $res['NB_MATTRESS_AR'];

        // find nb of Ne pas utiliser mattress
        $req = $bdd->prepare("SELECT COUNT(*) AS NB_MATTRESS_NPU
                            FROM mattress
                            INNER JOIN mattress_state ON mattress.ID_STATE = mattress_state.ID
                            WHERE mattress_state.NAME = 'Ne pas utiliser'");
        $req->execute();
        $res = $req->fetch();
        $nbNPU = $res['NB_MATTRESS_NPU'];

        // make array of array for graph
        if($this->nbMatTotal > 0){
            $this->statePoints['BON'] = round($nbBon*100/$this->nbMatTotal,2);
            $this->statePoints['AR'] = round($nbAR*100/$this->nbMatTotal,2);
            $this->statePoints['NPU'] = round($nbNPU*100/$this->nbMatTotal,2);
        }
        else{
            $this->statePoints = 0;
        }
    }

    public function computeForType($tab){
        $bdd = $this->bdd;

        $model = new GetTabModel($bdd);
        
        $ms = $model->getAllMattress($tab);
        $nbMatDisp = 0;
        foreach($ms as $m){
            if($m['DISPO'] == 1){
                $nbMatDisp++;
            }
        }
        $this->nbMatDisp = $nbMatDisp;

        // find nb all mattress
        $req = $bdd->prepare("SELECT COUNT(*) AS NB_MATTRESS
                            FROM mattress
                            INNER JOIN type ON mattress.ID_TYPE = type.ID
                            WHERE type.NAME = ?");
        $req->execute(Array($tab));
        $res = $req->fetch();
        $this->nbMatTotal = $res['NB_MATTRESS'];

        // find nb of BON mattress
        $req = $bdd->prepare("SELECT COUNT(*) AS NB_MATTRESS_BON
                            FROM mattress
                            INNER JOIN type ON mattress.ID_TYPE = type.ID
                            INNER JOIN mattress_state ON mattress.ID_STATE = mattress_state.ID
                            WHERE mattress_state.NAME = 'Bon' AND type.NAME = ?");
        $req->execute(Array($tab));
        $res = $req->fetch();
        $nbBon = $res['NB_MATTRESS_BON'];

        // find nb of A Remplacer mattress
        $req = $bdd->prepare("SELECT COUNT(*) AS NB_MATTRESS_AR
                            FROM mattress
                            INNER JOIN type ON mattress.ID_TYPE = type.ID
                            INNER JOIN mattress_state ON mattress.ID_STATE = mattress_state.ID
                            WHERE mattress_state.NAME = 'A remplacer' AND type.NAME = ?");
        $req->execute(Array($tab));
        $res = $req->fetch();
        $nbAR = $res['NB_MATTRESS_AR'];

        // find nb of Ne pas utiliser mattress
        $req = $bdd->prepare("SELECT COUNT(*) AS NB_MATTRESS_NPU
                            FROM mattress
                            INNER JOIN type ON mattress.ID_TYPE = type.ID
                            INNER JOIN mattress_state ON mattress.ID_STATE = mattress_state.ID
                            WHERE mattress_state.NAME = 'Ne pas utiliser' AND type.NAME = ?");
        $req->execute(Array($tab));
        $res = $req->fetch();
        $nbNPU = $res['NB_MATTRESS_NPU'];

        // make array of array for graph
        if($this->nbMatTotal > 0){
            $this->statePoints['BON'] = round($nbBon*100/$this->nbMatTotal,2);
            $this->statePoints['AR'] = round($nbAR*100/$this->nbMatTotal,2);
            $this->statePoints['NPU'] = round($nbNPU*100/$this->nbMatTotal,2);
        }
        else{
            $this->statePoints = 0;
        }
    }
}
?>