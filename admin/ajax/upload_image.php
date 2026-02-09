<?php
require_once '../../config.php';
requireAdmin();
header('Content-Type: application/json');

try {
    if (empty($_FILES['image'])) throw new Exception('No file uploaded');

    $file = $_FILES['image'];
    if ($file['error'] !== UPLOAD_ERR_OK) throw new Exception('Upload error');

    $allowed = ['image/jpeg','image/png','image/webp','image/gif'];
    if (!in_array($file['type'], $allowed)) throw new Exception('Invalid file type');

    $uploadDir = __DIR__ . '/../../uploads/articles';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $name = bin2hex(random_bytes(8)) . '.' . $ext;
    $dest = $uploadDir . '/' . $name;

    if (!move_uploaded_file($file['tmp_name'], $dest)) throw new Exception('Move failed');

    // create thumbnail (max width 400) in uploads/articles/thumbs
    $thumbDir = $uploadDir . '/thumbs';
    if (!is_dir($thumbDir)) mkdir($thumbDir, 0755, true);

    $thumbPath = $thumbDir . '/' . $name;
    list($w, $h) = getimagesize($dest);
    $maxW = 400;
    if ($w > $maxW) {
        $ratio = $h / $w;
        $newW = $maxW;
        $newH = intval($newW * $ratio);

        $srcImg = null;
        switch ($file['type']) {
            case 'image/jpeg': $srcImg = imagecreatefromjpeg($dest); break;
            case 'image/png': $srcImg = imagecreatefrompng($dest); break;
            case 'image/webp': $srcImg = imagecreatefromwebp($dest); break;
            case 'image/gif': $srcImg = imagecreatefromgif($dest); break;
        }

        if ($srcImg) {
            $thumbImg = imagecreatetruecolor($newW, $newH);
            // preserve PNG/GIF transparency
            if ($file['type'] === 'image/png' || $file['type'] === 'image/gif') {
                imagecolortransparent($thumbImg, imagecolorallocatealpha($thumbImg, 0, 0, 0, 127));
                imagealphablending($thumbImg, false);
                imagesavealpha($thumbImg, true);
            }
            imagecopyresampled($thumbImg, $srcImg, 0,0,0,0, $newW, $newH, $w, $h);
            switch ($file['type']) {
                case 'image/jpeg': imagejpeg($thumbImg, $thumbPath, 82); break;
                case 'image/png': imagepng($thumbImg, $thumbPath); break;
                case 'image/webp': imagewebp($thumbImg, $thumbPath, 80); break;
                case 'image/gif': imagegif($thumbImg, $thumbPath); break;
            }
            imagedestroy($srcImg);
            imagedestroy($thumbImg);
        } else {
            // fallback - copy original
            copy($dest, $thumbPath);
        }
    } else {
        // small image - copy
        copy($dest, $thumbPath);
    }

    $url = '/uploads/articles/' . $name;
    // TinyMCE expects JSON with location key or simple url depending
    echo json_encode(['success' => true, 'location' => $url, 'url' => $url]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

?>
