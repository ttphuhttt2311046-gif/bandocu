<?php
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trung tâm hỗ trợ - Shop Đồ Cũ</title>
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
        
        /* Main Help Center Hero */
        .help-hero {
            background: linear-gradient(135deg, #2e7d32, #43a047);
            color: white;
            text-align: center;
            padding: 40px 20px;
        }
        .help-hero h2 { font-size: 28px; margin-bottom: 15px; }
        
        /* Search Bar */
        .search-box {
            max-width: 600px;
            margin: 0 auto;
            position: relative;
        }
        .search-box input {
            width: 100%;
            padding: 12px 20px;
            font-size: 16px;
            border: none;
            border-radius: 25px;
            outline: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .search-suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            color: #333;
            margin-top: 5px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            display: none;
            z-index: 10;
            max-height: 200px;
            overflow-y: auto;
            text-align: left;
        }
        .search-suggestions div {
            padding: 10px 15px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
        }
        .search-suggestions div:hover {
            background-color: #f1f8e9;
            color: #2e7d32;
        }

        /* Container & Content layout */
        .help-container {
            max-width: 1100px;
            margin: 30px auto;
            display: flex;
            gap: 30px;
            padding: 0 20px;
        }
        
        /* Sidebar Tabs */
        .help-sidebar {
            width: 320px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            padding: 20px;
            height: fit-content;
        }
        .help-sidebar h3 {
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

        /* Content Area (noidungttht.php loader) */
        .help-content {
            flex: 1;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            padding: 30px;
            min-height: 400px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Shop Đồ Cũ</h1>
        <div class="header-nav">
            <a href="../index.php">Trang chủ</a>
            <a href="#">Xin chào, NguyenVan</a>
        </div>
    </div>

    <div class="help-hero">
        <h2>Trung tâm hỗ trợ</h2>
        <p style="margin-bottom: 20px; font-size: 16px;">Xin chào, chúng tôi có thể giúp gì cho bạn?</p>
        
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Nhập từ khóa cần hỗ trợ (ví dụ: bảo mật, thanh toán, đổi trả...)" onkeyup="filterSearch(this.value)">
            <div id="searchSuggestions" class="search-suggestions"></div>
        </div>
    </div>

    <div class="help-container">
        <div class="help-sidebar">
            <h3>Câu hỏi thường gặp</h3>
            <button class="tab-btn active" onclick="loadContent('baohomat', this)">1. Tài khoản & Bảo mật thông tin</button>
            <button class="tab-btn" onclick="loadContent('thanhtoan', this)">2. Hướng dẫn thanh toán an toàn</button>
            <button class="tab-btn" onclick="loadContent('doitra', this)">3. Chính sách đổi trả & hoàn tiền</button>
            <button class="tab-btn" onclick="loadContent('giaodich', this)">4. Xử lý tranh chấp mua bán</button>
        </div>

        <div class="help-content" id="noidungArea">
            </div>
    </div>

    <script>
        // Dữ liệu câu hỏi hỗ trợ để phục vụ tìm kiếm thông minh
        const searchData = [
            { keyword: "bảo mật", title: "Tài khoản & Bảo mật thông tin", id: "baohomat" },
            { keyword: "mật khẩu", title: "Tài khoản & Bảo mật thông tin", id: "baohomat" },
            { keyword: "thanh toán", title: "Hướng dẫn thanh toán an toàn", id: "thanhtoan" },
            { keyword: "chuyển khoản", title: "Hướng dẫn thanh toán an toàn", id: "thanhtoan" },
            { keyword: "đổi trả", title: "Chính sách đổi trả & hoàn tiền", id: "doitra" },
            { keyword: "hoàn tiền", title: "Chính sách đổi trả & hoàn tiền", id: "doitra" },
            { keyword: "tranh chấp", title: "Xử lý tranh chấp mua bán", id: "giaodich" },
            { keyword: "lừa đảo", title: "Xử lý tranh chấp mua bán", id: "giaodich" }
        ];

        // Hàm tải nội dung không chuyển trang sử dụng AJAX gọi tới noidungttht.php
        function loadContent(id, element) {
            // Đổi class active trên sidebar
            let buttons = document.querySelectorAll('.tab-btn');
            buttons.widgets = buttons.forEach(btn => btn.classList.remove('active'));
            if(element) {
                element.classList.add('active');
            } else {
                buttons.forEach(btn => {
                    if(btn.getAttribute('onclick').includes(id)) {
                        btn.classList.add('active');
                    }
                });
            }

            // Gọi AJAX lấy nội dung từ noidungttht.php
            let xhr = new XMLHttpRequest();
            xhr.open("GET", "noidungttht.php?id=" + id, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.getElementById("noidungArea").innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        // Tải mặc định nội dung đầu tiên khi mở trang
        window.onload = function() {
            loadContent('baohomat', document.querySelector('.tab-btn'));
        };

        // Hàm xử lý tìm kiếm thông minh (hiện danh sách gợi ý khi gõ)
        function filterSearch(keyword) {
            let suggestionsBox = document.getElementById("searchSuggestions");
            if(keyword.trim() === "") {
                suggestionsBox.style.display = "none";
                return;
            }

            let filtered = searchData.filter(item => item.keyword.toLowerCase().includes(keyword.toLowerCase()) || item.title.toLowerCase().includes(keyword.toLowerCase()));
            
            let uniqueItems = [];
            let mapIds = new Map();
            for (let item of filtered) {
                if(!mapIds.has(item.id)){
                    mapIds.set(item.id, true);
                    uniqueItems.push(item);
                }
            }

            if(uniqueItems.length > 0) {
                let html = "";
                uniqueItems.forEach(item => {
                    html += `<div onclick="selectSearchItem('${item.id}', '${item.title}')">🔍 ${item.title} (Từ khóa: <b>${item.keyword}</b>)</div>`;
                });
                suggestionsBox.innerHTML = html;
                suggestionsBox.style.display = "block";
            } else {
                suggestionsBox.innerHTML = `<div style="color: #888;">Không tìm thấy kết quả phù hợp</div>`;
                suggestionsBox.style.display = "block";
            }
        }

        // Chọn một mục từ gợi ý tìm kiếm để load thẳng nội dung không chuyển trang
        function selectSearchItem(id, title) {
            document.getElementById("searchInput").value = title;
            document.getElementById("searchSuggestions").style.display = "none";
            loadContent(id, null);
        }

        // Đóng hộp gợi ý khi click ra ngoài
        document.addEventListener('click', function(e) {
            if (!document.querySelector('.search-box').contains(e.target)) {
                document.getElementById('searchSuggestions').style.display = 'none';
            }
        });
    </script>
</body>
</html>