<?php
include "../connext.php";

$user = checkToken($connect);


$data = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST)) {
        $data = $_POST;
    } else {
        $json = file_get_contents("php://input");
        $data = json_decode($json, true) ?? [];
    }
}

$titel   = filterData($data["titel"] ?? '');
$content = filterData($data["content"] ?? '');

$usersid = $user['id'];

if (empty($titel) || empty($content)) {
    echo json_encode([
        "status" => "error",
        "message" => "الرجاء إدخال كل البيانات المطلوبة (العنوان والمحتوى)."
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$imagename = imageUpload("image");

try {
    $stmt = $connect->prepare("INSERT INTO `notes` (`titel`, `content`, `users_id`, `notes_image`) VALUES (?, ?, ?, ?)");
    $stmt->execute([$titel, $content, $usersid, $imagename ?? '']);

    if ($stmt->rowCount() > 0) {
        echo json_encode([
            "status" => "success",
            "message" => "تم إضافة الملاحظة بنجاح"
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "حدث خطأ أثناء إضافة الملاحظة"
        ], JSON_UNESCAPED_UNICODE);
    }
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "خطأ في السيرفر: " . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
