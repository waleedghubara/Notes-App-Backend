<?php

function filterData($value)
{
    return htmlspecialchars(strip_tags(trim($value ?? '')));
}

function imageUpload($imageRequest)
{
    global $messageErorr;

    if (!isset($_FILES[$imageRequest])) {
        $messageErorr[] = "لم يتم رفع أي ملف";
        return null;
    }

    $file = $_FILES[$imageRequest];
    $originalName = basename($file['name']);
    $tmpName      = $file['tmp_name'];
    $fileSize     = $file['size'];

    $allowExt = ["png", "jpg", "jpeg"];
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowExt)) {
        $messageErorr[] = "امتداد الملف غير مسموح: $originalName";
        return null;
    }

    if ($fileSize > 2 * 1024 * 1024) {
        $messageErorr[] = "حجم الملف أكبر من 2 ميجابايت: $originalName";
        return null;
    }

    $uploadDir = __DIR__ . "/upload/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $uploadPath = $uploadDir . $originalName;
    $counter = 1;

    while (file_exists($uploadPath)) {
        $uploadPath = $uploadDir
            . pathinfo($originalName, PATHINFO_FILENAME)
            . "_$counter."
            . $ext;
        $counter++;
    }

    if (move_uploaded_file($tmpName, $uploadPath)) {
        return basename($uploadPath);
    } else {
        $messageErorr[] = "فشل رفع الصورة: $originalName";
        return null;
    }
}

function deleteImage($dir, $imagename)
{
    if (file_exists($dir . "/" . $imagename)) {
        unlink($dir . "/" . $imagename);
    }
}

function checkToken($connect)
{
    $token = '';


    if (function_exists('getallheaders')) {
        $headers = getallheaders();
        $headers = array_change_key_case($headers, CASE_LOWER);

        if (isset($headers['authorization'])) {
            $token = $headers['authorization'];
        }
    }


    if (empty($token)) {
        if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
            $token = $_SERVER['HTTP_AUTHORIZATION'];
        } elseif (!empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $token = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        }
    }


    if (empty($token) && function_exists('apache_request_headers')) {
        $headers = apache_request_headers();
        $headers = array_change_key_case($headers, CASE_LOWER);
        if (isset($headers['authorization'])) {
            $token = $headers['authorization'];
        }
    }


    if (empty($token) && isset($_POST['token'])) {
        $token = $_POST['token'];
    }


    if (empty($token)) {
        echo json_encode(["status" => "error", "message" => "Token required"], JSON_UNESCAPED_UNICODE);
        exit;
    }


    if (stripos($token, 'Bearer ') === 0) {
        $token = substr($token, 7);
    }

    $stmt = $connect->prepare("SELECT * FROM `users` WHERE `token` = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(["status" => "error", "message" => "Invalid token"], JSON_UNESCAPED_UNICODE);
        exit;
    }

    return $user;
}
