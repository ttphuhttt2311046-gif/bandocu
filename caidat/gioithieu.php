<?php
// gioithieu.php
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giới thiệu - Shop Đồ Cũ</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: Arial, sans-serif; }
        body { background-color: #f4f6f8; color: #333; display: flex; flex-direction: column; min-height: 100vh; }
        
        /* Header style matching Shop Đồ Cũ */
        .header {
            background-color: #2e7d32;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 { font-size: 22px; }
        .header-nav a { color: white; text-decoration: none; margin-left: 20px; font-size: 14px; }
        
        /* Main Container centered */
        .main-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        /* Intro Card */
        .intro-card {
            background: white;
            padding: 40px 60px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            text-align: center;
            max-width: 450px;
            width: 100%;
        }

        /* Khung chứa logo chỉnh kích thước vừa vặn, bo tròn đẹp mắt */
        .intro-logo {
            width: 100px;
            height: 100px;
            background-color: #e8f5e9;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 20px auto;
            overflow: hidden; /* Cắt phần ảnh thừa ngoài khung tròn */
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            border: 2px solid #c8e6c9;
        }

        /* Thuộc tính ép hình ảnh nằm gọn trong khung tròn */
        .intro-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* Đảm bảo ảnh không bị méo tỉ lệ */
        }

        .intro-card h2 {
            font-size: 24px;
            color: #2e7d32;
            margin-bottom: 10px;
        }

        .intro-card .version {
            font-size: 14px;
            color: #666;
            background-color: #f1f8e9;
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            margin-top: 5px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Shop Đồ Cũ</h1>
        <div class="header-nav">
            <a href="../index.php">Trang chủ</a>
            <a href="trungtamhotro.php">Hỗ trợ</a>
            <a href="dieukhoan.php">Điều khoản</a>
        </div>
    </div>

    <div class="main-container">
        <div class="intro-card">
            <div class="intro-logo">
                <img src="../assets/img/LOGO.png" alt="Logo Shop Đồ Cũ">
            </div>
            <h2>Shop Đồ Cũ</h2>
            <div class="version">Phiên bản 2.0.1</div>
        </div>
    </div>

</body>
</html>