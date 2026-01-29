<?php
// Disable output buffering
if (ob_get_level()) ob_end_clean();

require_once '../../config.php';

// Check if user is admin
if (!isAdmin()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'غير مصرح بالدخول'], JSON_UNESCAPED_UNICODE);
    exit;
}

header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$response = ['success' => false, 'message' => ''];

try {
    switch ($action) {
        case 'get':
            $id = intval($_GET['id'] ?? 0);
            $stmt = $db->prepare("SELECT * FROM articles WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $article = $stmt->fetch();
            if ($article) {
                $response = ['success' => true, 'data' => $article];
            } else {
                $response['message'] = 'المقال غير موجود';
            }
            break;
            
        case 'create':
            $title = sanitize($_POST['title'] ?? '');
            $slug = sanitize($_POST['slug'] ?? '');
            $excerpt = sanitize($_POST['excerpt'] ?? '');
            $content = sanitize($_POST['content'] ?? '');
            $category = $_POST['category'] ?? 'article';
            $badge = sanitize($_POST['badge'] ?? '');
            $image_url = sanitize($_POST['image_url'] ?? '');
            $status = $_POST['status'] ?? 'draft';
            
            if (empty($title) || empty($slug) || empty($content)) {
                $response['message'] = 'يرجى ملء جميع الحقول المطلوبة';
                break;
            }
            
            $stmt = $db->prepare("
                INSERT INTO articles (title, slug, excerpt, content, category, badge, image_url, author_id, status, publish_date)
                VALUES (:title, :slug, :excerpt, :content, :category, :badge, :image_url, :author_id, :status, NOW())
            ");
            
            $stmt->execute([
                ':title' => $title,
                ':slug' => $slug,
                ':excerpt' => $excerpt,
                ':content' => $content,
                ':category' => $category,
                ':badge' => $badge,
                ':image_url' => $image_url,
                ':author_id' => $_SESSION['admin_id'],
                ':status' => $status
            ]);
            
            logActivity($db, $_SESSION['admin_id'], 'article_create', "إضافة مقال: {$title}", 'articles', $db->lastInsertId());
            $response = ['success' => true, 'message' => 'تم إضافة المقال بنجاح'];
            break;
            
        case 'update':
            $id = intval($_POST['id'] ?? 0);
            $title = sanitize($_POST['title'] ?? '');
            $slug = sanitize($_POST['slug'] ?? '');
            $excerpt = sanitize($_POST['excerpt'] ?? '');
            $content = sanitize($_POST['content'] ?? '');
            $category = $_POST['category'] ?? 'article';
            $badge = sanitize($_POST['badge'] ?? '');
            $image_url = sanitize($_POST['image_url'] ?? '');
            $status = $_POST['status'] ?? 'draft';
            
            $stmt = $db->prepare("
                UPDATE articles SET 
                title = :title, slug = :slug, excerpt = :excerpt, content = :content,
                category = :category, badge = :badge, image_url = :image_url, status = :status
                WHERE id = :id
            ");
            
            $stmt->execute([
                ':title' => $title,
                ':slug' => $slug,
                ':excerpt' => $excerpt,
                ':content' => $content,
                ':category' => $category,
                ':badge' => $badge,
                ':image_url' => $image_url,
                ':status' => $status,
                ':id' => $id
            ]);
            
            logActivity($db, $_SESSION['admin_id'], 'article_update', "تحديث مقال: {$title}", 'articles', $id);
            $response = ['success' => true, 'message' => 'تم تحديث المقال بنجاح'];
            break;
            
        case 'delete':
            $id = intval($_POST['id'] ?? 0);
            $stmt = $db->prepare("SELECT title FROM articles WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $article = $stmt->fetch();
            
            if ($article) {
                $delete = $db->prepare("DELETE FROM articles WHERE id = :id");
                $delete->execute([':id' => $id]);
                logActivity($db, $_SESSION['admin_id'], 'article_delete', "حذف مقال: {$article['title']}", 'articles', $id);
                $response = ['success' => true, 'message' => 'تم حذف المقال بنجاح'];
            } else {
                $response['message'] = 'المقال غير موجود';
            }
            break;
            
        default:
            $response['message'] = 'إجراء غير معروف';
    }
} catch(PDOException $e) {
    $response['message'] = 'خطأ: ' . $e->getMessage();
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);