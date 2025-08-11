<?php
// Si le server utilise la methode post pour lancer sa requête
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // alors il affiche "";
    echo "Formulaire envoyé en post";
// Sinon si le server utilise la methode get pour lancer sa requête
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // alors il affiche "";
    echo "Formulaire envoyé en get";
}

// je récupère les données envoyées par le formulaire quand l'utilisateur le remplit
// et envoie ses infos. On les récupere pour les afficher à l'utilisateur(ex: confirmation d'inscription)
// les enregistrer dans une base de données
// Les traiter( exemple: evois d'emails, calculs, recherches)
$nom = $_POST['nom'] ?? '';
$email = $_POST['email'] ?? '';
// "Si $_POST['nom'] existe et qu'il n'est pas nul, remplis le dans $nom.
// Sinon, mets un vide ('');
$message = $_POST['message'] ?? '';

// On affiche tout le contenu du tableau super global $_POST, c'est à dire toutes les données 
// envoyées par la methode POST dans le formulaire
var_dump($_POST);

?>




<!DOCTYPE html>
<html lang="fr">

<head>
    <link rel="stylesheet" href="assets/style/style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire de contact premier exo vacances</title>
</head>

<body>
    <section>
        <form action="contact.php" method='post'>
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="message">Message</label>
            <textarea name="message" id="message" required>
        </textarea>

            <button type="submit">Envoyer</button>

        </form>
    </section>
</body>

</html>