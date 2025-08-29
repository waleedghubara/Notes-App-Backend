<?php
include "../connext.php";


$user = checkToken($connect);
$usersId = $user['id'];


$data = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST)) {
        $data = $_POST;
    } else {
        $json = file_get_contents("php://input");
        $data = json_decode($json, true) ?? [];
    }
}


$notesId   = filterData($data["id"] ?? '');
$imageName = filterData($data["imagename"] ?? '');


if (empty($notesId)) {
    echo json_encode([
        "status"  => "error",
        "message" => "لم يتم إرسال معرف الملاحظة"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}


$stmt = $connect->prepare("DELETE FROM `notes` WHERE `notes_id` = ? AND `users_id` = ?");
$stmt->execute([$notesId, $usersId]);
$const = $stmt->rowCount();

if ($const > 0) {
    if (!empty($imageName)) {
        deleteImage("../upload", $imageName);
    }

    echo json_encode([
        "status"  => "success",
        "message" => "تم حذف الملاحظة بنجاح"
    ], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode([
        "status"  => "error",
        "message" => "لم يتم العثور على ملاحظة بهذا المعرف تخص هذا المستخدم"
    ], JSON_UNESCAPED_UNICODE);
}
