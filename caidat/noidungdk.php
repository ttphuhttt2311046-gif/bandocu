<?php
$id = isset($_GET['id']) ? $_GET['id'] : 'dieukhoanchung';

switch ($id) {
    case 'dieukhoanchung':
        echo '
            <h2 style="color: #2e7d32; margin-bottom: 15px; font-size: 20px;">1. Điều khoản chung & Đăng ký tài khoản</h2>
            <p style="line-height: 1.6; margin-bottom: 12px; color: #555;">Chào mừng bạn đến với nền tảng giao dịch <b>Shop Đồ Cũ</b>. Khi truy cập và sử dụng website của chúng tôi, bạn đồng ý tuân thủ các điều khoản sau:</p>
            <ul style="padding-left: 20px; line-height: 1.8; color: #444;">
                <li><b>Độ tuổi hợp pháp:</b> Người sử dụng phải từ 18 tuổi trở lên hoặc có sự giám sát của người đại diện hợp pháp theo quy định pháp luật Việt Nam.</li>
                <li><b>Bảo mật tài khoản:</b> Bạn chịu hoàn toàn trách nhiệm trong việc bảo mật thông tin tài khoản, mật khẩu và mọi hoạt động diễn ra dưới tên đăng nhập của mình.</li>
                <li><b>Thông tin chính xác:</b> Khi đăng ký, người dùng phải cung cấp thông tin chuẩn xác (số điện thoại, họ tên, địa chỉ nhận hàng). Mọi tổn thất do khai báo sai lệch sẽ do người dùng chịu trách nhiệm.</li>
            </ul>
        ';
        break;

    case 'quyđinhmuaban':
        echo '
            <h2 style="color: #2e7d32; margin-bottom: 15px; font-size: 20px;">2. Quy định mua bán và đăng tin sản phẩm cũ</h2>
            <p style="line-height: 1.6; margin-bottom: 12px; color: #555;">Nền tảng đóng vai trò trung gian kết nối người mua và người bán đồ đã qua sử dụng. Các nguyên tắc giao dịch bao gồm:</p>
            <ul style="padding-left: 20px; line-height: 1.8; color: #444;">
                <li><b>Mô tả trung thực:</b> Người bán (Shop) bắt buộc phải phản ánh đúng tình trạng thực tế của sản phẩm cũ (độ mới %, vết trầy xước, lỗi kỹ thuật nếu có).</li>
                <li><b>Giá cả minh bạch:</b> Giá niêm yết trên sản phẩm đã bao gồm các khoản phí cơ bản hoặc phải được thỏa thuận rõ ràng trước khi xác nhận đơn hàng.</li>
                <li><b>Quyền kiểm hàng:</b> Người mua có quyền kiểm tra ngoại quan sản phẩm theo chính sách giao nhận chung trước khi thanh toán tiền nhận hàng hoàn tất.</li>
            </ul>
        ';
        break;

    case 'hanhvivipham':
        echo '
            <h2 style="color: #2e7d32; margin-bottom: 15px; font-size: 20px;">3. Các hành vi nghiêm cấm trên hệ thống</h2>
            <p style="line-height: 1.6; margin-bottom: 15px; color: #555;">Nhằm xây dựng môi trường thương mại lành mạnh, nghiêm cấm các hành vi sau đây:</p>
            <ul style="padding-left: 20px; line-height: 1.8; color: #444;">
                <li><b>Kinh doanh hàng cấm:</b> Đăng bán vũ khí, chất kích thích, tài sản trộm cắp, hàng giả, hàng nhái thương hiệu hoặc các mặt hàng vi phạm pháp luật nhà nước.</li>
                <li><b>Lừa đảo chiếm đoạt:</b> Cố tình nhận tiền cọc/chuyển khoản trước nhưng không giao hàng, hoặc giao sản phẩm khác xa hoàn toàn mô tả cam kết.</li>
                <li><b>Thao túng đánh giá:</b> Sử dụng tài khoản ảo để tự đánh giá 5 sao hoặc bôi nhọ, phá hoại uy tín của các shop khác trên hệ thống.</li>
            </ul>
        ';
        break;

    case 'giaquyetchanhchap':
        echo '
            <h2 style="color: #2e7d32; margin-bottom: 15px; font-size: 20px;">4. Giải quyết tranh chấp & Miễn trừ trách nhiệm</h2>
            <p style="line-height: 1.6; margin-bottom: 15px; color: #555;">Quy định về xử lý khiếu nại và giới hạn trách nhiệm pháp lý của Ban quản trị:</p>
            <ul style="padding-left: 20px; line-height: 1.8; color: #444;">
                <li><b>Quyền can thiệp của Admin:</b> Ban quản trị có toàn quyền khóa vĩnh viễn tài khoản, gỡ bỏ sản phẩm vi phạm mà không cần thông báo trước nếu phát hiện gian lận.</li>
                <li><b>Miễn trừ trách nhiệm giao dịch ngoài:</b> Chúng tôi không chịu trách nhiệm pháp lý đối với các giao dịch thực hiện ngoài luồng hệ thống (giao dịch trực tiếp qua Zalo, Facebook, chuyển khoản riêng không qua cổng trung gian website).</li>
                <li><b>Hiệu lực điều khoản:</b> Các điều khoản này có thể được cập nhật định kỳ và có hiệu lực ngay khi đăng tải công khai trên website.</li>
            </ul>
        ';
        break;

    default:
        echo '<p>Không tìm thấy nội dung điều khoản.</p>';
        break;
}
?>