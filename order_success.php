<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt hàng thành công</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Segoe UI", Tahoma, sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #e3f2fd, #ffffff);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .success-container {
            width: 100%;
            padding: 20px;
        }

        .success-card {
            max-width: 420px;
            margin: auto;
            background: #fff;
            border-radius: 16px;
            padding: 35px 30px;
            text-align: center;
            box-shadow: 0 12px 30px rgba(0,0,0,0.15);
            animation: fadeIn 0.6s ease;
        }

        .icon {
            width: 72px;
            height: 72px;
            margin: 0 auto 20px;
            background: #2ecc71;
            color: #fff;
            font-size: 36px;
            font-weight: bold;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .success-card h2 {
            color: #2ecc71;
            margin-bottom: 10px;
        }

        .success-card p {
            color: #555;
            font-size: 15px;
            margin-bottom: 25px;
        }

        .actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
            min-width: 140px;
        }

        .btn.primary {
            background: #2ecc71;
            color: #fff;
        }

        .btn.primary:hover {
            background: #27ae60;
        }

        .btn.outline {
            border: 2px solid #2ecc71;
            color: #2ecc71;
            background: #fff;
        }

        .btn.outline:hover {
            background: #2ecc71;
            color: #fff;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

<div class="success-container">
    <div class="success-card">
        <div class="icon">✔</div>
        <h2>Đặt hàng thành công</h2>
        <p>Cảm ơn bạn đã mua hàng tại cửa hàng của chúng tôi.</p>

        <div class="actions">
            <a href="index.php" class="btn primary">🏠 Về trang chủ</a>
            <a href="orders.php" class="btn outline">📦 Xem đơn hàng</a>
        </div>
    </div>
</div>

</body>
</html>
