<!-- Fichier de connexion PDO -->

<?php
// Je declare la fonction dbConnexion qui va nous donner une connexion à la base de données
// PDO indique que la fonction retournera un  objet de type PDO
//PDO est une sorte de prise universelle qui permet en php de se brancher à une base de données
//et à lui envoyer des requetes , on peut l'imaginer comme un cable qui relie mon code PHP à MySQL 
//ou d'autres bases pour discuter avec elle.
function dbConnexion(): PDO
{
    $host = 'localhost'; // adresse du serveur Mysql
    $dbname = 'test_php'; // le nom de ma base de données
    $user = 'root'; // l'utilisateur mySQL qui enregistre ses données via le formulaire
    $pass = ''; // le mode de passe , vide ici ('')
// DSN = "carte d’identité" de la connexion pour PDO (type + hôte + base + encodage)
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";


    // On essaie d’ouvrir la connexion à MySQL.
    // La quatrieme varialbe est un tableau d’options :
    try { // new PDO(...) crée l’objet connexion.
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            // Quand on fait un fetch(), on récupère des tableaux associatifs (clés = noms de colonnes)
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        // Si tout s’est bien passé, on renvoie l’objet $pdo au code qui a appelé la fonction.
        return $pdo;
    } catch (PDOException $e) {
        // Si la connexion échoue, on PASSE ICI.
        // $e contient l’erreur. On arrête le script avec un message.
        die("Erreur connexion DB : " . $e->getMessage());
    }
    // Fin du try/catch et de la fonction

}

?>