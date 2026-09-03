<?php 
session_start();
include "db.php";

$orderId = $_SESSION['maDonHang'] ?? 0;

if ($orderId == 0) {
    die("Không tìm thấy đơn hàng");
}

/* LẤY TỔNG TIỀN TỪ DB*/
$stmt = $conn->prepare("SELECT tongTien FROM donhang WHERE maDonHang = ?");
$stmt->bind_param("i", $orderId);
$stmt->execute();
$stmt->bind_result($amount);
$stmt->fetch();
$stmt->close();

if (!$amount) {
    $amount = 0;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>VNPAY - Thanh toán</title>
<link rel="stylesheet" href="assets/css/fake_vnpay.css">
</head>

<body>

<!-- STEP 1 -->
<div class="wrapper" id="step1">
    <div class="header">
        <img src="./assets/img/vnpay.jpg" height="30"><br>
        Cổng thanh toán VNPAY
    </div>

    <div class="section">
        <div class="info">
            <span>Mã đơn hàng</span>
            <b>#<?= $orderId ?></b>
        </div>
        <div class="info">
            <span>Số tiền</span>
            <div class="amount"><?= number_format($amount) ?> VNĐ</div>
        </div>

        <hr>

        <h4>Chọn ngân hàng</h4>

        <div class="banks">
            <div class="bank" onclick="selectBank(this,'VCB')">
                <img src="./assets/img/vietcombank.jpg">
                <span>Vietcombank</span>
            </div>
            <div class="bank" onclick="selectBank(this,'BIDV')">
                <img src="./assets/img/bidv.jpg">
                <span>BIDV</span>
            </div>
            <div class="bank" onclick="selectBank(this,'TECOMBANK')">
                <img src="./assets/img/tecombank.jpg">
                <span>TECOMBANK</span>
            </div>
            <div class="bank" onclick="selectBank(this,'ACB')">
                <img src="./assets/img/acb.png">
                <span>ACB</span>
            </div>
            <div class="bank" onclick="selectBank(this,'MB BANK')">
                <img src="./assets/img/mb.png">
                <span>MB BANK</span>
            </div>
            <div class="bank" onclick="selectBank(this,'VPBANK')">
                <img src="./assets/img/vp.jpg">
                <span>VP BANK</span>
            </div>
            <div class="bank" onclick="selectBank(this,'TPBANK')">
                <img src="./assets/img/tp.png">
                <span>TP BANK</span>
            </div>
            <div class="bank" onclick="selectBank(this,'SHB')">
                <img src="./assets/img/shb.jpg">
                <span>SHB</span>
            </div>
        </div>

        <button onclick="nextLogin()">Tiếp tục</button>
        <button class="btn-back" onclick="goBack()">⬅ Quay lại</button>
    </div>
</div>

<!-- STEP 2 -->
<div class="wrapper" id="step2" style="display:none">
    <div class="header">Đăng nhập Internet Banking</div>
    <div class="section">
        <input type="text" id="account" placeholder="Số tài khoản">
        <input type="password" id="password" placeholder="Mật khẩu">
        <div id="loginError" style="color:red;font-size:14px;margin-top:8px;"></div>
        <button onclick="nextOTP()">Đăng nhập</button>
        <button class="btn-back" onclick="backToBank()">⬅ Quay lại</button>
    </div>
</div>

<!-- STEP 3 -->
<div class="wrapper" id="step3" style="display:none">
    <div class="header">Xác thực OTP</div>
    <div class="section">
        <p>Mã OTP đã gửi đến điện thoại của bạn</p>
        <input placeholder="Nhập OTP (demo: 123456)">
        <button onclick="pay()">Xác nhận</button>
    </div>
</div>

<!-- LOADING -->
<div class="wrapper loading" id="loading">
    <div class="spinner"></div>
    <p>Đang xử lý giao dịch...</p>
</div>

<script>
let bankSelected = null;
let loginFail = 0;

function selectBank(el, code){
    document.querySelectorAll('.bank').forEach(b=>b.classList.remove('active'));
    el.classList.add('active');
    bankSelected = code;
}

function nextLogin(){
    if(!bankSelected){
        alert("Vui lòng chọn ngân hàng");
        return;
    }
    step1.style.display="none";
    step2.style.display="block";
}

function backToBank(){
    document.getElementById("account").value = "";
    document.getElementById("password").value = "";
    document.getElementById("password").disabled = false;
    document.getElementById("loginError").innerHTML = "";
    loginFail = 0;

    step2.style.display="none";
    step1.style.display="block";
}

function nextOTP(){
    let acc=document.getElementById("account").value;
    let pass=document.getElementById("password").value;
    let errorBox=document.getElementById("loginError");

    if(acc==""||pass==""){
        errorBox.innerHTML="Vui lòng nhập đầy đủ thông tin.";
        return;
    }

    if(pass!=="123456"){
        loginFail++;
        if(loginFail<3){
            errorBox.innerHTML="Sai mật khẩu. Bạn còn "+(3-loginFail)+" lần thử.";
        }else{
            errorBox.innerHTML="Tài khoản bị khóa do nhập sai 3 lần.";
            document.getElementById("password").disabled=true;
        }
        return;
    }

    loginFail=0;
    errorBox.innerHTML="";
    step2.style.display="none";
    step3.style.display="block";
}

function pay(){
    step3.style.display="none";
    loading.style.display="block";

    setTimeout(()=>{
        window.location =
        "fake_vnpay_return.php"+
        "?vnp_ResponseCode=00"+
        "&vnp_TxnRef=<?= $orderId ?>"+
        "&vnp_Amount=<?= $amount*100 ?>"+
        "&vnp_BankCode="+bankSelected;
    },2000);
}

/* QUAY LẠI HỦY ĐƠN */
function goBack(){
    if(confirm("Bạn có chắc muốn hủy thanh toán?")){
window.location.href="cancel_vnpay.php?vnp_TxnRef=<?= $orderId ?>";    }
}
</script>

</body>
</html>