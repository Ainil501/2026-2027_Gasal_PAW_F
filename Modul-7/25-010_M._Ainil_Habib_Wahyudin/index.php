<?php
require "koneksi.php";

$mulai = "";
$selesai = "";
$labels = [];
$data = [];
$rekap = [];
$total_pendapatan = 0;
$total_pelanggan = 0;

if (isset($_GET['tanggal_mulai']) && isset($_GET['tanggal_selesai'])) {
	$mulai = $_GET['tanggal_mulai'];
	$selesai = $_GET['tanggal_selesai'];


	$result = mysqli_query($conn, "SELECT DATE(waktu_transaksi) AS tgl, SUM(total) AS total_harian
		FROM transaksi
		WHERE waktu_transaksi BETWEEN '$mulai' AND '$selesai'
		GROUP BY DATE(waktu_transaksi)
		ORDER BY tgl ASC");

	while ($r = mysqli_fetch_assoc($result)) {
		$labels[] = $r['tgl'];
		$data[] = $r['total_harian'];
		$rekap[] = $r;
		$total_pendapatan += $r['total_harian'];
	}


	$result2 = mysqli_query($conn, "SELECT COUNT(DISTINCT pelanggan_id) AS jml
		FROM transaksi
		WHERE waktu_transaksi BETWEEN '$mulai' AND '$selesai'");
	$r2 = mysqli_fetch_assoc($result2);
	$total_pelanggan = $r2['jml'];
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Laporan Penjualan</title>
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<style>
		body { font-family: Arial, sans-serif; margin: 20px; }
		table { border-collapse: collapse; width: 100%; margin-top: 15px; }
		th, td { border: 1px solid #000; padding: 6px; text-align: left; }


		@media print {
			.no-print { display: none !important; }
		}
	</style>
</head>
<body>

<div class="no-print">
	<h1>Filter Laporan Penjualan</h1>
	<form method="GET">
		Dari: <input type="date" name="tanggal_mulai" value="<?= $mulai ?>" required>
		Sampai: <input type="date" name="tanggal_selesai" value="<?= $selesai ?>" required>
		<button type="submit">Tampilkan</button>
	</form>
	<br>
	<?php if (!empty($rekap)): ?>
		<button onclick="window.print()">Cetak</button>
		<a href="excel.php?tanggal_mulai=<?= $mulai ?>&tanggal_selesai=<?= $selesai ?>"><button>Excel</button></a>
	<?php endif; ?>
</div>

<?php if (!empty($rekap)): ?>

	<h2>Grafik Penjualan</h2>
	<div style="width: 600px; height: 300px;">
		<canvas id="myChart"></canvas>
	</div>

	<script>
		const ctx = document.getElementById('myChart');
		new Chart(ctx, {
			type: 'bar',
			data: {
				labels: <?= json_encode($labels) ?>,
				datasets: [{
					label: 'Total Pendapatan (Rp)',
					data: <?= json_encode($data) ?>,
					borderWidth: 1
				}]
			},
			options: {
				maintainAspectRatio: false,
				scales: { y: { beginAtZero: true } }
			}
		});
	</script>

	<h2>Rekapitulasi</h2>
	<table>
		<tr>
			<th>No</th>
			<th>Total Pendapatan</th>
			<th>Tanggal Transaksi</th>
		</tr>
		<?php $no = 1; foreach ($rekap as $row): ?>
		<tr>
			<td><?= $no++ ?></td>
			<td>Rp. <?= number_format($row['total_harian'], 0, ',', '.') ?></td>
			<td><?= date('d M Y', strtotime($row['tgl'])) ?></td>
		</tr>
		<?php endforeach; ?>
	</table>

	<table>
		<tr>
			<th>Jumlah Pelanggan Unik</th>
			<th>Total Pendapatan Keseluruhan</th>
		</tr>
		<tr>
			<td ><?= $total_pelanggan ?> Orang</td>
			<td >Rp. <?= number_format($total_pendapatan, 0, ',', '.') ?></td>
		</tr>
	</table>

<?php elseif ($mulai != ""): ?>
	<p>Tidak ada transaksi pada rentang tanggal tersebut.</p>
<?php endif; ?>

</body>
</html>
