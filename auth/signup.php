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

$username = filterData($data["username"] ?? '');
$email    = filterData($data["email"] ?? '');
$phone    = filterData($data["phone"] ?? '');
$age      = filterData($data["age"] ?? '');
$password = filterData($data["password"] ?? '');


$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

$imagename = imageUpload("profile");

$token = hash("sha512", bin2hex(random_bytes(64)) . uniqid("", true) . time());

$stmt = $connect->prepare("INSERT INTO `users`
(`username`, `email`, `phone`, `age`, `password`, `profile`, `token`) VALUES (?,?,?,?,?,?,?)");

$done = $stmt->execute([$username, $email, $phone, $age, $hashedPassword, $imagename, $token]);

if ($done) {
    echo json_encode([
        "status" => "create account successfully",
        "token"  => $token,
    ], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "create account unsuccessfully"
    ], JSON_UNESCAPED_UNICODE);
}
