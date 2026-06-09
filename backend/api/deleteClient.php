<?php
require_once '../helpers/cors.php';
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Méthode non autorisée."]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);


$numCompte = 0;
if (isset($data['numCompte'])) {
    $numCompte = intval($data['numCompte']);
} elseif (isset($_GET['numCompte'])) {
    $numCompte = intval($_GET['numCompte']);
}

if ($numCompte <= 0) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Numéro de compte invalide."]);
    exit();
}

$database = new Database();
$db       = $database->getConnection();


$check = $db->prepare("SELECT numCompte FROM client_bancaire WHERE numCompte = :numCompte");
$check->bindParam(':numCompte', $numCompte);
$check->execute();

if (!$check->fetch()) {
    http_response_code(404);
    echo json_encode(["success" => false, "message" => "Client introuvable."]);
    exit();
}

$stmt = $db->prepare("DELETE FROM client_bancaire WHERE numCompte = :numCompte");
$stmt->bindParam(':numCompte', $numCompte);

if ($stmt->execute()) {
    http_response_code(200);
    echo json_encode(["success" => true, "message" => "Client supprimé avec succès."]);
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur lors de la suppression."]);
}
?>
