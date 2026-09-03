<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Chat AI</title>

<style>
#chatbox {
    width: 420px;
    height: 500px;
    border: 1px solid #ccc;
    overflow-y: auto;
    padding: 10px;
    font-family: Arial;
}
.msg { margin: 8px 0; }
.user { text-align: right; color: blue; }
.bot { text-align: left; color: green; }
</style>
</head>

<body>

<h3>🤖 AI hỗ trợ sản phẩm</h3>

<div id="chatbox"></div>

<input id="msg" placeholder="Hỏi sản phẩm..." style="width:75%">
<button onclick="send()">Gửi</button>

<script>
const chatbox = document.getElementById("chatbox");

async function send() {
    let input = document.getElementById("msg");
    let message = input.value.trim();
    if (!message) return;

    chatbox.innerHTML += `<div class="msg user">Bạn: ${message}</div>`;
    input.value = "";

    chatbox.innerHTML += `<div class="msg bot">AI đang trả lời...</div>`;

    let res = await fetch("chat_ai_api.php", {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify({message})
    });

    let data = await res.json();

    chatbox.lastChild.remove();

    chatbox.innerHTML += `<div class="msg bot">AI: ${data.reply}</div>`;
    chatbox.scrollTop = chatbox.scrollHeight;
}

document.getElementById("msg").addEventListener("keydown", e => {
    if (e.key === "Enter") send();
});
</script>

</body>
</html>