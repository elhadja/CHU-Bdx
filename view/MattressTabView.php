<?php

class MattressTabView
{
    private $allMattress;
    private $type;
    
    public function addAllMattress($allMattress){
        $this->allMattress = $allMattress;
    }
    
    public function setType($type){
        $this->type = $type;
    }
    
    public function launch(){
        echo '
        <table class="table table-dark mt-1">
        <thead>
            <tr class="bg-primary">
                <th scope="col" class="h5">Matelas</th>
                <th scope="col" class="h5">Type</th>
                <th scope="col" class="h5">Disponibilité</th>
                <th scope="col" class="h5">Etat</th>
            </tr>
        </thead>
        <tbody>';
        foreach($this->allMattress as $mattress){
            $date_dispo = $mattress['DATE_DISPO'];
            if($date_dispo != 'Libre' && $date_dispo != 'HS'){
                $format = new DateTime($date_dispo);
                $date_dispo = $format->format('d\/m\/y');
            }
            $type = $mattress['TYPE'];
            if($this->type == "!" || $this->type == $type){
                $name = $mattress['NAME'];
                if($type == 'HS'){
                    $type = '<span class="text-danger">'.$type.'</span>';
                }
                $dispo = $mattress['DISPO'];
                if($dispo == 0){
                    $dispo = '<span class="text-danger">'.$date_dispo.'</span>';
                }
                else{
                    $dispo = '<span class="text-success">'.$date_dispo.'</span>';
                }
                $state = $mattress['STATE'];
                if($state == 'Ne pas utiliser'){
                    $state = '<span class="text-danger">'.$state.'</span>';
                }
                $id = $mattress['ID'];
                echo <<<VIEW
            <tr>
                <th scope="row"><a href="#" onclick=" $('#Main_Form_TASK').val('InfoMat');
                                                      $('#Mattress_Id').val($id);
                                                      $('#Main_Form').submit(); ">$name</a></th>
                <td>$type</td>
                <td>$dispo</td>
                <td>$state</td>
            </tr>
VIEW;
            }
        }
        echo '</tbody></table>';
    }
}
?>