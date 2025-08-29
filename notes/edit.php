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

$notesid = filterData($data["id"] ?? '');
$titel   = filterData($data["titel"] ?? '');
$content = filterData($data["content"] ?? '');

if (empty($notesid) || empty($titel) || empty($content)) {
    echo json_encode([
        "status"  => "error",
        "message" => "الرجاء إدخال كل البيانات المطلوبة (العنوان، المحتوى، رقم الملاحظة)."
    ], JSON_UNESCAPED_UNICODE);
    exit;
}


$check = $connect->prepare("SELECT * FROM `notes` WHERE `notes_id`=? AND `users_id`=?");
$check->execute([$notesid, $usersId]);
if ($check->rowCount() == 0) {
    echo json_encode([
        "status"  => "error",
        "message" => "الملاحظة غير موجودة أو لا تملك صلاحية لتعديلها"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}


$stmt = $connect->prepare("UPDATE `notes` SET `titel`=?, `content`=? WHERE `notes_id`=? AND `users_id`=?");
$stmt->execute([$titel, $content, $notesid, $usersId]);

if ($stmt->rowCount() > 0) {
    echo json_encode([
        "status"  => "success",
        "message" => "تم تعديل الملاحظة بنجاح",
        "data"    => [
            "notes_id" => $notesid,
            "titel"    => $titel,
            "content"  => $content
        ]
    ], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode([
        "status"  => "error",
        "message" => "لم يتم تعديل أي بيانات (ربما لم تقم بأي تغيير)"
    ], JSON_UNESCAPED_UNICODE);
}
