<?php
require_once '../helpers/cors.php';
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Méthode non autorisée."]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

$login    = isset($data['login'])    ? trim($data['login'])    : '';
$password = isset($data['password']) ? trim($data['password']) : '';

if (empty($login) || empty($password)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Login et mot de passe requis."]);
    exit();
}

$database = new Database();
$db       = $database->getConnection();

$stmt = $db->prepare("SELECT id, login FROM utilisateurs WHERE login = :login AND password = MD5(:password)");
$stmt->bindParam(':login',    $login);
$stmt->bindParam(':password', $password);
$stmt->execute();

$user = $stmt->fetch();

if ($user) {
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "message" => "Connexion réussie.",
        "user"    => ["id" => $user['id'], "login" => $user['login']]
    ]);
} else {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Identifiants incorrects."]);
}
?>
