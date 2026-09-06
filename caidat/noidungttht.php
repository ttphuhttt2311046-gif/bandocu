<?php
$id = isset($_GET['id']) ? $_GET['id'] : 'baohomat';

switch ($id) {
    case 'baohomat':
        echo '
            <h2 style="color: #2e7d32; margin-bottom: 15px; font-size: 20px;">1. Tài khoản & Bảo mật thông tin</h2>
            <p style="line-height: 1.6; margin-bottom: 15px; color: #555;">Để đảm bảo an toàn tuyệt đối cho tài khoản của bạn trên <b>Shop Đồ Cũ</b>, vui lòng tuân thủ các quy tắc bảo mật sau:</p>
            <ul style="padding-left: 20px; line-height: 1.8; color: #444;">
                <li><b>Không chia sẻ mật khẩu:</b> Tuyệt đối không cung cấp mật khẩu hoặc mã OTP xác thực cho bất kỳ ai, kể cả nhân viên giả mạo hỗ trợ.</li>
                <li><b>Cập nhật thông tin:</b> Thường xuyên thay đổi mật khẩu định kỳ 3-6 tháng/lần.</li>
                <li><b>Xác thực tài khoản:</b> Sử dụng số điện thoại chính chủ để đăng ký tài khoản nhằm dễ dàng khôi phục khi gặp sự cố.</li>
            </ul>
        ';
        break;

    case 'thanhtoan':
        echo '
            <h2 style="color: #2e7d32; margin-bottom: 15px; font-size: 20px;">2. Hướng dẫn thanh toán an toàn</h2>
            <p style="line-height: 1.6; margin-bottom: 15px; color: #555;">Giao dịch đồ cũ tiềm ẩn rủi ro nếu không thực hiện đúng quy trình của hệ thống:</p>
            <ul style="padding-left: 20px; line-height: 1.8; color: #444;">
                <li><b>Thanh toán qua nền tảng:</b> Khuyến khích sử dụng các cổng thanh toán hoặc dịch vụ trung gian do Shop Đồ Cũ hỗ trợ để được bảo vệ quyền lợi.</li>
                <li><b>Giao dịch trực tiếp (COD/Tiền mặt):</b> Nếu gặp trực tiếp kiểm tra hàng, hãy chọn nơi công cộng, sáng sủa và kiểm tra kỹ tình trạng sản phẩm trước khi đưa tiền.</li>
                <li><b>Tránh chuyển khoản riêng:</b> Không nên chuyển khoản 100% trước cho các shop có dấu hiệu bất minh khi chưa xác thực uy tín.</li>
            </ul>
        ';
        break;

    case 'doitra':
        echo '
            <h2 style="color: #2e7d32; margin-bottom: 15px; font-size: 20px;">3. Chính sách đổi trả & hoàn tiền</h2>
            <p style="line-height: 1.6; margin-bottom: 15px; color: #555;">Do đặc thù là sản phẩm đồ đã qua sử dụng, chính sách đổi trả áp dụng cụ thể như sau:</p>
            <ul style="padding-left: 20px; line-height: 1.8; color: #444;">
                <li><b>Thời gian khiếu nại:</b> Trong vòng <b>24 giờ</b> kể từ khi nhận hàng thành công trên hệ thống.</li>
                <li><b>Điều kiện đổi trả:</b> Sản phẩm nhận được khác biệt hoàn toàn so với mô tả (sai mẫu mã, hỏng hóc nặng không đúng như cam kết ban đầu của người bán).</li>
                <li><b>Bằng chứng yêu cầu:</b> Cần cung cấp video quay lại quá trình khui hàng (unboxing) rõ nét làm căn cứ giải quyết.</li>
            </ul>
        ';
        break;

    case 'giaodich':
        echo '
            <h2 style="color: #2e7d32; margin-bottom: 15px; font-size: 20px;">4. Xử lý tranh chấp mua bán</h2>
            <p style="line-height: 1.6; margin-bottom: 15px; color: #555;">Khi phát sinh mâu thuẫn giữa người mua và shop bán đồ cũ, Ban quản trị sẽ hỗ trợ giải quyết:</p>
            <ul style="padding-left: 20px; line-height: 1.8; color: #444;">
                <li><b>Bước 1:</b> Gửi yêu cầu khiếu nại hoặc bấm nút "Báo cáo" trực tiếp tại trang shop hoặc trang chi tiết sản phẩm.</li>
                <li><b>Bước 2:</b> Cung cấp đầy đủ hình ảnh đoạn chat, hóa đơn hoặc video chứng minh.</li>
                <li><b>Bước 3:</b> Ban quản trị xem xét và đưa ra quyết định xử lý (khóa tài khoản shop vi phạm, hoàn tiền hoặc hỗ trợ trung gian).</li>
            </ul>
        ';
        break;

    default:
        echo '<p>Không tìm thấy nội dung hỗ trợ.</p>';
        break;
}
?>