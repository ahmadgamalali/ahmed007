<style>
.activity-container { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
.activity-filters { display: flex; gap: 15px; margin-bottom: 25px; }
.activity-table { width: 100%; border-collapse: collapse; }
.activity-table th { background: var(--bg-light); padding: 15px; text-align: right; font-weight: 600; color: var(--primary-blue); }
.activity-table td { padding: 15px; border-bottom: 1px solid var(--border-color); }
.activity-type { padding: 5px 12px; border-radius: 15px; font-size: 12px; font-weight: 600; }
.type-login { background: #3498db; color: white; }
.type-logout { background: #95a5a6; color: white; }
.type-create { background: #27ae60; color: white; }
.type-update { background: #f39c12; color: white; }
.type-delete { background: #e74c3c; color: white; }
</style>

<?php
$stmt = $db->prepare("
    SELECT a.*, u.full_name, u.username 
    FROM activity_log a
    JOIN admin_users u ON a.admin_id = u.id
    ORDER BY a.created_at DESC
    LIMIT 100
");
$stmt->execute();
$activities = $stmt->fetchAll();
?>

<div class="activity-container">
    <h2>سجل النشاطات</h2>
    
    <table class="activity-table">
        <thead>
            <tr>
                <th>المستخدم</th>
                <th>النوع</th>
                <th>الوصف</th>
                <th>عنوان IP</th>
                <th>التاريخ</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($activities as $activity): ?>
                <tr>
                    <td><?php echo sanitize($activity['full_name']); ?></td>
                    <td>
                        <span class="activity-type type-<?php 
                            $type = explode('_', $activity['action_type'])[0];
                            echo in_array($type, ['login', 'logout', 'create', 'update', 'delete']) ? $type : 'update';
                        ?>">
                            <?php echo sanitize($activity['action_type']); ?>
                        </span>
                    </td>
                    <td><?php echo sanitize($activity['action_description']); ?></td>
                    <td><?php echo sanitize($activity['ip_address']); ?></td>
                    <td><?php echo date('Y-m-d H:i', strtotime($activity['created_at'])); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
