<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - خطأ في الخادم</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Cairo', sans-serif;
            background: #1a1a2e;
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
        
        .robot {
            width: 200px;
            height: 240px;
            margin: 0 auto 40px;
            position: relative;
            animation: malfunction 2s infinite;
        }
        
        .robot-head {
            width: 120px;
            height: 100px;
            background: linear-gradient(135deg, #c5a47e 0%, #a88a65 100%);
            border-radius: 20px;
            margin: 0 auto;
            position: relative;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        
        .antenna {
            width: 4px;
            height: 30px;
            background: #c5a47e;
            position: absolute;
            top: -30px;
            left: 50%;
            transform: translateX(-50%);
        }
        
        .antenna::after {
            content: '';
            width: 12px;
            height: 12px;
            background: #e74c3c;
            border-radius: 50%;
            position: absolute;
            top: -6px;
            left: 50%;
            transform: translateX(-50%);
            animation: blink 1s infinite;
        }
        
        .eyes {
            display: flex;
            justify-content: space-around;
            padding: 25px 20px 0;
        }
        
        .eye {
            width: 20px;
            height: 20px;
            background: #08137b;
            border-radius: 50%;
            position: relative;
            animation: eye-error 0.5s infinite;
        }
        
        .eye::after {
            content: 'X';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #e74c3c;
            font-weight: 700;
            font-size: 18px;
        }
        
        .mouth {
            width: 60px;
            height: 4px;
            background: #08137b;
            margin: 15px auto;
            border-radius: 2px;
            animation: mouth-error 1s infinite;
        }
        
        .robot-body {
            width: 140px;
            height: 120px;
            background: linear-gradient(135deg, #4f09a7 0%, #08137b 100%);
            border-radius: 10px;
            margin: 10px auto 0;
            position: relative;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        
        .panel {
            width: 80px;
            height: 60px;
            background: rgba(197, 164, 126, 0.2);
            border: 2px solid #c5a47e;
            border-radius: 5px;
            margin: 15px auto;
            display: flex;
            flex-wrap: wrap;
            padding: 5px;
            gap: 5px;
        }
        
        .led {
            width: 8px;
            height: 8px;
            background: #e74c3c;
            border-radius: 50%;
            animation: led-flash 0.3s infinite;
        }
        
        h1 {
            font-size: 120px;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 0 5px 20px rgba(231, 76, 60, 0.5);
            color: #e74c3c;
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
            color: #1a1a2e;
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
        
        .glitch {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: repeating-linear-gradient(
                0deg,
                rgba(231, 76, 60, 0.05),
                rgba(231, 76, 60, 0.05) 2px,
                transparent 2px,
                transparent 4px
            );
            animation: glitch-scan 8s linear infinite;
            z-index: 1;
            pointer-events: none;
        }
        
        @keyframes malfunction {
            0%, 90%, 100% { transform: translateX(0); }
            92% { transform: translateX(-5px); }
            94% { transform: translateX(5px); }
            96% { transform: translateX(-3px); }
            98% { transform: translateX(3px); }
        }
        
        @keyframes blink {
            0%, 90%, 100% { opacity: 1; }
            95% { opacity: 0; }
        }
        
        @keyframes eye-error {
            0%, 90%, 100% { transform: scale(1); }
            95% { transform: scale(0.8); }
        }
        
        @keyframes mouth-error {
            0%, 100% { transform: scaleX(1); }
            50% { transform: scaleX(0.8); }
        }
        
        @keyframes led-flash {
            0%, 100% { opacity: 1; background: #e74c3c; }
            50% { opacity: 0.3; background: #c0392b; }
        }
        
        @keyframes glitch-scan {
            0% { transform: translateY(-100%); }
            100% { transform: translateY(100%); }
        }
        
        @media (max-width: 768px) {
            h1 { font-size: 80px; }
            h2 { font-size: 24px; }
            p { font-size: 16px; }
            .robot { width: 150px; height: 180px; }
            .robot-head { width: 90px; height: 75px; }
            .robot-body { width: 105px; height: 90px; }
            .btn { margin: 5px; font-size: 16px; padding: 12px 30px; }
        }
    </style>
</head>
<body>
    <div class="glitch"></div>
    
    <div class="container">
        <div class="robot">
            <div class="robot-head">
                <div class="antenna"></div>
                <div class="eyes">
                    <div class="eye"></div>
                    <div class="eye"></div>
                </div>
                <div class="mouth"></div>
            </div>
            <div class="robot-body">
                <div class="panel">
                    <div class="led"></div>
                    <div class="led"></div>
                    <div class="led"></div>
                    <div class="led"></div>
                    <div class="led"></div>
                    <div class="led"></div>
                    <div class="led"></div>
                    <div class="led"></div>
                </div>
            </div>
        </div>
        
        <h1>500</h1>
        <h2>عطل تقني مؤقت!</h2>
        <p>
            عذراً، يبدو أن هناك خطأ في الخادم.<br>
            نحن نعمل على إصلاح المشكلة، يرجى المحاولة لاحقاً.
        </p>
        <a href="javascript:location.reload()" class="btn">إعادة المحاولة</a>
        <a href="/admin/" class="btn">العودة للوحة التحكم</a>
    </div>
</body>
</html>
