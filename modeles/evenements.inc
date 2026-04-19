<?php
//chemin vers le fichier des evenements
$chemin_evenements = __DIR__ . '/../data/evenements_eurovision.json';

/**
 * Lire tous les evenements
 */
function lireEvenements() {
    global $chemin_evenements;
    $contenu = file_get_contents($chemin_evenements);
    $evenements = json_decode($contenu, true);
    if (!is_array($evenements)) {
        $evenements = [];
    }
    return $evenements;
}

/**
 * On mélange les evenements puis on les redistribue dans acc
 */
function tirerAleatoires($nombre) {
    $evenements = lireEvenements();
    shuffle($evenements);

    $acc = [];
    for ($i = 0; $i < $nombre && $i < count($evenements); $i++) {
        $acc[] = $evenements[$i];
    }

    return $acc;
}
?>
