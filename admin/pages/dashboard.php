<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 25px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 20px;
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-icon {
    width: 70px;
    height: 70px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    color: white;
}

.stat-icon.blue { background: linear-gradient(135deg, #08137b, #4f09a7); }
.stat-icon.green { background: linear-gradient(135deg, #27ae60, #2ecc71); }
.stat-icon.orange { background: linear-gradient(135deg, #e67e22, #f39c12); }
.stat-icon.red { background: linear-gradient(135deg, #c0392b, #e74c3c); }

.stat-info h3 {
    font-size: 36px;
    color: var(--primary-blue);
    margin-bottom: 5px;
}

.stat-info p {
    color: #7f8c8d;
    font-size: 16px;
}

.quick-actions {
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.quick-actions h2 {
    color: var(--primary-blue);
    margin-bottom: 20px;
    font-size: 24px;
}

.action-buttons {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.action-btn {
    padding: 15px 20px;
    border-radius: 10px;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
    text-align: right;
}

.action-btn.primary {
    background: linear-gradient(135deg, #08137b, #4f09a7);
    color: white;
}

.action-btn.secondary {
    background: linear-gradient(135deg, #c5a47e, #a88a65);
    color: white;
}

.action-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}

.recent-activity {
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.recent-activity h2 {
    color: var(--primary-blue);
    margin-bottom: 20px;
    font-size: 24px;
}

.activity-list {
    max-height: 400px;
    overflow-y: auto;
}

.activity-item {
    padding: 15px;
    border-right: 3px solid var(--accent-gold);
    background: var(--bg-light);
    margin-bottom: 15px;
    border-radius: 10px;
}

.activity-item .activity-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 5px;
}

.activity-item .activity-type {
    font-weight: 600;
    color: var(--primary-blue);
}

.activity-item .activity-time {
    color: #7f8c8d;
    font-size: 14px;
}

.activity-item .activity-desc {
    color: #666;
    font-size: 14px;
}

.welcome-banner {
    background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-purple) 100%);
    color: white;
    padding: 40px;
    border-radius: 15px;
    margin-bottom: 30px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.welcome-banner h1 {
    font-size: 32px;
    margin-bottom: 10px;
}

.welcome-banner p {
    font-size: 18px;
    opacity: 0.9;
}
</style>

<?php
// Get recent activity
try {
    $stmt = $db->prepare("
        SELECT a.*, u.full_name 
        FROM activity_log a
        JOIN admin_users u ON a.admin_id = u.id
        ORDER BY a.created_at DESC
        LIMIT 10
    ");
    $stmt->execute();
    $activities = $stmt->fetchAll();
} catch(PDOException $e) {
    $activities = [];
}
?>

<div class="welcome-banner">
    <h1>مرحباً، <?php echo sanitize($admin['full_name']); ?>! 👋</h1>
    <p>نتمنى لك يوماً مثمراً في إدارة موقعك</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-envelope"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $stats['messages']; ?></h3>
            <p>رسائل جديدة</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-newspaper"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $stats['articles']; ?></h3>
            <p>مقالات منشورة</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-briefcase"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $stats['services']; ?></h3>
            <p>خدمات نشطة</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon red">
            <i class="fas fa-inbox"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $stats['total_messages']; ?></h3>
            <p>إجمالي الرسائل</p>
        </div>
    </div>
</div>

<div class="quick-actions">
    <h2><i class="fas fa-bolt"></i> إجراءات سريعة</h2>
    <div class="action-buttons">
        <a href="?page=messages" class="action-btn primary">
            <i class="fas fa-envelope"></i>
            <span>عرض الرسائل</span>
        </a>
        <a href="?page=articles" class="action-btn primary">
            <i class="fas fa-plus-circle"></i>
            <span>إضافة مقال</span>
        </a>
        <a href="?page=services" class="action-btn secondary">
            <i class="fas fa-briefcase"></i>
            <span>إدارة الخدمات</span>
        </a>
        <a href="?page=settings" class="action-btn secondary">
            <i class="fas fa-cog"></i>
            <span>الإعدادات</span>
        </a>
    </div>
</div>

<div class="recent-activity">
    <h2><i class="fas fa-history"></i> النشاطات الأخيرة</h2>
    <div class="activity-list">
        <?php if (count($activities) > 0): ?>
            <?php foreach ($activities as $activity): ?>
                <div class="activity-item">
                    <div class="activity-header">
                        <span class="activity-type">
                            <i class="fas fa-user"></i> <?php echo sanitize($activity['full_name']); ?>
                        </span>
                        <span class="activity-time">
                            <i class="far fa-clock"></i>
                            <?php 
                            $time = strtotime($activity['created_at']);
                            $diff = time() - $time;
                            if ($diff < 60) echo 'الآن';
                            elseif ($diff < 3600) echo floor($diff / 60) . ' دقيقة';
                            elseif ($diff < 86400) echo floor($diff / 3600) . ' ساعة';
                            else echo date('Y-m-d H:i', $time);
                            ?>
                        </span>
                    </div>
                    <div class="activity-desc">
                        <?php echo sanitize($activity['action_description']); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center; color: #999; padding: 20px;">لا توجد نشاطات حالياً</p>
        <?php endif; ?>
    </div>
</div>