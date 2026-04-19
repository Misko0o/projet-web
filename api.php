<?php
session_start();

// inclure les modeles et le controleur
require_once('modeles/utilisateurs.inc');
require_once('modeles/evenements.inc');

// indiquer au navigateur que la reponse est du JSON
header('Content-Type: application/json; charset=utf-8');


/**
 * Traiter une demande d'inscription
 */
function traiterInscription() {
    //on recupere les donnees du formulaire
    $pseudo = '';
    if(isset($_POST['pseudo'])) {
        $pseudo = trim($_POST['pseudo']);
    }

    $mot_de_passe = '';
    if(isset($_POST['mot_de_passe'])) {
        $mot_de_passe = trim($_POST['mot_de_passe']);
    }

    $confirmation = '';
    if(isset($_POST['confirmation'])) {
        $confirmation = trim($_POST['confirmation']);
    }

    //verifications des conditions
    if (strlen($pseudo) < 4) {
        echo json_encode(['succes' => false, 'erreur' => 'Le pseudo doit faire au moins 4 caracteres']);
        exit;
    }
    if (strlen($mot_de_passe) < 6) {
        echo json_encode(['succes' => false, 'erreur' => 'Le mot de passe doit faire au moins 6 caracteres']);
        exit;
    }
    if ($mot_de_passe !== $confirmation) {
        echo json_encode(['succes' => false, 'erreur' => 'Les mots de passe ne correspondent pas']);
        exit;
    }

    //on verifie que le pseudo n'est pas deja pris
    $utilisateur_existant = chercherUtilisateurParPseudo($pseudo);
    if ($utilisateur_existant !== null) {  
        echo json_encode(['succes' => false, 'erreur' => 'Ce pseudo est deja pris']);
        exit;
    }

    //on cree l'utilisateur
    $nouvel_utilisateur = creerUtilisateur($pseudo, $mot_de_passe);

    //on le connecte
    $_SESSION['id_utilisateur'] = $nouvel_utilisateur['id'];
    $_SESSION['pseudo'] = $nouvel_utilisateur['pseudo'];
    $_SESSION['est_invite'] = false;

    echo json_encode(['succes' => true, 'pseudo' => $nouvel_utilisateur['pseudo']]);
    exit;
}

/**
 * Traiter une demande de connexion
 */
function traiterConnexion() {
    $pseudo = '';
    if(isset($_POST['pseudo'])) {
        $pseudo = trim($_POST['pseudo']);
    }

    $mot_de_passe = '';
    if(isset($_POST['mot_de_passe'])) {
        $mot_de_passe = $_POST['mot_de_passe'];
    }

    if (strlen($pseudo)==0 || strlen($mot_de_passe)==0) {
        echo json_encode(['succes' => false, 'erreur' => 'Veuillez remplir tous les champs']);
        exit;
    }

    //on cherche l'utilisateur dans le fichier json
    $utilisateur = chercherUtilisateurParPseudo($pseudo);

    if ($utilisateur === null) {
        echo json_encode(['succes' => false, 'erreur' => 'Utilisateur introuvable']);
        exit;
    }

    //on verifie le mot de passe
    if ($mot_de_passe !== $utilisateur['mot_de_passe']) {
        echo json_encode(['succes' => false, 'erreur' => 'Mot de passe incorrect']);
        exit;
    }

    $_SESSION['id_utilisateur'] = $utilisateur['id'];
    $_SESSION['pseudo'] = $utilisateur['pseudo'];
    $_SESSION['est_invite'] = false;

    echo json_encode(['succes' => true, 'pseudo' => $utilisateur['pseudo']]);
    exit;
}

/**
 * Traiter une connexion en mode invite (sans compte)
 */
function traiterInvite() {
    $pseudo_invite = 'Invite_'.substr((string)random_int(1000,9999),3); //un pseudo dont les dernierschiffre sont aleatoires 

    $_SESSION['id_utilisateur'] = 'invite_' . session_id();
    $_SESSION['pseudo'] = $pseudo_invite;
    $_SESSION['est_invite'] = true;

    echo json_encode(['succes' => true, 'pseudo' => $pseudo_invite]);
    exit;
}

?>