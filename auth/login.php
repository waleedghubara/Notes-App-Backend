<?php
include "../connext.php";

$data = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST)) {
        $data = $_POST;
    } else {
        $json = file_get_contents("php://input");
        $data = json_decode($json, true) ?? [];
    }
}

$email    = filterData($data["email"] ?? '');
$password = filterData($data["password"] ?? '');

$stmt = $connect->prepare("SELECT * FROM `users` WHERE `email` = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password'])) {
    $token = hash("sha512", bin2hex(random_bytes(64)) . uniqid("", true) . time());

    $update = $connect->prepare("UPDATE `users` SET `token` = ? WHERE `id` = ?");
    $update->execute([$token, $user['id']]);

    echo json_encode([
        "status"  => "success",
        "message" => "login successfully",
        "token"   => $token,
        "id"      => $user['id'],
        "data"    => $user
    ], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode([
        "status"  => "error",
        "message" => "Invalid email or password"
    ], JSON_UNESCAPED_UNICODE);
}
