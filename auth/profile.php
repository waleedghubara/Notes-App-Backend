<?php
include "../connext.php";

$user = checkToken($connect);
$usersId = $user['id'];

$stmt = $connect->prepare("SELECT `id`, `username`, `phone`, `email`, `age`, `profile` FROM `users` WHERE `id` = ?");
$stmt->execute([$usersId]);
$const = $stmt->rowCount();
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

if ($const > 0) {
    echo json_encode(
        [
            "status"  => "success",
            "message" => "تم جلب البيانات بنجاح",
            "data"    => $profile
        ],
        JSON_UNESCAPED_UNICODE
    );
} else {
    echo json_encode(
        [
            "status"  => "error",
            "message" => "لا توجد بيانات لهذا المستخدم",
            "data"    => []
        ],
        JSON_UNESCAPED_UNICODE
    );
}
