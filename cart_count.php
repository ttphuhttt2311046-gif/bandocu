<?php
session_start();
echo isset($_SESSION['cart']) 
    ? array_sum(array_column($_SESSION['cart'], 'qty')) 
    : 0;
?>
