<?php
include_once 'Header.php';

class InfoMatView{
    
    //Déclaration de variable
    private $mat;
    private $rdvs = [];
    private $pagemessage;
    
    public function setmessage($msg){
        $this->pagemessage = $msg;
    }
    
    public function setMat($mat){
        $this->mat = $mat;
    }
    
    public function setRdvs($rdvs){
        $this->rdvs = $rdvs;
    }
    
    //Launch
    public function launch(){
        $header = new Header("Info matelas");
        $header->launch();
        
        $id = $this->mat['ID'];
        $name = $this->mat['NAME'];
        $type = $this->mat['TYPE'];
        $state = $this->mat['STATE'];
        if($this->mat['URGENT']==0){
            $urgent = 'Non';
        }
        else{
            $urgent = 'Oui';
        }
        
        echo '
        <div class="container p-3"><h4>Information sur le matelas</h4>
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
        <th class="table-info">Type</th>
        <td>
        <div class="row">
        <div class="col text-center">'.$type.'</div>
        <div class="col text-right">
        <a class="nav-link ml-1 mr-1" data-target="#Type_Modify" href="#Type_Modify" data-toggle="modal">Modifier</a>
        </div></div></td></tr>
            
        <tr>
        <th class="table-info">Etat</th>
        <td>
        <div class="row">
        <div class="col text-center">'.$state.'</div>
        <div class="col text-right">
        <a class="nav-link ml-1 mr-1" data-target="#State_Modify" href="#StateModify" data-toggle="modal">Modifier</a>
        </div></div></td></tr>
            
        <tr>
        <th class="table-info">Urgent</th>
        <td>
        <div class="row">
        <div class="col text-center">'.$urgent.'</div>
        <div class="col text-right">
        <a class="nav-link ml-1 mr-1" data-target="#Urgent_Modify" href="#Urgent_Modify" data-toggle="modal">Modifier</a>
        </div></div></td></tr>
            
        </table>
        </thead>
            
        <div class="container-fluid text-right"><a data-target="#Delete" href="#Delete" data-toggle="modal" class="badge badge-danger align-right">Supprimer</a></div>';
        
        if(sizeof($this->rdvs)!=0){
            $_SESSION['RES'] = $this->rdvs;
            $cpt = 0;
            echo '
                <h5>Traitements associés :</h5>
                <table class="table table-dark table-responsive-lg mt-1">
                <thead class="bg-primary">
                    <tr>
                        <th scope="col" class="h5">Nom</th>
                        <th scope="col" class="h5">Prénom</th>
                        <th scope="col" class="h5">NIP</th>
                        <th scope="col" class="h5">Date SCN</th>
                        <th scope="col" class="h5">Date FTR</th>
                        <th scope="col" class="h5">Etat</th>
                        <th scope="col" class="h5">Plus d\'infos</th>
                    </tr>
                </thead>
                <tbody>';
            foreach($this->rdvs as $rdv){
                $rname = $rdv['NAME'];
                $surname = $rdv['SURNAME'];
                $nip = $rdv['NIP'];
                
                $raw_scanner = $rdv['DATE_SCANNER'];
                $format = new DateTime($raw_scanner);
                $date_scanner = $format->format('d\/m\/y');
                
                $raw_ftr = $rdv['DATE_FTR'];
                $format = new DateTime($raw_ftr);
                $date_ftr = $format->format('d\/m\/y');
                
                $rstate = $rdv['STATE'];
                // find current state
                $today = date('Y-m-d');
                if($rstate == 'Normal'){
                    if($raw_scanner < $today && $raw_ftr < $today){
                        $rstate = 'Terminé';
                    }
                    elseif($raw_scanner <= $today && $raw_ftr > $today){
                        $rstate = 'En cours';
                    }
                    else{
                        $rstate = 'A venir';
                    }
                }
                echo <<<VIEW
                <tr>
                    <td>$rname</td>
                    <td>$surname</td>
                    <td>$nip</td>
                    <td>$date_scanner</td>
                    <td>$date_ftr</td>
                    <td>$rstate</td>
                    <td class="Infos">
                        <button type="button" class="btn btn-outline-light" onclick="$('#Main_Form_TASK').val('InfoPatRDV');
                                                                                     $('#Rdv_Id').val($cpt);
                                                                                     $('#Main_Form').submit();">
                            <i class="fas fa-plus"></i>
                        </button>
                    </td>
                </tr>
VIEW;
                $cpt++;
            }
            echo '</tbody>';
        }
        else{
            echo '<h6>Aucun traitement associé</h6>';
        }
    
        echo <<<VIEW
        <!-- Modal Delete -->
        <div class="modal fade" id="Delete" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Supprimer le matelas ?</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        Le matelas sera supprimé définitivement de la base de données.
                        <form action="index.php" method="POST">
                            <input type="hidden" name="TASK" value="DelMat">
                            <input type="hidden" name="ID_MATTRESS" value=$id>
                            <div class="modal-footer">
                                <input class="btn btn-danger" type="submit" value="Supprimer">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
                
        <!-- Modal Modify Name -->
        <div class="modal fade" id="Name_Modify" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Modifier le nom</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <form action="index.php" method="POST">
                            <input type="hidden" name="TASK" value="InfoMat">
                            <input type="hidden" name="ID_MATTRESS" value=$id>
                            <div class="form-group">
                                <label>Nom actuel</label>
                                <input type="text" name="NAME" class="form-control" placeholder="$name" disabled>
                            </div><div class="form-group">
                                <label>Nouveau nom</label>
                                <input type="text" name="NEW_NAME" class="form-control" required>
                            </div>
                            <input type="submit" value="Modifier">
                        </form>
                    </div>
                </div>
            </div>
        </div>
                
        <!-- Modal Modify Type -->
        <div class="modal fade" id="Type_Modify" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Modifier le type</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
                    <div class="modal-body">
                    <form action="index.php" method="POST">
                        <input type="hidden" name="TASK" value="InfoMat">
                        <input type="hidden" name="ID_MATTRESS" value=$id>
                        <div class="form-group">
                            <label>Type actuel</label>
                            <input type="text" name="TYPE" class="form-control" placeholder="$type" disabled>
                        </div>
                        <div class="form-group">
                            <label for="NEW_TYPE">Nouveau type</label>
                            <select name="NEW_TYPE" class="form-control">
                                <option value="-1">--Type--</option>
                                <option value="1">Crane</option>
                                <option value="2">Thorax</option>
                                <option value="3">Abdomen</option>
                            </select>
                        </div>
                        <input type="submit" value="Modifier">
                    </form>
                    </div>
                </div>
            </div>
        </div>
                
        <!-- Modal Modify State -->
        <div class="modal fade" id="State_Modify" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title">Modifier l'état</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
                    <div class="modal-body">
                    <form action="index.php" method="POST">
                        <input type="hidden" name="TASK" value="InfoMat">
                        <input type="hidden" name="ID_MATTRESS" value=$id>
                        <div class="form-group">
                            <label>Etat actuel</label>
                            <input type="text" name="STATE" class="form-control" placeholder="$state" disabled>
                        </div>
                        <div class="form-group">
                            <label for="NEW_STATE">Nouvel état</label>
                            <select name="NEW_STATE" class="form-control">
                                <option value="-1">--Etat--</option>
                                <option value="1">Bon</option>
                                <option value="2">A remplacer</option>
                                <option value="3">Ne pas utiliser</option>
                            </select>
                        </div>
                        <input type="submit" value="Modifier">
                    </form>
                    </div>
                </div>
            </div>
        </div>
                
        <!-- Modal Modify Urgent -->
        <div class="modal fade" id="Urgent_Modify" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Modifier l'urgence</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
                    <div class="modal-body">
                        <form action="index.php" method="POST">
                            <input type="hidden" name="TASK" value="InfoMat">
                            <input type="hidden" name="ID_MATTRESS" value=$id>
                            <div class="form-group">
                                <label>Urgence actuelle</label>
                                <input type="text" name="URGENT" class="form-control" placeholder="$urgent" disabled>
                            </div><div class="form-group">
                                <label for="NEW_URGENT">Nouvelle urgence</label>
                                <select name="NEW_URGENT" class="form-control">
                                    <option value="0">Non</option>
                                    <option value="1">Oui</option>
                                </select>
                            </div>
                            <input type="submit" value="Modifier">
                        </form>
                    </div>
                </div>
            </div>
        </div>
VIEW;
        
    }
}
?>