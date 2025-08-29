<?php
include "../connext.php";

$data = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!empty($_GET)) {
        $data = $_GET;
    } else {
        $json = file_get_contents("php://input");
        $data = json_decode($json, true) ?? [];
    }
}
$user = checkToken($connect);
$usersId = filterData($data["id"] ?? '');

$stmt = $connect->prepare("SELECT * FROM `notes` WHERE `users_id`= ?");
$stmt->execute([$usersId]);
$const = $stmt->rowCount();
$notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($const > 0) {
    echo json_encode(
        [
            "status"  => "success",
            "message" => "تم جلب الملاحظات بنجاح ",
            "count"   => $const,
            "data"    => $notes
        ],
        JSON_UNESCAPED_UNICODE
    );
} else {
    echo json_encode(
        [
            "status"  => "error",
            "message" => "لا توجد ملاحظات لهذا المستخدم",
            "data"    => []
        ],
        JSON_UNESCAPED_UNICODE
    );
}
