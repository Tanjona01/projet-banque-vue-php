<?php
require_once '../helpers/cors.php';
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Méthode non autorisée."]);
    exit();
}

function getStatut($solde) {
    if ($solde < 1000)                      return "insuffisant";
    elseif ($solde >= 1000 && $solde <= 5000) return "moyen";
    else                                    return "élevé";
}

$database = new Database();
$db       = $database->getConnection();

$stmt    = $db->query("SELECT numCompte, nom, solde FROM client_bancaire ORDER BY numCompte ASC");
$clients = $stmt->fetchAll();

foreach ($clients as &$client) {
    $client['statut'] = getStatut(floatval($client['solde']));
}

http_response_code(200);
echo json_encode([
    "success" => true,
    "data"    => $clients
]);
?>
