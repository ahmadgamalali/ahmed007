<?php
require_once '../config.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$response = ['success' => false, 'message' => ''];

try {
    switch ($action) {
        case 'list':
            $stmt = $db->prepare("SELECT id, word_ar, pronunciation, definition_ar, examples, synonyms, antonyms, word_type, category, image_url, video_url, difficulty_level, usage_count, is_featured FROM dictionary WHERE is_featured = TRUE OR is_featured = FALSE ORDER BY is_featured DESC, usage_count DESC LIMIT 1000");
            $stmt->execute();
            $words = $stmt->fetchAll();
            
            // Parse JSON fields
            foreach ($words as &$word) {
                if ($word['examples']) {
                    $word['examples'] = json_decode($word['examples'], true);
                }
            }
            
            $response = ['success' => true, 'data' => $words];
            break;

        case 'get':
            $id = intval($_GET['id'] ?? 0);
            $stmt = $db->prepare("SELECT * FROM dictionary WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $word = $stmt->fetch();
            
            if ($word) {
                if ($word['examples']) {
                    $word['examples'] = json_decode($word['examples'], true);
                }
                $response = ['success' => true, 'data' => $word];
            } else {
                $response['message'] = 'الكلمة غير موجودة';
            }
            break;

        case 'search':
            $query = $_GET['q'] ?? '';
            if (strlen($query) < 2) {
                $response['message'] = 'أدخل على الأقل حرفين';
                break;
            }

            $query = '%' . $query . '%';
            $stmt = $db->prepare("SELECT id, word_ar, definition_ar, difficulty_level FROM dictionary WHERE word_ar LIKE :query OR definition_ar LIKE :query LIMIT 20");
            $stmt->execute([':query' => $query]);
            $results = $stmt->fetchAll();
            $response = ['success' => true, 'data' => $results];
            break;

        case 'create':
            if (!isAdmin()) {
                $response['message'] = 'غير مصرح بالدخول';
                break;
            }

            $word_ar = sanitize($_POST['word_ar'] ?? '');
            $pronunciation = sanitize($_POST['pronunciation'] ?? '');
            $definition_ar = sanitize($_POST['definition_ar'] ?? '');
            $examples = json_encode(explode("\n", $_POST['examples'] ?? ''));
            $synonyms = sanitize($_POST['synonyms'] ?? '');
            $antonyms = sanitize($_POST['antonyms'] ?? '');
            $word_type = $_POST['word_type'] ?? 'noun';
            $category = $_POST['category'] ?? '';
            $image_url = sanitize($_POST['image_url'] ?? '');
            $video_url = sanitize($_POST['video_url'] ?? '');
            $difficulty_level = $_POST['difficulty_level'] ?? 'beginner';

            if (empty($word_ar) || empty($definition_ar)) {
                $response['message'] = 'الكلمة والتعريف مطلوبان';
                break;
            }

            $stmt = $db->prepare("INSERT INTO dictionary (word_ar, pronunciation, definition_ar, examples, synonyms, antonyms, word_type, category, image_url, video_url, difficulty_level, created_by) VALUES (:word_ar, :pronunciation, :definition_ar, :examples, :synonyms, :antonyms, :word_type, :category, :image_url, :video_url, :difficulty_level, :created_by)");
            
            $stmt->execute([
                ':word_ar' => $word_ar,
                ':pronunciation' => $pronunciation,
                ':definition_ar' => $definition_ar,
                ':examples' => $examples,
                ':synonyms' => $synonyms,
                ':antonyms' => $antonyms,
                ':word_type' => $word_type,
                ':category' => $category,
                ':image_url' => $image_url,
                ':video_url' => $video_url,
                ':difficulty_level' => $difficulty_level,
                ':created_by' => $_SESSION['admin_id'] ?? null
            ]);

            $response = ['success' => true, 'message' => 'تمت إضافة الكلمة بنجاح', 'id' => $db->lastInsertId()];
            break;

        case 'update':
            if (!isAdmin()) {
                $response['message'] = 'غير مصرح بالدخول';
                break;
            }

            $id = intval($_POST['id'] ?? 0);
            $word_ar = sanitize($_POST['word_ar'] ?? '');
            $definition_ar = sanitize($_POST['definition_ar'] ?? '');
            $examples = json_encode(array_filter(explode("\n", $_POST['examples'] ?? '')));

            $stmt = $db->prepare("UPDATE dictionary SET word_ar = :word_ar, definition_ar = :definition_ar, examples = :examples WHERE id = :id");
            $result = $stmt->execute([
                ':word_ar' => $word_ar,
                ':definition_ar' => $definition_ar,
                ':examples' => $examples,
                ':id' => $id
            ]);

            if ($result) {
                $response = ['success' => true, 'message' => 'تم تحديث الكلمة بنجاح'];
            }
            break;

        case 'delete':
            if (!isAdmin()) {
                $response['message'] = 'غير مصرح بالدخول';
                break;
            }

            $id = intval($_POST['id'] ?? 0);
            $stmt = $db->prepare("DELETE FROM dictionary WHERE id = :id");
            $result = $stmt->execute([':id' => $id]);

            if ($result) {
                $response = ['success' => true, 'message' => 'تم حذف الكلمة بنجاح'];
            }
            break;

        default:
            $response['message'] = 'الإجراء غير معروف';
    }
} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'خطأ: ' . $e->getMessage()];
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>
