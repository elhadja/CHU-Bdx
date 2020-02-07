<?php
include_once 'Header.php';
include_once 'controller/MattressTabController.php';

class InfoPatRDVView{
    
    //Déclaration de variable
    private $type;
    private $pagemessage;
    private $bdd;
    
    public function __construct($bdd){
        $this->bdd = $bdd;
    }
    
    public function setmessage($msg){
        $this->pagemessage = $msg;
    }
    
    public function setType($type){
        $this->type = $type;
    }
    
    //Launch
    public function launch(){
        $header = new Header("Info Traitement");
        $header->launch();
        
        if(isset($_SESSION['POST']['ID_RDV'])){
            $id_rdv = $_SESSION['POST']['ID_RDV'];
        }
        else{
            $id_rdv = 0;
        }
        
        // res est récupéré dans la fonction getResearchTab du model GetTabModel
        $rdv = $_SESSION['RES'][$id_rdv];
        
        $name = $rdv['NAME'];
        $surname = $rdv['SURNAME'];
        $nip = $rdv['NIP'];
        
        $date_scanner = $rdv['DATE_SCANNER'];
        $format = new DateTime($date_scanner);
        $date_scanner = $format->format('d\/m\/y');
        
        $date_mep = $rdv['DATE_MEP'];
        $format = new DateTime($date_mep);
        $date_mep = $format->format('d\/m\/y');
        
        $date_ftr = $rdv['DATE_FTR'];
        $format = new DateTime($date_ftr);
        $date_ftr = $format->format('d\/m\/y');
        
        $nb_session = $rdv['NB_SESSION'];
        $mattress = $rdv['MATTRESS'];
        $state = $rdv['STATE'];
        
        $user = $rdv['USER'];
        $note = $rdv['NOTE'];
        
        echo '
        <div class="container p-3"><h4>Information sur le traitement</h4>
        <div class="container-fluid text-center"><h6>'.$this->pagemessage.'</h6></div>

        <table class="table table-bordered">
        
        <tr>
        <th class="table-info">Nom</th>
        <td>
        <div class="row">
        <div class="col text-center">'.$name.'</div>
        <div class="col text-right">
        <a class="nav-link ml-1 mr-1" data-target="#Name_Modify" href="#Name_Modify" data-toggle="modal">Modifier</a>
        </div></div></td></tr>
        
        <tr>
        <th class="table-info">Prénom</th>
        <td>
        <div class="row">
        <div class="col text-center">'.$surname.'</div>
        <div class="col text-right">
        <a class="nav-link ml-1 mr-1" data-target="#Surname_Modify" href="#Surname_Modify" data-toggle="modal">Modifier</a>
        </div></div></td></tr>
        
        <tr>
        <th class="table-info">NIP</th>
        <td>
        <div class="row">
        <div class="col text-center">'.$nip.'</div>
        <div class="col text-right">
        <a class="nav-link ml-1 mr-1" data-target="#NIP_Modify" href="#NIP_Modify" data-toggle="modal">Modifier</a>
        </div></div></td></tr>
        
        <tr>
        <th class="table-info">Date Scanner</th>
        <td>
        <div class="row">
        <div class="col text-center">'.$date_scanner.'</div>
        <div class="col text-right">
        <a class="nav-link ml-1 mr-1" data-target="#Date_Scanner_Modify" href="#Date_Scanner_Modify" data-toggle="modal">Modifier</a>
        </div></div></td></tr>
        
        <tr>
        <th class="table-info">Date MEP</th>
        <td>
        <div class="row">
        <div class="col text-center">'.$date_mep.'</div>
        <div class="col text-right">
        <a class="nav-link ml-1 mr-1" data-target="#Date_MEP_Modify" href="#Date_MEP_Modify" data-toggle="modal">Modifier</a>
        </div></div></td></tr>
        
        <tr>
        <th class="table-info">Date FTR</th>
        <td>
        <div class="row">
        <div class="col text-center">'.$date_ftr.'</div>
        <div class="col text-right">
        <a class="nav-link ml-1 mr-1" data-target="#Date_FTR_Modify" href="#Date_FTR_Modify" data-toggle="modal">Modifier</a>
        </div></div></td></tr>
        
        <tr>
        <th class="table-info">Sessions</th>
        <td>
        <div class="row">
        <div class="col text-center">'.$nb_session.'</div>
        <div class="col text-right">
        <a class="nav-link ml-1 mr-1" data-target="#NB_SESSION_Modify" href="#NB_SESSION_Modify" data-toggle="modal">Modifier</a>
        </div></div></td></tr>
        
        <tr>
        <th class="table-info">Matelas</th>
        <td>
        <div class="row">
        <div class="col text-center">'.$mattress.'</div>
        <div class="col text-right">
        <a class="nav-link ml-1 mr-1" data-target="#Mattress_Modify" href="#Mattress_Modify" data-toggle="modal">Modifier</a>
        </div></div></td></tr>
        
        <tr>
        <th class="table-info">Etat</th>
        <td>
        <div class="row">
        <div class="col text-center">'.$state.'</div>
        <div class="col text-right">
        <a class="nav-link ml-1 mr-1" data-target="#State_Modify" href="#State_Modify" data-toggle="modal">Modifier</a>
        </div></div></td></tr>
        
        <tr>
        <th class="table-info">Notes</th>
        <td>
        <div class="row">
        <div class="col text-center">'.$note.'</div>
        <div class="col text-right">
        <a class="nav-link ml-1 mr-1" data-target="#Notes_Modify" href="#Notes_Modify" data-toggle="modal">Modifier</a>
        </div></div></td></tr>
        
        <tr>
        <th class="table-info">Responsable</th>
        
        <td>
        <div class="row">
        <div class="col text-center">'.$user.'</div>
        <div class="col"></div></div></td></tr>
        
        </table>
        </thead>
        
        <div class="container-fluid text-right"><a data-target="#Delete" href="#Delete" data-toggle="modal" class="badge badge-danger align-right">Supprimer</a></div>
        
        <h5>Matelas de même type :</h5>';
        
        $mattab = new MattressTabController($this->bdd);
        if($this->type == 1){
            $mattab->launch("Crane");
        }
        else if($this->type == 2){
            $mattab->launch("Thorax");
        }
        else if($this->type == 3){
            $mattab->launch("Abdomen");
        }
        echo <<<VIEW

        <!-- Modal Delete -->
        <div class="modal fade" id="Delete" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Supprimer le traitement ?</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
        <div class="modal-body">
        Le traitement sera supprimé définitivement de la base de données.
        <form action="index.php" method="POST">
        <input type="hidden" name="TASK" value="DelPat">
        <input type="hidden" name="ID_RDV" value=$id_rdv>
        <input type="hidden" name="DELETEPAT" value="NO">
        <div class="form-group form-check">
        <input class="form-check-input" type="checkbox" name="DELETEPAT" value="YES" id="deletepat">
        <label class="form-check-label" for="deletepat">Supprimer aussi les données du patient</label>
        </div></div>
        <div class="modal-footer">
        <input class="btn btn-danger" type="submit" value="Supprimer">
        </div>
        </form></div></div></div></div>
        
        <!-- Modal Modify Name -->
        <div class="modal fade" id="Name_Modify" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Modifier le nom du patient</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
        <div class="modal-body">
        <form action="index.php" method="POST">
        <input type="hidden" name="TASK" value="InfoPatRDV">
        <input type="hidden" name="ID_RDV" value=$id_rdv>
        <div class="form-group">
        <label for="exampleInputUsername1">Nom actuel</label>
        <input type="text" name="NAME" class="form-control" placeholder="$name" disabled>
        </div><div class="form-group">
        <label for="exampleInputPassword1">Nouveau nom</label>
        <input type="text" name="NEW_NAME" class="form-control" placeholder="" required>
        </div>
        <input type="submit" value="Modifier">
        </form></div></div></div></div>
        
        <!-- Modal Modify Surname -->
        <div class="modal fade" id="Surname_Modify" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Modifier le prénom du patient</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
        <div class="modal-body">
        <form action="index.php" method="POST">
        <input type="hidden" name="TASK" value="InfoPatRDV">
        <input type="hidden" name="ID_RDV" value=$id_rdv>
        <div class="form-group">
        <label for="exampleInputUsername1">Prénom actuel</label>
        <input type="text" name="SURNAME" class="form-control" placeholder="$surname" disabled>
        </div><div class="form-group">
        <label for="exampleInputPassword1">Nouveau prénom</label>
        <input type="text" name="NEW_SURNAME" class="form-control" placeholder="" required>
        </div>
        <input type="submit" value="Modifier">
        </form></div></div></div></div>
        
        <!-- Modal Modify NIP -->
        <div class="modal fade" id="NIP_Modify" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Modifier le NIP du patient</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
        <div class="modal-body">
        <form action="index.php" method="POST">
        <input type="hidden" name="TASK" value="InfoPatRDV">
        <input type="hidden" name="ID_RDV" value=$id_rdv>
        <div class="form-group">
        <label for="exampleInputUsername1">NIP actuel</label>
        <input type="number" name="NIP" class="form-control" placeholder="$nip" disabled>
        </div><div class="form-group">
        <label for="exampleInputPassword1">Nouveau NIP</label>
        <input type="number" name="NEW_NIP" class="form-control" placeholder="" required>
        </div>
        <input type="submit" value="Modifier">
        </form></div></div></div></div>
        
        <!-- Modal Modify Date Scanner -->
        <div class="modal fade" id="Date_Scanner_Modify" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Modifier la date de scanner</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
        <div class="modal-body">
        <form action="index.php" method="POST">
        <input type="hidden" name="TASK" value="InfoPatRDV">
        <input type="hidden" name="ID_RDV" value=$id_rdv>
        <div class="form-group">
        <label for="exampleInputUsername1">Date de scanner actuelle</label>
        <input type="text" name="DATE_SCANNER" class="form-control" placeholder="$date_scanner" disabled>
        </div><div class="form-group">
        <label for="exampleInputPassword1">Nouvelle date de scanner</label>
        <input type="date" name="NEW_DATE_SCANNER" class="form-control" placeholder="" required>
        </div>
        <input type="submit" value="Modifier">
        </form></div></div></div></div>
        
        <!-- Modal Modify Date MEP -->
        <div class="modal fade" id="Date_MEP_Modify" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Modifier la Date MEP</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
        <div class="modal-body">
        <form action="index.php" method="POST">
        <input type="hidden" name="TASK" value="InfoPatRDV">
        <input type="hidden" name="ID_RDV" value=$id_rdv>
        <div class="form-group">
        <label for="exampleInputUsername1">Date MEP actuelle</label>
        <input type="text" name="DATE_MEP" class="form-control" placeholder="$date_mep" disabled>
        </div><div class="form-group">
        <label for="exampleInputPassword1">Nouvelle Date MEP</label>
        <input type="date" name="NEW_DATE_MEP" class="form-control" placeholder="" required>
        </div>
        <input type="submit" value="Modifier">
        </form></div></div></div></div>
        
        <!-- Modal Modify Date FTR -->
        <div class="modal fade" id="Date_FTR_Modify" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Modifier la Date FTR</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
        <div class="modal-body">
        <form action="index.php" method="POST">
        <input type="hidden" name="TASK" value="InfoPatRDV">
        <input type="hidden" name="ID_RDV" value=$id_rdv>
        <div class="form-group">
        <label for="exampleInputUsername1">Date FTR actuelle</label>
        <input type="text" name="DATE_FTR" class="form-control" placeholder="$date_ftr" disabled>
        </div><div class="form-group">
        <label for="exampleInputPassword1">Nouvelle Date FTR</label>
        <input type="date" name="NEW_DATE_FTR" class="form-control" placeholder="" required>
        </div>
        <input type="submit" value="Modifier">
        </form></div></div></div></div>
        
        <!-- Modal Modify NB_SESSION -->
        <div class="modal fade" id="NB_SESSION_Modify" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Modifier le nombre de séances</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
        <div class="modal-body">
        <form action="index.php" method="POST">
        <input type="hidden" name="TASK" value="InfoPatRDV">
        <input type="hidden" name="ID_RDV" value=$id_rdv>
        <div class="form-group">
        <label for="exampleInputUsername1">Nombre de séances actuel</label>
        <input type="number" name="NB_SESSION" class="form-control" placeholder="$nb_session" disabled>
        </div><div class="form-group">
        <label for="exampleInputPassword1">Nouveau nombre de séances</label>
        <input type="numer" name="NEW_NB_SESSION" class="form-control" placeholder="" required>
        </div>
        <input type="submit" value="Modifier">
        </form></div></div></div></div>
        
        <!-- Modal Modify Mattress Number -->
        <div class="modal fade" id="Mattress_Modify" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Modifier le matelas utilisé</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
        <div class="modal-body">
        <form action="index.php" method="POST">
        <input type="hidden" name="TASK" value="InfoPatRDV">
        <input type="hidden" name="ID_RDV" value=$id_rdv>
        <div class="form-group">
        <label for="exampleInputUsername1">Matelas actuel</label>
        <input type="text" name="MATTRESS" class="form-control" placeholder="$mattress" disabled>
        </div><div class="form-group">
        <label for="exampleInputPassword1">Nouveau Matelas</label>
        <input type="text" name="NEW_MATTRESS" class="form-control" placeholder="" required>
        </div>
        <input type="submit" value="Modifier">
        </form></div></div></div></div>
        
        <!-- Modal Modify Notes -->
        <div class="modal fade" id="Notes_Modify" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Modifier les notes concernant ce traitement</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
        <div class="modal-body">
        <form action="index.php" method="POST">
        <input type="hidden" name="TASK" value="InfoPatRDV">
        <input type="hidden" name="ID_RDV" value=$id_rdv>
        <div class="form-group">
        <label for="exampleInputUsername1">Notes actuelles</label>
        <input type="text" name="NOTES" class="form-control" placeholder="$note" disabled>
        </div><div class="form-group">
        <label for="exampleInputPassword1">Nouvelles notes</label>
        <input type="text" name="NEW_NOTES" class="form-control" placeholder="">
        </div>
        <input type="submit" value="Modifier">
        </form></div></div></div></div>
        
        <!-- Modal Modify State -->
        <div class="modal fade" id="State_Modify" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Modifier l'état du traitement</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
        <div class="modal-body">
        <form action="index.php" method="POST">
        <input type="hidden" name="TASK" value="InfoPatRDV">
        <input type="hidden" name="ID_RDV" value=$id_rdv>
        <div class="form-group">
        <label for="exampleInputUsername1">Etat actuel</label>
        <input type="text" name="STATE" class="form-control" placeholder="$state" disabled>
        </div>
        <div class="form-group">
            <label for="NEW_STATE">Nouvel état</label>
            <select name="NEW_STATE" class="form-control">
                <option value="-1">--Etat--</option>
                <option value="1">Normal</option>
                <option value="2">Reporté</option>
                <option value="3">Annulé</option>
            </select>
        </div>
        <input type="submit" value="Modifier">
        </form></div></div></div></div>
        
VIEW;
    }
    
}
?>
