<?php
require_once '../helpers/cors.php';
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Méthode non autorisée."]);
    exit();
}

function getStatut($solde) {
    if ($solde < 1000)                        return "insuffisant";
    elseif ($solde >= 1000 && $solde <= 5000) return "moyen";
    else                                      return "élevé";
}

$data  = json_decode(file_get_contents("php://input"), true);
$nom   = isset($data['nom'])   ? trim($data['nom'])       : '';
$solde = isset($data['solde']) ? floatval($data['solde']) : null;

if (empty($nom)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Le nom du client est requis."]);
    exit();
}

if ($solde === null || !is_numeric($data['solde'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Le solde est requis et doit être un nombre."]);
    exit();
}

$database = new Database();
$db       = $database->getConnection();

$stmt = $db->prepare("INSERT INTO client_bancaire (nom, solde) VALUES (:nom, :solde)");
$stmt->bindValue(':nom',   $nom,   PDO::PARAM_STR);
$stmt->bindValue(':solde', $solde);

if ($stmt->execute()) {
    $newId  = $db->lastInsertId();
    $statut = getStatut($solde);
    http_response_code(201);
    echo json_encode([
        "success"   => true,
        "message"   => "Client ajouté avec succès.",
        "numCompte" => $newId,
        "statut"    => $statut
    ]);
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur lors de l'ajout du client."]);
}
?>
