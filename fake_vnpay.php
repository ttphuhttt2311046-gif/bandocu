<?php
session_start();
$orderId = $_SESSION['maDonHang'] ?? 0;
$amount  = $_SESSION['fake_amount'] ?? 0;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>VNPAY - Thanh toán</title>

<style>
*{box-sizing:border-box;font-family:Segoe UI,Roboto,Arial}
body{
    margin:0;
    background:#f2f4f8;
}
.wrapper{
    max-width:460px;
    margin:40px auto;
    background:#fff;
    border-radius:12px;
    box-shadow:0 10px 30px rgba(0,0,0,.12);
    overflow:hidden;
}
.header{
    background:#005baa;
    padding:20px;
    color:#fff;
    text-align:center;
}
.header img{
    height:34px;
    margin-bottom:8px;
}
.section{
    padding:20px;
}
.info{
    display:flex;
    justify-content:space-between;
    margin-bottom:10px;
    font-size:15px;
}
.amount{
    font-size:22px;
    font-weight:700;
    color:#005baa;
    text-align:right;
}
.banks{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:12px;
    margin-top:15px;
}
.bank{
    border:2px solid #e5e7eb;
    border-radius:10px;
    padding:12px;
    cursor:pointer;
    display:flex;
    align-items:center;
    gap:10px;
    transition:.2s;
}
.bank img{
    width:36px;
}
.bank span{
    font-weight:600;
}
.bank.active{
    border-color:#005baa;
    background:#f0f6ff;
}
button{
    width:100%;
    padding:14px;
    background:#005baa;
    color:#fff;
    border:none;
    border-radius:10px;
    font-size:16px;
    font-weight:600;
    margin-top:20px;
    cursor:pointer;
}
button:hover{opacity:.9}

.loading{
    text-align:center;
    padding:40px;
    display:none;
}
.spinner{
    width:42px;
    height:42px;
    border:4px solid #e5e7eb;
    border-top:4px solid #005baa;
    border-radius:50%;
    margin:0 auto 15px;
    animation:spin 1s linear infinite;
}
@keyframes spin{100%{transform:rotate(360deg)}}

.otp input{
    width:100%;
    padding:12px;
    font-size:18px;
    text-align:center;
    border-radius:8px;
    border:1px solid #ccc;
    margin:15px 0;
}
.note{
    font-size:13px;
    color:#6b7280;
}
</style>
</head>

<body>

<!-- STEP 1 -->
<div class="wrapper" id="step1">
    <div class="header">
        <img src="assets/img/unnamed.png">
        <div>Cổng thanh toán VNPAY</div>
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
                <img src="https://i.imgur.com/2ISgYja.png">
                <span>Vietcombank</span>
            </div>
            <div class="bank" onclick="selectBank(this,'BIDV')">
                <img src="assets/img/thumbnail-logo-BIDV.jpg">
                <span>BIDV</span>
            </div>
            <div class="bank" onclick="selectBank(this,'TCB')">
                <img src="assets/img/logo-techcombank-dongphucvina.vn_.png">
                <span>Techcombank</span>
            </div>
            <div class="bank" onclick="selectBank(this,'ACB')">
                <img src="assets/img/y-nghia-logo-acb-1.jpg">
                <span>ACB</span>
            </div>
        </div>

        <button onclick="nextOTP()">Tiếp tục thanh toán</button>
    </div>
</div>

<!-- STEP 2 OTP -->
<div class="wrapper" id="step2" style="display:none">
    <div class="header">Xác thực OTP</div>
    <div class="section otp">
        <p>Mã OTP đã được gửi đến số điện thoại của bạn</p>
        <input placeholder="Nhập OTP (demo: 123456)">
        <button onclick="pay()">Xác nhận</button>
        <p class="note">* Đây là môi trường mô phỏng VNPay Sandbox</p>
    </div>
</div>

<!-- LOADING -->
<div class="wrapper loading" id="loading">
    <div class="spinner"></div>
    <p>Đang xử lý giao dịch, vui lòng chờ...</p>
</div>

<script>
let bankSelected = null;

function selectBank(el, code){
    document.querySelectorAll('.bank').forEach(b=>b.classList.remove('active'));
    el.classList.add('active');
    bankSelected = code;
}

function nextOTP(){
    if(!bankSelected){
        alert("Vui lòng chọn ngân hàng");
        return;
    }
    document.getElementById("step1").style.display="none";
    document.getElementById("step2").style.display="block";
}

function pay(){
    document.getElementById("step2").style.display="none";
    document.getElementById("loading").style.display="block";

    setTimeout(()=>{
        window.location =
        "fake_vnpay_return.php"+
        "?vnp_ResponseCode=00"+
        "&vnp_TxnRef=<?= $orderId ?>"+
        "&vnp_Amount=<?= $amount*100 ?>"+
        "&vnp_BankCode="+bankSelected;
    }, 2500);
}
</script>

</body>
</html>
