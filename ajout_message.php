<?php
// J'inclus mon fichier de config qui contient la fonction dbConnexion() (retourne un objet PDO).
// __DIR__ garantit un chemin absolu fiable, peu importe d'où le script est lancé.
require_once __DIR__ . '/config/database.php';

// J'initialise mes variables d'état pour l'affichage des messages.
$success = '';
$errors = [];

// J'initialise aussi les champs du formulaire, pour éviter les notices au premier affichage (GET).
$nom = '';
$email = '';
$message = '';

// Je vérifie que la page a été appelée via un envoi de formulaire en méthode POST.
// $_SERVER est une superglobale ; REQUEST_METHOD me dit si c’est "POST" ou "GET".
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Je récupère les champs depuis la superglobale $_POST.
    // L'opérateur ?? évite l'erreur si l'index n'existe pas ; trim() enlève les espaces autour.
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // --- VALIDATIONS SIMPLES ---

    // Si le nom est vide, j'ajoute une erreur.
    if ($nom === '') {
        $errors[] = "Le nom est obligatoire.";
    }

    // Je vérifie l'email : non vide + format valide avec filter_var().
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email invalide.";
    }

    // Le message ne doit pas être vide non plus.
    if ($message === '') {
        $errors[] = "Le message est obligatoire.";
    }

    // Si AUCUNE erreur (tableau vide), je peux insérer en base.
    // "!$errors" veut dire : "le tableau $errors est vide".
    if (!$errors) {
        try {
            // J'ouvre une connexion à la base via ma fonction utilitaire (définie dans database.php).
            // Elle me renvoie un objet PDO prêt à l'emploi.
            $pdo = dbConnexion();

            // J'écris ma requête d'insertion avec des placeholders (?) pour préparer la requête.
            $sql = "INSERT INTO messages (nom,email,message) VALUES (?,?,?)";

            // Je prépare la requête (PDO renvoie un statement).
            $stmt = $pdo->prepare($sql);

            // J'exécute la requête en passant les valeurs dans l'ordre des ?.
            $stmt->execute([$nom, $email, $message]);

            // Si tout marche, je stocke un message de succès.
            $success = "Message ajouté avec succès !";

            // Je vide les champs pour nettoyer le formulaire après insertion.
            $nom = $email = $message = '';
        } catch (PDOException $e) {
            // Si une erreur SQL arrive, je la récupère et je l'ajoute dans mes erreurs.
            $errors[] = "ERREUR SQL : " . $e->getMessage();
        }
    }
}
?>
<!-- Superglobales : $_SERVER, $_POST (toujours dispos partout). -->
<!-- Variables : $success, $errors, $nom, $email, $message, $pdo, $sql, $stmt. -->
<!-- Fonctions : dbConnexion() (à toi), trim(), filter_var(). -->
<!-- PDO : objet de connexion DB ; prepare() crée une requête préparée ; execute() l’exécute. -->
<!-- try/catch : j’essaie d’exécuter le code “risqué” (DB). Si ça casse, je passe dans catch (PDOException $e). -->

<!DOCTYPE html>
<html lang="fr">

<head>
    <!-- J'indique l'encodage pour éviter les soucis d'accents. -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Ajouter un message</title>

    <!-- Un peu de style minimal pour que ce soit lisible sans framework. -->
    <style>
        body {
            font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
            margin: 2rem;
            line-height: 1.5;
        }

        .container {
            max-width: 720px;
            margin: 0 auto;
        }

        h1 {
            margin-bottom: 1rem;
        }

        .alert {
            padding: .75rem 1rem;
            border-radius: .5rem;
            margin-bottom: 1rem;
        }

        .alert-success {
            background: #e8f7ee;
            border: 1px solid #c7ebd4;
        }

        .alert-error {
            background: #ffefef;
            border: 1px solid #ffd0d0;
        }

        form label {
            font-weight: 600;
            display: inline-block;
            margin-top: .75rem;
        }

        input[type="text"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: .6rem .7rem;
            border: 1px solid #ccc;
            border-radius: .5rem;
        }

        button[type="submit"] {
            margin-top: 1rem;
            padding: .6rem 1rem;
            border: 0;
            border-radius: .5rem;
            cursor: pointer;
            background: #111827;
            color: white;
        }

        ul {
            margin: .5rem 0 0 1.2rem;
        }

        a {
            color: #0b63f6;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Mon titre principal -->
        <h1>Ajouter un message</h1>

        <!-- Si j'ai un message de succès à afficher -->
        <?php if ($success): ?>
            <div class="alert alert-success">
                <!-- htmlspecialchars empêche l'injection de HTML/JS dans l'affichage -->
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <!-- Si j'ai des erreurs, je les liste toutes pour aider l'utilisateur -->
        <?php if ($errors): ?>
            <div class="alert alert-error">
                <strong>Veuillez corriger les erreurs suivantes :</strong>
                <ul>
                    <?php foreach ($errors as $e): ?>
                        <li><?php echo htmlspecialchars($e); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Mon formulaire. method="post" -> données dans $_POST ; action vide -> soumet sur cette même page -->
        <form method="post" action="">
            <!-- Champ NOM -->
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($nom); ?>" <!-- Je réaffiche ce
                que l'utilisateur a saisi -->
            required
            />

            <!-- Champ EMAIL -->
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" <!-- Idem, je
                garde la saisie -->
            required
            />

            <!-- Champ MESSAGE -->
            <label for="message">Message</label>
            <textarea id="message" name="message" rows="5" required><?php echo htmlspecialchars($message); ?></textarea>

            <!-- Bouton d'envoi -->
            <button type="submit">Enregistrer</button>
        </form>

        <!-- Un petit lien pratique vers la page de listing -->
        <p style="margin-top:1rem;">
            <a href="liste_messages.php">Voir la liste des messages</a>
        </p>

        <!-- Astuce dev : je peux afficher rapidement ce que je reçois (à commenter/retirer en prod) -->
        <?php /* echo '<pre>'; var_dump($_POST, $errors, $success); echo '</pre>'; */ ?>
    </div>
</body>

</html>