<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - ممنوع</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Cairo', sans-serif;
            background: linear-gradient(135deg, #4f09a7 0%, #08137b 100%);
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
        
        .stop-sign {
            width: 150px;
            height: 150px;
            margin: 0 auto 30px;
            position: relative;
        }
        
        .octagon {
            width: 150px;
            height: 150px;
            background: #e74c3c;
            position: relative;
            clip-path: polygon(30% 0%, 70% 0%, 100% 30%, 100% 70%, 70% 100%, 30% 100%, 0% 70%, 0% 30%);
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse 2s infinite;
            box-shadow: 0 10px 40px rgba(231, 76, 60, 0.5);
        }
        
        .hand {
            font-size: 60px;
            animation: wave 1s infinite;
        }
        
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
        }
        
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(197, 164, 126, 0.6);
        }
        
        .warning-lines {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            opacity: 0.1;
            background: repeating-linear-gradient(
                45deg,
                transparent,
                transparent 50px,
                rgba(255, 255, 255, 0.5) 50px,
                rgba(255, 255, 255, 0.5) 100px
            );
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        @keyframes wave {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-10deg); }
            75% { transform: rotate(10deg); }
        }
        
        @media (max-width: 768px) {
            h1 { font-size: 80px; }
            h2 { font-size: 24px; }
            p { font-size: 16px; }
            .stop-sign { width: 100px; height: 100px; }
            .octagon { width: 100px; height: 100px; }
            .hand { font-size: 40px; }
        }
    </style>
</head>
<body>
    <div class="warning-lines"></div>
    
    <div class="container">
        <div class="stop-sign">
            <div class="octagon">
                <div class="hand">✋</div>
            </div>
        </div>
        
        <h1>403</h1>
        <h2>وقفة! ممنوع المرور</h2>
        <p>
            عذراً، لا تملك الصلاحيات اللازمة للوصول إلى هذه الصفحة.<br>
            إذا كنت تعتقد أن هذا خطأ، يرجى التواصل مع المسؤول.
        </p>
        <a href="/admin/" class="btn">العودة للوحة التحكم</a>
    </div>
</body>
</html>
