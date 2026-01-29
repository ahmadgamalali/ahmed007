<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - الصفحة غير موجودة</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Cairo', sans-serif;
            background: linear-gradient(135deg, #08137b 0%, #4f09a7 50%, #c5a47e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .container {
            text-align: center;
            color: white;
            padding: 40px;
            position: relative;
            z-index: 2;
        }
        
        .compass {
            width: 180px;
            height: 180px;
            margin: 0 auto 40px;
            position: relative;
            animation: float 3s ease-in-out infinite;
        }
        
        .compass-ring {
            width: 180px;
            height: 180px;
            border: 5px solid #c5a47e;
            border-radius: 50%;
            position: relative;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        }
        
        .compass-needle {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 10px;
            height: 120px;
            animation: spin 4s linear infinite;
        }
        
        .needle-north {
            width: 0;
            height: 0;
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-bottom: 60px solid #e74c3c;
            position: absolute;
            top: 0;
            left: 0;
        }
        
        .needle-south {
            width: 0;
            height: 0;
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top: 60px solid #ecf0f1;
            position: absolute;
            bottom: 0;
            left: 0;
        }
        
        .compass-center {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 20px;
            height: 20px;
            background: #c5a47e;
            border-radius: 50%;
            box-shadow: 0 0 10px rgba(197, 164, 126, 0.8);
        }
        
        .directions {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
        }
        
        .direction {
            position: absolute;
            font-size: 20px;
            font-weight: 700;
            color: #c5a47e;
        }
        
        .dir-n { top: 10px; left: 50%; transform: translateX(-50%); }
        .dir-s { bottom: 10px; left: 50%; transform: translateX(-50%); }
        .dir-e { right: 10px; top: 50%; transform: translateY(-50%); }
        .dir-w { left: 10px; top: 50%; transform: translateY(-50%); }
        
        h1 {
            font-size: 120px;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 0 5px 20px rgba(0,0,0,0.3);
        }
        
        h2 {
            font-size: 32px;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        p {
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 30px;
            opacity: 0.9;
        }
        
        .btn {
            display: inline-block;
            padding: 15px 40px;
            background: #c5a47e;
            color: #08137b;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 18px;
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px rgba(197, 164, 126, 0.4);
            margin: 0 10px;
        }
        
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(197, 164, 126, 0.6);
        }
        
        .stars {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }
        
        .star {
            position: absolute;
            width: 2px;
            height: 2px;
            background: white;
            border-radius: 50%;
            animation: twinkle 3s infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }
        
        @keyframes twinkle {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 1; }
        }
        
        @media (max-width: 768px) {
            h1 { font-size: 80px; }
            h2 { font-size: 24px; }
            p { font-size: 16px; }
            .compass { width: 120px; height: 120px; }
            .compass-ring { width: 120px; height: 120px; }
            .btn { margin: 5px; font-size: 16px; padding: 12px 30px; }
        }
    </style>
</head>
<body>
    <div class="stars" id="stars"></div>
    
    <div class="container">
        <div class="compass">
            <div class="compass-ring">
                <div class="directions">
                    <span class="direction dir-n">N</span>
                    <span class="direction dir-s">S</span>
                    <span class="direction dir-e">E</span>
                    <span class="direction dir-w">W</span>
                </div>
                <div class="compass-needle">
                    <div class="needle-north"></div>
                    <div class="needle-south"></div>
                </div>
                <div class="compass-center"></div>
            </div>
        </div>
        
        <h1>404</h1>
        <h2>تهنا في الطريق!</h2>
        <p>
            عذراً، الصفحة التي تبحث عنها غير موجودة.<br>
            ربما تم نقلها أو حذفها، أو ربما كتبت العنوان بشكل خاطئ.
        </p>
        <a href="/admin/" class="btn">لوحة التحكم</a>
        <a href="/" class="btn">الصفحة الرئيسية</a>
    </div>
    
    <script>
        // Create twinkling stars
        const starsContainer = document.getElementById('stars');
        for (let i = 0; i < 100; i++) {
            const star = document.createElement('div');
            star.className = 'star';
            star.style.left = Math.random() * 100 + '%';
            star.style.top = Math.random() * 100 + '%';
            star.style.animationDelay = Math.random() * 3 + 's';
            star.style.animationDuration = (Math.random() * 2 + 2) + 's';
            starsContainer.appendChild(star);
        }
    </script>
</body>
</html>
