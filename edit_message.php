<?php

// Page d’édition d’un message existant : charge la ligne par son ID, affiche un formulaire pré-rempli,
// valide les données et met à jour en base (UPDATE).

// 1) J'inclus la config pour utiliser dbConnexion() (retourne un objet PDO prêt).
require __DIR__ . '/config/database.php';

// 2) J’ouvre la connexion PDO.
$pdo = dbConnexion();

// 3) Variables d’état pour l’interface.
$errors = [];     // Tableau des erreurs de validation/SQL à afficher au-dessus du formulaire
$success = '';     // Message de succès après mise à jour

// 4) Je récupère l'ID depuis l'URL (méthode GET) et je le force en entier.
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// 5) Si l'ID est invalide, je stoppe proprement.
if ($id <= 0) {
    http_response_code(400); // Mauvaise requête
    exit('ID invalide');
}

// 6) Je charge la ligne existante pour pré-remplir le formulaire.
$stmt = $pdo->prepare("SELECT id, nom, email, message FROM messages WHERE id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch();

// 7) Si aucune ligne trouvée, je renvoie une 404.
if (!$row) {
    http_response_code(404);
    exit('Message introuvable');
}

// 8) Je pré-remplis les champs avec les valeurs actuelles (avant soumission POST).
$nom = $row['nom'];
$email = $row['email'];
$message = $row['message'];

// 9) Si le formulaire est soumis en POST, je récupère, valide et mets à jour.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 9.1) Récupération + nettoyage des champs
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // 9.2) Validations de base
    if ($nom === '') {
        $errors[] = "Le nom est obligatoire.";
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email invalide.";
    }
    if ($message === '') {
        $errors[] = "Le message est obligatoire.";
    } elseif (mb_strlen($message) < 5) {
        $errors[] = "Le message doit faire au moins 5 caractères.";
    }

    // 9.3) Unicité de l'email (exclure la ligne courante)
    if (!$errors) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM messages WHERE email = ? AND id <> ?");
        $stmt->execute([$email, $id]);
        if ((int) $stmt->fetchColumn() > 0) {
            $errors[] = "Cet email est déjà utilisé par un autre message.";
        }
    }

    // 9.4) UPDATE si tout est OK
    if (!$errors) {
        try {
            $stmt = $pdo->prepare("UPDATE messages SET nom = ?, email = ?, message = ? WHERE id = ?");
            $stmt->execute([$nom, $email, $message, $id]);

            // Message de succès (tu peux aussi rediriger pour éviter le resoumis F5)
            $success = "Message mis à jour avec succès !";

            // 👉 Option (décommenter pour rediriger) :
            // header('Location: liste_messages.php?msg=' . urlencode('Message mis à jour')); exit;

        } catch (PDOException $e) {
            $errors[] = "Erreur SQL (update) : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <title>Modifier le message #<?php echo (int) $id; ?></title>
    <style>
        body {
            font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
            margin: 2rem;
            line-height: 1.5
        }

        .container {
            max-width: 720px;
            margin: 0 auto
        }

        .alert {
            padding: .75rem 1rem;
            border-radius: .5rem;
            margin-bottom: 1rem
        }

        .alert-success {
            background: #e8f7ee;
            border: 1px solid #c7ebd4
        }

        .alert-error {
            background: #ffefef;
            border: 1px solid #ffd0d0
        }

        label {
            display: inline-block;
            margin-top: .75rem;
            font-weight: 600
        }

        input[type="text"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: .6rem .7rem;
            border: 1px solid #ccc;
            border-radius: .5rem
        }

        button {
            margin-top: 1rem;
            padding: .6rem 1rem;
            border: 0;
            border-radius: .5rem;
            cursor: pointer;
            background: #111827;
            color: #fff
        }

        a {
            color: #0b63f6;
            text-decoration: none
        }

        a:hover {
            text-decoration: underline
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Modifier le message #<?php echo (int) $id; ?></h1>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <?php if ($errors): ?>
            <div class="alert alert-error">
                <strong>Veuillez corriger :</strong>
                <ul>
                    <?php foreach ($errors as $e): ?>
                        <li><?php echo htmlspecialchars($e); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Formulaire pré-rempli -->
        <form method="post" action="">
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($nom); ?>" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>

            <label for="message">Message</label>
            <textarea id="message" name="message" rows="5" required><?php echo htmlspecialchars($message); ?></textarea>

            <button type="submit">Enregistrer</button>
        </form>

        <p style="margin-top:1rem;"><a href="liste_messages.php">← Retour à la liste</a></p>
    </div>
</body>

</html>