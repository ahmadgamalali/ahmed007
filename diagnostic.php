<?php
// Simple Diagnostic - Works Everywhere
// Place in: public_html/check.php

// Don't use any includes that might fail
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>فحص النظام</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .box { background: white; padding: 20px; margin: 10px 0; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .success { color: #27ae60; font-weight: bold; }
        .error { color: #e74c3c; font-weight: bold; }
        h1 { color: #08137b; }
        h2 { color: #4f09a7; }
        .test-btn { background: #08137b; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px; text-decoration: none; display: inline-block; }
    </style>
</head>
<body>
    <h1>🔍 فحص النظام البسيط</h1>
    
    <div class="box">
        <h2>1. PHP Information</h2>
        <p>PHP Version: <b><?php echo phpversion(); ?></b></p>
        <p>Server: <b><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></b></p>
    </div>
    
    <div class="box">
        <h2>2. File Check</h2>
        <?php
        $files = [
            'config.php',
            'api/contact.php',
            'api/articles.php',
            'api/services.php',
            'api/settings.php',
            'admin/index.php',
            'admin/ajax/messages.php'
        ];
        
        echo '<table border="1" cellpadding="10" style="width:100%; border-collapse:collapse;">';
        echo '<tr><th>File</th><th>Status</th></tr>';
        foreach ($files as $file) {
            $exists = file_exists($file);
            echo '<tr>';
            echo '<td>' . $file . '</td>';
            echo '<td>' . ($exists ? '<span class="success">✓ EXISTS</span>' : '<span class="error">✗ MISSING</span>') . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        ?>
    </div>
    
    <div class="box">
        <h2>3. Database Connection Test</h2>
        <?php
        if (file_exists('config.php')) {
            try {
                require_once 'config.php';
                echo '<p class="success">✓ Config loaded successfully</p>';
                
                if (isset($db)) {
                    echo '<p class="success">✓ Database connected!</p>';
                    
                    // Test query
                    try {
                        $stmt = $db->query("SELECT COUNT(*) as count FROM contact_messages");
                        $count = $stmt->fetch()['count'];
                        echo '<p>Messages in database: <b>' . $count . '</b></p>';
                    } catch (Exception $e) {
                        echo '<p class="error">✗ Error querying database: ' . $e->getMessage() . '</p>';
                    }
                } else {
                    echo '<p class="error">✗ Database variable not set</p>';
                }
            } catch (Exception $e) {
                echo '<p class="error">✗ Error: ' . $e->getMessage() . '</p>';
            }
        } else {
            echo '<p class="error">✗ config.php not found</p>';
        }
        ?>
    </div>
    
    <div class="box">
        <h2>4. API Test Links</h2>
        <a href="/api/contact.php" target="_blank" class="test-btn">Test Contact API</a>
        <a href="/api/articles.php" target="_blank" class="test-btn">Test Articles API</a>
        <a href="/api/services.php" target="_blank" class="test-btn">Test Services API</a>
        <a href="/api/settings.php" target="_blank" class="test-btn">Test Settings API</a>
    </div>
    
    <div class="box">
        <h2>5. Dashboard Write Test</h2>
        <p>To test if dashboard can write:</p>
        <ol>
            <li>Login to: <a href="/admin/login.php">/admin/login.php</a></li>
            <li>Go to Services page</li>
            <li>Try to add a new service</li>
            <li>If it saves → Dashboard can WRITE ✓</li>
            <li>If 403 error → Dashboard can't WRITE ✗</li>
        </ol>
    </div>
    
    <div class="box">
        <h2>6. Frontend Connection Test</h2>
        <p>Open browser console (F12) on your homepage and check if you see:</p>
        <ul>
            <li>"Error loading services" → API not connected</li>
            <li>"Error loading articles" → API not connected</li>
            <li>No errors → Connected successfully!</li>
        </ul>
    </div>
    
    <div class="box" style="background: #fff3cd;">
        <h2>⚠️ Common Issues</h2>
        <p><b>If diagnostic.php doesn't work:</b> Usually means config.php has an error</p>
        <p><b>If dashboard can't write:</b> AJAX files have session/auth issues</p>
        <p><b>If frontend doesn't show API data:</b> Need to upload new index.html with dynamic loader</p>
    </div>
    
    <div class="box" style="background: #d4edda;">
        <h2>✅ What To Do Next</h2>
        <ol>
            <li><b>Upload new index.html</b> (has dynamic loader now)</li>
            <li><b>Add services in admin</b> (they will show on frontend)</li>
            <li><b>Add articles in admin</b> (they will show on frontend)</li>
            <li><b>Update settings in admin</b> (they will show on frontend)</li>
        </ol>
    </div>
</body>
</html>