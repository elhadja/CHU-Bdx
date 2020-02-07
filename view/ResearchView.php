<?php

include_once 'Header.php';

class ResearchView
{
    private $cpt;
    private $string;

    public function launch($res){
        // include the header to the view
        $header = new Header("Welcome");
        $header->launch();

        $nbres = sizeof($res);
        $cpt = 0;

        echo '<div class="container pt-3 pr-5 pl-5">';

        $name = $_SESSION['POST']['NAME'];

        if($nbres > 1){
            echo <<<VIEW
            <h5>$nbres résultats trouvés pour "$name"</h5>
VIEW;
        }
        elseif ($nbres == 1) {
            echo <<<VIEW
            <h5>1 résultat trouvé pour "$name"</h5>
VIEW;
        }
        else{
            echo <<<VIEW
            <h5>Aucun résultat trouvé pour "$name"</h5>
VIEW;
        }

        if($nbres > 0){
            echo '
            <table class="table table-dark table-responsive-lg mt-1">
            <thead class="bg-primary">
                <tr>
                    <th scope="col" class="h5">Nom</th>
                    <th scope="col" class="h5">Prénom</th>
                    <th scope="col" class="h5">NIP</th>
                    <th scope="col" class="h5">Date SCN</th>
                    <th scope="col" class="h5">Date FTR</th>
                    <th scope="col" class="h5">Statut</th>
                    <th scope="col" class="h5">Plus d\'infos</th>
                </tr>
            </thead>

            <tbody>';
                foreach($res as $rdv){
                    $name = $rdv['NAME'];
                    $surname = $rdv['SURNAME'];
                    $nip = $rdv['NIP'];
                    
                    $raw_scanner = $rdv['DATE_SCANNER'];
                    $format = new DateTime($raw_scanner);
                    $date_scanner = $format->format('d\/m\/y');
                    
                    $raw_ftr = $rdv['DATE_FTR'];
                    $format = new DateTime($raw_ftr);
                    $date_ftr = $format->format('d\/m\/y');
                    
                    $state = $rdv['STATE'];
                    // find current state
                    $today = date('Y-m-d');
                    if($state == 'Normal'){
                        if($raw_scanner < $today && $raw_ftr < $today){
                            $state = 'Terminé';
                        }
                        elseif($raw_scanner <= $today && $raw_ftr > $today){
                            $state = 'En cours';
                        }
                        else{
                            $state = 'A venir';
                        }
                    }

                    echo <<<VIEW
            <tr>
                <td>$name</td>
                <td>$surname</td>
                <td>$nip</td>
                <td>$date_scanner</td>
                <td>$date_ftr</td>
                <td>$state</td>
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
        }
        echo '</div></body>';
    }
}
?>