<?php

// Ce script supprime un message de la base de données via son ID envoyé en POST.

// 1J'inclus le fichier de configuration qui contient la fonction dbConnexion().
require __DIR__ . '/config/database.php';

//  Je vérifie que le script est appelé en méthode POST.
// Cela évite qu'on accède à cette page directement en tapant l'URL dans le navigateur.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Redirection vers la liste des messages si on arrive ici autrement qu'en POST.
    header('Location: liste_messages.php');
    exit;
}

//  Je récupère l'ID depuis la superglobale $_POST.
// Je force le typage en entier pour éviter toute injection (ex : SQL ou XSS).
$id = (int)($_POST['id'] ?? 0);

//  Si l'ID est invalide (0 ou vide), je redirige.
if ($id <= 0) {
    header('Location: liste_messages.php');
    exit;
}

try {
    //  Connexion à la base.
    $pdo = dbConnexion();

    //  Préparation de la requête DELETE.
    // Les requêtes préparées évitent les injections SQL.
    $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");

    //  Exécution de la requête avec l'ID en paramètre.
    $stmt->execute([$id]);

} catch (PDOException $e) {
    //  En cas d'erreur SQL, on peut stocker le message pour debug (à éviter en prod).
    // Pour l'instant, on affiche juste un message simple.
    die("Erreur lors de la suppression : " . $e->getMessage());
}

// Redirection vers la liste après suppression.
header('Location: liste_messages.php');
exit;
