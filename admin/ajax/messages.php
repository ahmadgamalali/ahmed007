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
        case 'view':
            $id = intval($_GET['id'] ?? 0);
            $stmt = $db->prepare("SELECT * FROM contact_messages WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $message = $stmt->fetch();
            
            if ($message) {
                // Mark as read
                if ($message['status'] === 'new') {
                    $update = $db->prepare("UPDATE contact_messages SET status = 'read' WHERE id = :id");
                    $update->execute([':id' => $id]);
                    logActivity($db, $_SESSION['admin_id'], 'message_read', "قراءة الرسالة من {$message['name']}", 'contact_messages', $id);
                }
                
                $response['success'] = true;
                $response['data'] = $message;
            } else {
                $response['message'] = 'الرسالة غير موجودة';
            }
            break;
            
        case 'reply':
            $id = intval($_POST['id'] ?? 0);
            $reply = sanitize($_POST['reply'] ?? '');
            
            if (empty($reply)) {
                $response['message'] = 'يرجى كتابة الرد';
                break;
            }
            
            $stmt = $db->prepare("SELECT * FROM contact_messages WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $message = $stmt->fetch();
            
            if ($message) {
                // Update message with reply
                $update = $db->prepare("
                    UPDATE contact_messages 
                    SET admin_reply = :reply, 
                        replied_by = :admin_id, 
                        replied_at = NOW(),
                        status = 'replied'
                    WHERE id = :id
                ");
                
                $update->execute([
                    ':reply' => $reply,
                    ':admin_id' => $_SESSION['admin_id'],
                    ':id' => $id
                ]);
                
                // Here you would send email notification
                // mail($message['email'], 'رد على رسالتك', $reply);
                
                logActivity($db, $_SESSION['admin_id'], 'message_reply', "الرد على رسالة من {$message['name']}", 'contact_messages', $id);
                
                $response['success'] = true;
                $response['message'] = 'تم إرسال الرد بنجاح';
            } else {
                $response['message'] = 'الرسالة غير موجودة';
            }
            break;
            
        case 'delete':
            $id = intval($_POST['id'] ?? 0);
            
            $stmt = $db->prepare("SELECT name FROM contact_messages WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $message = $stmt->fetch();
            
            if ($message) {
                $delete = $db->prepare("DELETE FROM contact_messages WHERE id = :id");
                $delete->execute([':id' => $id]);
                
                logActivity($db, $_SESSION['admin_id'], 'message_delete', "حذف رسالة من {$message['name']}", 'contact_messages', $id);
                
                $response['success'] = true;
                $response['message'] = 'تم حذف الرسالة بنجاح';
            } else {
                $response['message'] = 'الرسالة غير موجودة';
            }
            break;
            
        case 'update_status':
            $id = intval($_POST['id'] ?? 0);
            $status = $_POST['status'] ?? '';
            
            if (!in_array($status, ['new', 'read', 'replied', 'archived'])) {
                $response['message'] = 'حالة غير صحيحة';
                break;
            }
            
            $stmt = $db->prepare("UPDATE contact_messages SET status = :status WHERE id = :id");
            $stmt->execute([':status' => $status, ':id' => $id]);
            
            logActivity($db, $_SESSION['admin_id'], 'message_status', "تغيير حالة الرسالة إلى {$status}", 'contact_messages', $id);
            
            $response['success'] = true;
            $response['message'] = 'تم تحديث الحالة بنجاح';
            break;
            
        default:
            $response['message'] = 'إجراء غير معروف';
    }
} catch(PDOException $e) {
    $response['message'] = 'خطأ في قاعدة البيانات: ' . $e->getMessage();
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);