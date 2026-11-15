<?php
$conn = mysqli_connect("localhost", "root", "", "penjualan");

if (!$conn) {
	die("Database gagal terhubung: " . mysqli_connect_error());
}
?>
