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
            $stmt = $db->prepare("SELECT * FROM services WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $service = $stmt->fetch();
            $response = $service ? ['success' => true, 'data' => $service] : ['success' => false, 'message' => 'الخدمة غير موجودة'];
            break;
            
        case 'create':
            $title = sanitize($_POST['title'] ?? '');
            $description = sanitize($_POST['description'] ?? '');
            $icon = sanitize($_POST['icon'] ?? 'fa-star');
            $display_order = intval($_POST['display_order'] ?? 0);
            $status = $_POST['status'] ?? 'active';
            
            if (empty($title) || empty($description)) {
                $response['message'] = 'يرجى ملء جميع الحقول المطلوبة';
                break;
            }
            
            $stmt = $db->prepare("INSERT INTO services (title, description, icon, display_order, status) VALUES (:title, :desc, :icon, :order, :status)");
            $stmt->execute([':title' => $title, ':desc' => $description, ':icon' => $icon, ':order' => $display_order, ':status' => $status]);
            
            logActivity($db, $_SESSION['admin_id'], 'service_create', "إضافة خدمة: {$title}", 'services', $db->lastInsertId());
            $response = ['success' => true, 'message' => 'تم إضافة الخدمة بنجاح'];
            break;
            
        case 'update':
            $id = intval($_POST['id'] ?? 0);
            $title = sanitize($_POST['title'] ?? '');
            $description = sanitize($_POST['description'] ?? '');
            $icon = sanitize($_POST['icon'] ?? 'fa-star');
            $display_order = intval($_POST['display_order'] ?? 0);
            $status = $_POST['status'] ?? 'active';
            
            $stmt = $db->prepare("UPDATE services SET title = :title, description = :desc, icon = :icon, display_order = :order, status = :status WHERE id = :id");
            $stmt->execute([':title' => $title, ':desc' => $description, ':icon' => $icon, ':order' => $display_order, ':status' => $status, ':id' => $id]);
            
            logActivity($db, $_SESSION['admin_id'], 'service_update', "تحديث خدمة: {$title}", 'services', $id);
            $response = ['success' => true, 'message' => 'تم تحديث الخدمة بنجاح'];
            break;
            
        case 'delete':
            $id = intval($_POST['id'] ?? 0);
            $stmt = $db->prepare("SELECT title FROM services WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $service = $stmt->fetch();
            
            if ($service) {
                $delete = $db->prepare("DELETE FROM services WHERE id = :id");
                $delete->execute([':id' => $id]);
                logActivity($db, $_SESSION['admin_id'], 'service_delete', "حذف خدمة: {$service['title']}", 'services', $id);
                $response = ['success' => true, 'message' => 'تم حذف الخدمة بنجاح'];
            } else {
                $response['message'] = 'الخدمة غير موجودة';
            }
            break;
            
        default:
            $response['message'] = 'إجراء غير معروف';
    }
} catch(PDOException $e) {
    $response['message'] = 'خطأ: ' . $e->getMessage();
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);