<?php
require "koneksi.php";

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=rekap_penjualan.xls");

$mulai = $_GET['tanggal_mulai'];
$selesai = $_GET['tanggal_selesai'];

$result = mysqli_query($conn, "SELECT DATE(waktu_transaksi) AS tgl, SUM(total) AS total_harian
	FROM transaksi
	WHERE waktu_transaksi BETWEEN '$mulai' AND '$selesai'
	GROUP BY DATE(waktu_transaksi)
	ORDER BY tgl ASC");

$result2 = mysqli_query($conn, "SELECT COUNT(DISTINCT pelanggan_id) AS jml
	FROM transaksi
	WHERE waktu_transaksi BETWEEN '$mulai' AND '$selesai'");
$r2 = mysqli_fetch_assoc($result2);
?>
<h2>Rekapitulasi Penjualan <?= $mulai ?> s/d <?= $selesai ?></h2>
<table border="1">
	<tr>
		<th>No</th>
		<th>Total Pendapatan</th>
		<th>Tanggal Transaksi</th>
	</tr>
	<?php
	$no = 1;
	$total_pendapatan = 0;
	while ($r = mysqli_fetch_assoc($result)):
		$total_pendapatan += $r['total_harian'];
	?>
	<tr>
		<td><?= $no++ ?></td>
		<td><?= $r['total_harian'] ?></td>
		<td><?= date('d M Y', strtotime($r['tgl'])) ?></td>
	</tr>
	<?php endwhile; ?>
</table>
<br>
<table border="1">
	<tr>
		<th>Jumlah Pelanggan Unik</th>
		<th>Total Pendapatan Keseluruhan</th>
	</tr>
	<tr>
		<td><?= $r2['jml'] ?> Orang</td>
		<td><?= $total_pendapatan ?></td>
	</tr>
</table>
