<?php

// chemin vers le fichier des utilisateurs
$chemin_utilisateurs = __DIR__.'/../data/utilisateurs.json';

/**
 * Retourne un tableau de tous les utilisateurs
 */
function lireUtilisateurs() {
    global $chemin_utilisateurs;
    $contenu = file_get_contents($chemin_utilisateurs); 
    $utilisateurs = json_decode($contenu, true);
    if (!is_array($utilisateurs)) {
        $utilisateurs = [];
    }
    return $utilisateurs;
}

/**
 * Sauvegarde un utilisateurs dans le fichier json
 */
function sauvegarderUtilisateurs($utilisateurs) {
    global $chemin_utilisateurs;
    $json = json_encode($utilisateurs, JSON_PRETTY_PRINT);
    file_put_contents($chemin_utilisateurs, $json);
}

/**
 * Cherche un utilisateur selon son pseudo
 * Renvoie l'utilisateur trouve (si il existe) ou null (sinon)
 */
function chercherUtilisateurParPseudo($pseudo) {
    $utilisateurs = lireUtilisateurs();
    foreach ($utilisateurs as $utilisateur) {
        if (strtolower($utilisateur['pseudo']) === strtolower($pseudo)) {
            return $utilisateur;
        }
    }
    return null;
}


/**
 * Creer un nouvel utilisateur et le sauvegarder
 * Retourne le nouvel utilisateur cree
 */
function creerUtilisateur($pseudo, $mot_de_passe) {
    $utilisateurs = lireUtilisateurs();
    $id = count($utilisateurs)+1;
    $nouvel_utilisateur = [
        'id' => $id,
        'pseudo' => $pseudo,
        'mot_de_passe' => password_hash($mot_de_passe, PASSWORD_DEFAULT), //on cree un mot de passe crypte a l'aide d'une fonction externe
        'parties_jouees' => 0,
        'parties_gagnees' => 0,
        'score_total' => 0,
        'date_inscription' => date('Y-m-d H:i:s')
    ];

    $utilisateurs[] = $nouvel_utilisateur;
    sauvegarderUtilisateurs($utilisateurs);

    return $nouvel_utilisateur;
}

/**
 * Met a jour les stat d'un joueur apres une partie
 */
function mettreAJourStats($id_utilisateur, $score, $a_gagne) {
    $utilisateurs = lireUtilisateurs();
    foreach ($utilisateurs as $clef => $utilisateur) {
        if ($utilisateur['id'] === $id_utilisateur) {
            $utilisateurs[$clef]['parties_jouees'] = $utilisateur['parties_jouees'] + 1; //le nombre de partie 
            $utilisateurs[$clef]['score_total'] = $utilisateur['score_total'] + $score; //le score
            if ($a_gagne) {
                $utilisateurs[$clef]['parties_gagnees'] = $utilisateur['parties_gagnees'] + 1;
            }
            break;
        }
    }
    sauvegarderUtilisateurs($utilisateurs);
}
?>