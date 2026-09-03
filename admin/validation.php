<?php

function clean($value)
{
    return htmlspecialchars(
        strip_tags(
            trim($value ?? '')
        ),
        ENT_QUOTES,
        'UTF-8'
    );
}
function validUsername($username)
{
    return preg_match('/^[\p{L}\p{N}_ ]{3,30}$/u', trim($username));
}

function validPassword($password)
{
    // 1. Tăng độ dài tối thiểu lên ít nhất 8 ký tự
    if (strlen($password) < 8) return false;
    
    // 2. Phải có chữ hoa
    if (!preg_match('/[A-Z]/', $password)) return false;
    
    // 3. Phải có chữ thường
    if (!preg_match('/[a-z]/', $password)) return false;
    
    // 4. Phải có số
    if (!preg_match('/[0-9]/', $password)) return false;
    
    // 5. Phải có ký tự đặc biệt (Nên thêm vào)
    if (!preg_match('/[^a-zA-Z0-9]/', $password)) return false;
    
    return true;
}

function validPhone($phone)
{
    return preg_match('/^[0-9]{10,11}$/', $phone);
}
function validRequired($value)
{
    return $value !== '';
}

function validEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validInt($number)
{
    return filter_var($number, FILTER_VALIDATE_INT) !== false;
}

function validPrice($number)
{
    return is_numeric($number) && $number >= 0;
}

function validLength($text, $min, $max)
{
    $len = mb_strlen($text);

    return $len >= $min && $len <= $max;
}

function validImage($filename)
{
    $allow = ['jpg', 'jpeg', 'png', 'webp'];

    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    return in_array($ext, $allow);
}