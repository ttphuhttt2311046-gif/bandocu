<?php
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Điều khoản dịch vụ - Shop Đồ Cũ</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: Arial, sans-serif; }
        body { background-color: #f4f6f8; color: #333; }
        
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
        
        /* Main Hero Banner */
        .terms-hero {
            background: linear-gradient(135deg, #2e7d32, #43a047);
            color: white;
            text-align: center;
            padding: 40px 20px;
        }
        .terms-hero h2 { font-size: 28px; margin-bottom: 10px; }
        .terms-hero p { font-size: 15px; opacity: 0.9; }

        /* Container & Content layout */
        .terms-container {
            max-width: 1100px;
            margin: 30px auto;
            display: flex;
            gap: 30px;
            padding: 0 20px;
        }
        
        /* Sidebar Tabs */
        .terms-sidebar {
            width: 320px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            padding: 20px;
            height: fit-content;
        }
        .terms-sidebar h3 {
            font-size: 16px;
            color: #2e7d32;
            margin-bottom: 15px;
            border-bottom: 2px solid #e8f5e9;
            padding-bottom: 8px;
        }
        .tab-btn {
            display: block;
            width: 100%;
            padding: 12px 15px;
            text-align: left;
            background: none;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            color: #555;
            cursor: pointer;
            transition: all 0.2s;
            margin-bottom: 5px;
        }
        .tab-btn:hover {
            background-color: #f1f8e9;
            color: #2e7d32;
        }
        .tab-btn.active {
            background-color: #2e7d32;
            color: white;
            font-weight: bold;
        }

        /* Content Area (noidungdk.php loader) */
        .terms-content {
            flex: 1;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            padding: 30px;
            min-height: 450px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Shop Đồ Cũ</h1>
        <div class="header-nav">
            <a href="../index.php">Trang chủ</a>
            <a href="trungtamhotro.php">Hỗ trợ</a>
            <a href="#">Xin chào, NguyenVan</a>
        </div>
    </div>

    <div class="terms-hero">
        <h2>Điều khoản dịch vụ</h2>
        <p>Quy định pháp lý và thỏa thuận sử dụng nền tảng thương mại điện tử Shop Đồ Cũ</p>
    </div>

    <div class="terms-container">
        <!-- Sidebar danh mục điều khoản -->
        <div class="terms-sidebar">
            <h3>Danh mục điều khoản</h3>
            <button class="tab-btn active" onclick="loadTerms('dieukhoanchung', this)">1. Điều khoản chung & Tài khoản</button>
            <button class="tab-btn" onclick="loadTerms('quyđinhmuaban', this)">2. Quy định mua bán đồ cũ</button>
            <button class="tab-btn" onclick="loadTerms('hanhvivipham', this)">3. Các hành vi nghiêm cấm</button>
            <button class="tab-btn" onclick="loadTerms('giaquyetchanhchap', this)">4. Giải quyết tranh chấp & Miễn trừ</button>
        </div>

        <!-- Khu vực hiển thị nội dung chi tiết không chuyển trang qua noidungdk.php -->
        <div class="terms-content" id="noidungdkArea">
            <!-- Tải nội dung mặc định -->
        </div>
    </div>

    <script>
        // Hàm tải nội dung điều khoản bằng AJAX không chuyển trang
        function loadTerms(id, element) {
            let buttons = document.querySelectorAll('.tab-btn');
            buttons.forEach(btn => btn.classList.remove('active'));
            if(element) {
                element.classList.add('active');
            } else {
                buttons.forEach(btn => {
                    if(btn.getAttribute('onclick').includes(id)) {
                        btn.classList.add('active');
                    }
                });
            }

            let xhr = new XMLHttpRequest();
            xhr.open("GET", "noidungdk.php?id=" + id, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.getElementById("noidungdkArea").innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        // Tải mặc định mục đầu tiên khi mở trang
        window.onload = function() {
            loadTerms('dieukhoanchung', document.querySelector('.tab-btn'));
        };
    </script>
</body>
</html>