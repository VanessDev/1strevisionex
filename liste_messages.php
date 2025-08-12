<?php
// J'inclus mon fichier de config où se trouve ma fonction dbConnexion() (qui renvoie un objet PDO prêt à l'emploi).
require __DIR__ . '/config/database.php';

// J'ouvre une connexion à ma base grâce à ma fonction utilitaire.
$pdo = dbConnexion();

// Je fais une requête SQL pour récupérer toutes les colonnes utiles de la table messages,
// classées par date d'envoi décroissante (les plus récentes en premier).
// query() suffit ici car il n'y a pas de variables utilisateur, donc aucun risque d'injection.
// fetchAll() me renvoie toutes les lignes sous forme de tableau associatif.
$rows = $pdo->query("SELECT id, nom, email, message, date_envoi FROM messages ORDER BY date_envoi DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Liste des messages</title>

    <!-- Un petit style pour rendre le tableau plus joli et plus lisible -->
    <style>
        body {
            font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
            margin: 2rem;
        }

        h1 {
            margin-bottom: 1rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: .6rem .7rem;
            vertical-align: top;
        }

        th {
            background: #f6f7f9;
            text-align: left;
        }

        tbody tr:nth-child(odd) {
            background: #fafafa;
        }

        .empty {
            padding: 1rem;
            text-align: center;
            color: #666;
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
    <!-- Titre principal de la page -->
    <h1>Messages</h1>

    <!-- Début du tableau pour afficher les messages -->
    <table>
        <thead>
            <tr>
                <!-- Les titres des colonnes -->
                <th style="width:60px;">ID</th>
                <th style="width:180px;">Nom</th>
                <th style="width:220px;">Email</th>
                <th>Message</th>
                <th style="width:200px;">Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!$rows): ?>
                <!-- Si aucun message dans la base, j'affiche une ligne vide avec un message -->
                <tr>
                    <td colspan="5" class="empty">Aucun message pour le moment.</td>
                </tr>
            <?php else: ?>
                <!-- Je boucle sur chaque ligne de résultat -->
                <?php foreach ($rows as $r): ?>
                    <tr>
                        <!-- ID : entier donc je caste en (int) pour éviter toute injection -->
                        <td><?php echo (int) $r['id']; ?></td>

                        <!-- Nom : j'affiche en protégeant contre le HTML/JS -->
                        <td><?php echo htmlspecialchars($r['nom']); ?></td>

                        <!-- Email : idem, j'échappe pour éviter l'injection -->
                        <td><?php echo htmlspecialchars($r['email']); ?></td>

                        <!-- Message : j'échappe et je convertis les retours à la ligne en <br> pour le formatage -->
                        <td><?php echo nl2br(htmlspecialchars($r['message'])); ?></td>

                        <!-- Date : je la reformate en jour/mois/année heure:minute si possible -->
                        <td>
                            <?php
                            // strtotime() transforme la date SQL en timestamp
                            $ts = strtotime($r['date_envoi']);
                            // Si la conversion marche, je l'affiche formatée, sinon je montre la valeur brute.
                            echo $ts ? date('d/m/Y H:i', $ts) : htmlspecialchars($r['date_envoi']);
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Un lien pour revenir à la page d'ajout de message -->
    <p style="margin-top:1rem;"><a href="ajouter_message.php">Ajouter un message</a></p>
</body>

</html>