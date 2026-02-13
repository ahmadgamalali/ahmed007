<?php
require_once '../config.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$response = ['success' => false, 'message' => ''];

try {
    switch ($action) {
        case 'list':
            $stmt = $db->prepare("SELECT id, name, name_ar, slug, bio, bio_ar, image_url, category, category_ar, specialization, specialization_ar, follower_count, engagement_rate, platform, platform_url, email, rate_per_post, is_featured, verification_status, status FROM influencers WHERE status = 'active' ORDER BY is_featured DESC, follower_count DESC");
            $stmt->execute();
            $influencers = $stmt->fetchAll();
            $response = ['success' => true, 'data' => $influencers];
            break;

        case 'get':
            $id = intval($_GET['id'] ?? 0);
            $stmt = $db->prepare("SELECT * FROM influencers WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $influencer = $stmt->fetch();

            if ($influencer) {
                // Get portfolio items
                $portfolioStmt = $db->prepare("SELECT * FROM influencer_portfolio WHERE influencer_id = :id");
                $portfolioStmt->execute([':id' => $id]);
                $influencer['portfolio'] = $portfolioStmt->fetchAll();

                $response = ['success' => true, 'data' => $influencer];
            } else {
                $response['message'] = 'المؤثر غير موجود';
            }
            break;

        case 'contact':
            $influencer_id = intval($_POST['influencer_id'] ?? 0);
            $name = sanitize($_POST['name'] ?? '');
            $email = sanitize($_POST['email'] ?? '');
            $phone = sanitize($_POST['phone'] ?? '');
            $subject = sanitize($_POST['subject'] ?? '');
            $message = sanitize($_POST['message'] ?? '');
            $budget = floatval($_POST['budget'] ?? 0);

            if (empty($name) || empty($email) || empty($subject) || empty($message)) {
                $response['message'] = 'جميع الحقول مطلوبة';
                break;
            }

            $stmt = $db->prepare("INSERT INTO influencer_contacts (influencer_id, contact_name, contact_email, contact_phone, subject, message, budget, response_status, created_at) VALUES (:influencer_id, :contact_name, :contact_email, :contact_phone, :subject, :message, :budget, 'pending', NOW())");

            $stmt->execute([
                ':influencer_id' => $influencer_id,
                ':contact_name' => $name,
                ':contact_email' => $email,
                ':contact_phone' => $phone,
                ':subject' => $subject,
                ':message' => $message,
                ':budget' => $budget
            ]);

            $contact_id = $db->lastInsertId();

            // Update contact count
            $updateStmt = $db->prepare("UPDATE influencers SET contacts_count = contacts_count + 1 WHERE id = :id");
            $updateStmt->execute([':id' => $influencer_id]);

            $response = ['success' => true, 'message' => 'تم إرسال رسالتك بنجاح', 'id' => $contact_id];
            break;

        case 'create':
            if (!isAdmin()) {
                $response['message'] = 'غير مصرح بالدخول';
                break;
            }

            $name = sanitize($_POST['name'] ?? '');
            $name_ar = sanitize($_POST['name_ar'] ?? '');
            $slug = sanitize($_POST['slug'] ?? '');
            $bio = sanitize($_POST['bio'] ?? '');
            $bio_ar = sanitize($_POST['bio_ar'] ?? '');
            $category = $_POST['category'] ?? '';
            $category_ar = $_POST['category_ar'] ?? '';
            $platform = $_POST['platform'] ?? 'instagram';
            $platform_url = sanitize($_POST['platform_url'] ?? '');
            $follower_count = intval($_POST['follower_count'] ?? 0);
            $engagement_rate = floatval($_POST['engagement_rate'] ?? 0);
            $rate_per_post = floatval($_POST['rate_per_post'] ?? 0);
            $email = sanitize($_POST['email'] ?? '');
            $phone = sanitize($_POST['phone'] ?? '');
            $country = sanitize($_POST['country'] ?? '');
            $image_url = sanitize($_POST['image_url'] ?? '');

            if (empty($name_ar) || empty($slug)) {
                $response['message'] = 'الاسم والـ slug مطلوبان';
                break;
            }

            $stmt = $db->prepare("INSERT INTO influencers (name, name_ar, slug, bio, bio_ar, category, category_ar, platform, platform_url, follower_count, engagement_rate, rate_per_post, email, phone, country, image_url) VALUES (:name, :name_ar, :slug, :bio, :bio_ar, :category, :category_ar, :platform, :platform_url, :follower_count, :engagement_rate, :rate_per_post, :email, :phone, :country, :image_url)");

            $result = $stmt->execute([
                ':name' => $name,
                ':name_ar' => $name_ar,
                ':slug' => $slug,
                ':bio' => $bio,
                ':bio_ar' => $bio_ar,
                ':category' => $category,
                ':category_ar' => $category_ar,
                ':platform' => $platform,
                ':platform_url' => $platform_url,
                ':follower_count' => $follower_count,
                ':engagement_rate' => $engagement_rate,
                ':rate_per_post' => $rate_per_post,
                ':email' => $email,
                ':phone' => $phone,
                ':country' => $country,
                ':image_url' => $image_url
            ]);

            if ($result) {
                $response = ['success' => true, 'message' => 'تم إضافة المؤثر بنجاح', 'id' => $db->lastInsertId()];
            }
            break;

        case 'update':
            if (!isAdmin()) {
                $response['message'] = 'غير مصرح بالدخول';
                break;
            }

            $id = intval($_POST['id'] ?? 0);
            $follower_count = intval($_POST['follower_count'] ?? 0);
            $engagement_rate = floatval($_POST['engagement_rate'] ?? 0);
            $rate_per_post = floatval($_POST['rate_per_post'] ?? 0);
            $is_featured = isset($_POST['is_featured']) ? 1 : 0;
            $status = $_POST['status'] ?? 'active';

            $stmt = $db->prepare("UPDATE influencers SET follower_count = :follower_count, engagement_rate = :engagement_rate, rate_per_post = :rate_per_post, is_featured = :is_featured, status = :status WHERE id = :id");

            $result = $stmt->execute([
                ':follower_count' => $follower_count,
                ':engagement_rate' => $engagement_rate,
                ':rate_per_post' => $rate_per_post,
                ':is_featured' => $is_featured,
                ':status' => $status,
                ':id' => $id
            ]);

            if ($result) {
                $response = ['success' => true, 'message' => 'تم تحديث المؤثر بنجاح'];
            }
            break;

        case 'delete':
            if (!isAdmin()) {
                $response['message'] = 'غير مصرح بالدخول';
                break;
            }

            $id = intval($_POST['id'] ?? 0);
            $stmt = $db->prepare("DELETE FROM influencers WHERE id = :id");
            $result = $stmt->execute([':id' => $id]);

            if ($result) {
                $response = ['success' => true, 'message' => 'تم حذف المؤثر بنجاح'];
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
