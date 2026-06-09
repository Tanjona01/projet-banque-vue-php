<?php
require_once '../helpers/cors.php';
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Méthode non autorisée."]);
    exit();
}

$database = new Database();
$db       = $database->getConnection();

$stmt = $db->query("SELECT nom, solde FROM client_bancaire ORDER BY solde DESC");
$clients = $stmt->fetchAll();

$total       = array_sum(array_column($clients, 'solde'));
$nbClients   = count($clients);

http_response_code(200);
echo json_encode([
    "success"   => true,
    "total"     => $total,
    "nbClients" => $nbClients,
    "clients"   => $clients
]);
?>
