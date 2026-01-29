<?php
/**
 * System Integration Verification Script
 * Place this file in your root directory and access via browser
 * URL: http://yourdomain.com/verify.php
 * 
 * This script checks if all components are properly configured
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<html><head><meta charset='UTF-8'><title>System Verification</title>";
echo "<style>body{font-family:Arial;padding:20px;direction:rtl}";
echo ".success{color:green;font-weight:bold}.error{color:red;font-weight:bold}";
echo "h1{color:#08137b}h2{color:#4f09a7;margin-top:30px}";
echo "table{border-collapse:collapse;width:100%;margin:20px 0}";
echo "td,th{border:1px solid #ddd;padding:12px;text-align:right}";
echo "th{background:#f5f5f5}</style></head><body>";

echo "<h1>🔍 نظام التحقق من التكامل</h1>";
echo "<p>هذا الملف يتحقق من أن جميع مكونات النظام تعمل بشكل صحيح</p>";
echo "<hr>";

$checks = [];

// ===================================
// 1. Check PHP Version
// ===================================
echo "<h2>1. فحص إصدار PHP</h2>";
$phpVersion = phpversion();
$phpOK = version_compare($phpVersion, '7.4.0', '>=');
echo "<p>إصدار PHP الحالي: <b>$phpVersion</b> ";
echo $phpOK ? "<span class='success'>✓</span>" : "<span class='error'>✗ (يجب 7.4 أو أحدث)</span>";
echo "</p>";
$checks['PHP Version'] = $phpOK;

// ===================================
// 2. Check config.php
// ===================================
echo "<h2>2. فحص ملف الإعدادات</h2>";
$configExists = file_exists('config.php');
echo "<p>ملف config.php: ";
echo $configExists ? "<span class='success'>✓ موجود</span>" : "<span class='error'>✗ غير موجود</span>";
echo "</p>";
$checks['Config File'] = $configExists;

if ($configExists) {
    // ===================================
    // 3. Check Database Connection
    // ===================================
    echo "<h2>3. فحص الاتصال بقاعدة البيانات</h2>";
    try {
        require_once 'config.php';
        echo "<p>الاتصال بقاعدة البيانات: <span class='success'>✓ ناجح</span></p>";
        echo "<p>قاعدة البيانات: <b>" . DB_NAME . "</b></p>";
        $checks['Database Connection'] = true;
        
        // ===================================
        // 4. Check Tables
        // ===================================
        echo "<h2>4. فحص جداول قاعدة البيانات</h2>";
        echo "<table>";
        echo "<tr><th>الجدول</th><th>الحالة</th><th>عدد السجلات</th></tr>";
        
        $tables = ['admin_users', 'contact_messages', 'articles', 'services', 'site_settings', 'activity_log'];
        $allTablesExist = true;
        
        foreach ($tables as $table) {
            try {
                $stmt = $db->query("SELECT COUNT(*) as count FROM $table");
                $count = $stmt->fetch()['count'];
                echo "<tr><td>$table</td><td><span class='success'>✓</span></td><td>$count</td></tr>";
                $checks["Table: $table"] = true;
            } catch(PDOException $e) {
                echo "<tr><td>$table</td><td><span class='error'>✗ غير موجود</span></td><td>-</td></tr>";
                $allTablesExist = false;
                $checks["Table: $table"] = false;
            }
        }
        echo "</table>";
        $checks['All Tables Exist'] = $allTablesExist;
        
        // ===================================
        // 5. Check Admin User
        // ===================================
        echo "<h2>5. فحص المستخدم الإداري</h2>";
        try {
            $stmt = $db->query("SELECT COUNT(*) as count FROM admin_users WHERE status = 'active'");
            $adminCount = $stmt->fetch()['count'];
            echo "<p>عدد المستخدمين الإداريين النشطين: <b>$adminCount</b> ";
            echo $adminCount > 0 ? "<span class='success'>✓</span>" : "<span class='error'>✗</span>";
            echo "</p>";
            $checks['Admin Users Exist'] = $adminCount > 0;
            
            if ($adminCount > 0) {
                $stmt = $db->query("SELECT username, email, full_name FROM admin_users WHERE status = 'active' LIMIT 3");
                echo "<table>";
                echo "<tr><th>اسم المستخدم</th><th>البريد الإلكتروني</th><th>الاسم الكامل</th></tr>";
                while ($admin = $stmt->fetch()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($admin['username']) . "</td>";
                    echo "<td>" . htmlspecialchars($admin['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($admin['full_name']) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
        } catch(PDOException $e) {
            echo "<p><span class='error'>✗ خطأ في جلب المستخدمين</span></p>";
            $checks['Admin Users Exist'] = false;
        }
        
    } catch(PDOException $e) {
        echo "<p>الاتصال بقاعدة البيانات: <span class='error'>✗ فشل</span></p>";
        echo "<p>الخطأ: " . $e->getMessage() . "</p>";
        $checks['Database Connection'] = false;
    }
}

// ===================================
// 6. Check Required Directories
// ===================================
echo "<h2>6. فحص المجلدات المطلوبة</h2>";
echo "<table>";
echo "<tr><th>المجلد</th><th>الحالة</th></tr>";

$directories = [
    'admin' => 'لوحة التحكم',
    'admin/pages' => 'صفحات لوحة التحكم',
    'admin/ajax' => 'معالجات AJAX',
    'admin/errors' => 'صفحات الأخطاء',
    'api' => 'واجهات API',
];

$allDirsExist = true;
foreach ($directories as $dir => $name) {
    $exists = is_dir($dir);
    echo "<tr><td>$name ($dir)</td><td>";
    echo $exists ? "<span class='success'>✓</span>" : "<span class='error'>✗</span>";
    echo "</td></tr>";
    $checks["Directory: $dir"] = $exists;
    if (!$exists) $allDirsExist = false;
}
echo "</table>";
$checks['All Directories Exist'] = $allDirsExist;

// ===================================
// 7. Check API Files
// ===================================
echo "<h2>7. فحص ملفات API</h2>";
echo "<table>";
echo "<tr><th>الملف</th><th>الحالة</th><th>الرابط</th></tr>";

$apiFiles = [
    'contact.php' => 'نموذج التواصل',
    'articles.php' => 'المقالات',
    'services.php' => 'الخدمات',
    'settings.php' => 'الإعدادات',
];

$allAPIFilesExist = true;
foreach ($apiFiles as $file => $name) {
    $path = "api/$file";
    $exists = file_exists($path);
    echo "<tr><td>$name</td><td>";
    echo $exists ? "<span class='success'>✓</span>" : "<span class='error'>✗</span>";
    echo "</td><td>";
    if ($exists) {
        $url = "/api/$file";
        echo "<a href='$url' target='_blank'>اختبار</a>";
    }
    echo "</td></tr>";
    $checks["API: $file"] = $exists;
    if (!$exists) $allAPIFilesExist = false;
}
echo "</table>";
$checks['All API Files Exist'] = $allAPIFilesExist;

// ===================================
// 8. Check Admin Files
// ===================================
echo "<h2>8. فحص ملفات لوحة التحكم</h2>";
$adminFiles = [
    'admin/index.php' => 'الصفحة الرئيسية',
    'admin/login.php' => 'صفحة تسجيل الدخول',
];

echo "<table>";
echo "<tr><th>الملف</th><th>الحالة</th></tr>";
$allAdminFilesExist = true;
foreach ($adminFiles as $file => $name) {
    $exists = file_exists($file);
    echo "<tr><td>$name</td><td>";
    echo $exists ? "<span class='success'>✓</span>" : "<span class='error'>✗</span>";
    echo "</td></tr>";
    $checks["File: $file"] = $exists;
    if (!$exists) $allAdminFilesExist = false;
}
echo "</table>";
$checks['All Admin Files Exist'] = $allAdminFilesExist;

// ===================================
// 9. Final Summary
// ===================================
echo "<h2>9. الملخص النهائي</h2>";

$totalChecks = count($checks);
$passedChecks = count(array_filter($checks));
$percentage = round(($passedChecks / $totalChecks) * 100);

echo "<div style='background:#f5f5f5;padding:20px;border-radius:10px;margin:20px 0'>";
echo "<h3>النتيجة: $passedChecks / $totalChecks اختبار ناجح ($percentage%)</h3>";

if ($percentage == 100) {
    echo "<p style='color:green;font-size:20px;font-weight:bold'>✓ جميع الفحوصات ناجحة! النظام جاهز للعمل</p>";
    echo "<p><a href='/admin/login.php' style='background:#08137b;color:white;padding:15px 30px;text-decoration:none;border-radius:5px;display:inline-block;margin-top:10px'>الذهاب إلى لوحة التحكم</a></p>";
} else if ($percentage >= 80) {
    echo "<p style='color:orange;font-size:18px;font-weight:bold'>⚠ معظم الفحوصات ناجحة، لكن هناك بعض المشاكل</p>";
} else {
    echo "<p style='color:red;font-size:18px;font-weight:bold'>✗ هناك مشاكل كثيرة تحتاج إلى إصلاح</p>";
}

echo "<h4 style='margin-top:20px'>الفحوصات الفاشلة:</h4>";
echo "<ul>";
$hasFailures = false;
foreach ($checks as $check => $status) {
    if (!$status) {
        echo "<li style='color:red'>✗ $check</li>";
        $hasFailures = true;
    }
}
if (!$hasFailures) {
    echo "<li style='color:green'>لا توجد مشاكل!</li>";
}
echo "</ul>";

echo "</div>";

// ===================================
// 10. Recommendations
// ===================================
echo "<h2>10. التوصيات</h2>";
echo "<ul>";

if (!$checks['Database Connection']) {
    echo "<li><b>إصلاح الاتصال بقاعدة البيانات:</b> تحقق من بيانات الاتصال في config.php</li>";
}

if (!$checks['All Tables Exist']) {
    echo "<li><b>إنشاء الجداول:</b> قم باستيراد ملف database_schema.sql</li>";
}

if (!$checks['Admin Users Exist']) {
    echo "<li><b>إضافة مستخدم إداري:</b> قم بإدراج مستخدم في جدول admin_users</li>";
}

if (!$checks['All Directories Exist']) {
    echo "<li><b>إنشاء المجلدات:</b> تأكد من رفع جميع المجلدات المطلوبة</li>";
}

if (!$checks['All API Files Exist']) {
    echo "<li><b>رفع ملفات API:</b> تأكد من رفع مجلد api/ بجميع ملفاته</li>";
}

if ($percentage == 100) {
    echo "<li style='color:green'><b>✓ النظام جاهز تماماً!</b> يمكنك البدء في الاستخدام</li>";
}

echo "</ul>";

echo "<hr style='margin:40px 0'>";
echo "<p style='text-align:center;color:#999'>اصدار 2.0.0 - System Integration Verification</p>";
echo "<p style='text-align:center'><b>ملاحظة:</b> احذف هذا الملف بعد التحقق من النظام لأسباب أمنية</p>";

echo "</body></html>";
?>
