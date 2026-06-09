<?php
require_once '../helpers/cors.php';
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Méthode non autorisée."]);
    exit();
}

function getStatut($solde) {
    if ($solde < 1000)                        return "insuffisant";
    elseif ($solde >= 1000 && $solde <= 5000) return "moyen";
    else                                      return "élevé";
}

$data      = json_decode(file_get_contents("php://input"), true);
$numCompte = isset($data['numCompte']) ? intval($data['numCompte'])  : 0;
$nom       = isset($data['nom'])       ? trim($data['nom'])          : '';
$solde     = isset($data['solde'])     ? floatval($data['solde'])    : null;

if ($numCompte <= 0) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Numéro de compte invalide."]);
    exit();
}

if (empty($nom)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Le nom est requis."]);
    exit();
}

if ($solde === null || !isset($data['solde']) || !is_numeric($data['solde'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Le solde est requis et doit être un nombre."]);
    exit();
}

$database = new Database();
$db       = $database->getConnection();

$check = $db->prepare("SELECT numCompte FROM client_bancaire WHERE numCompte = :numCompte");
$check->bindValue(':numCompte', $numCompte, PDO::PARAM_INT);
$check->execute();

if (!$check->fetch()) {
    http_response_code(404);
    echo json_encode(["success" => false, "message" => "Client introuvable."]);
    exit();
}

$stmt = $db->prepare("UPDATE client_bancaire SET nom = :nom, solde = :solde WHERE numCompte = :numCompte");
$stmt->bindValue(':nom',       $nom,       PDO::PARAM_STR);
$stmt->bindValue(':solde',     $solde);
$stmt->bindValue(':numCompte', $numCompte, PDO::PARAM_INT);

try {
    $stmt->execute();
    $statut = getStatut($solde);
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "message" => "Client mis à jour avec succès.",
        "statut"  => $statut
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur SQL : " . $e->getMessage()]);
}
?>
