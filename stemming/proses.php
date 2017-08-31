<html>
<head>
	<title>Stemming Porter Bahasa Indonesia</title>
	<style>
		.col:hover{background-color:#FF0;cursor:pointer;}
	</style>
</head>
<body>

<?php
include "stemming.php";
 $a = $_POST['kata'];
 $awal = microtime(true);
 echo "
 		<h1 align='center'>Tabel Hasil Stemming dengan Algoritma Porter Bahasa Indonesia</h1>
 		<table border='1' align='center' style='border-collapse:collapse;' width='80%'>
  			<tr align='center' bgcolor='#00FFFF'>
				<th>No</th>
				<th>Inputan</th>
				<th>Hapus Partikel</th>
				<th>Hapus Posessive Pronoun</th>
				<th>Hapus Awalan 1</th>
				<th>Hapus Awalan 2</th>
				<th>Hapus Akhiran</th>
				<th>Kata Dasar</th>
			</tr>
			";
$warna = "#DFE3FF";
$i=1;
 foreach($a as $kata){
	 if($warna=="#DFE3FF"){$warna="#ffffff";}else{$warna="#DFE3FF";}	
 echo "<tr align='center' bgcolor='$warna' class='col'>
 				<td>$i</td>
				<td>$kata</td>
				<td>".hapuspartikel($kata)."</td>
				<td>".hapuspp(hapuspartikel($kata))."</td>
				<td>".hapusawalan1(hapuspp(hapuspartikel($kata)))."</td>
				<td>".hapusawalan2(hapusawalan1(hapuspp(hapuspartikel($kata))))."</td>
				<td>".hapusakhiran(hapusawalan2(hapusawalan1(hapuspp(hapuspartikel($kata)))))."</td>
				<td>".hapusakhiran(hapusawalan2(hapusawalan1(hapuspp(hapuspartikel($kata)))))."</td>
			</tr>";
$i++;
}
echo "</table>";
$akhir = microtime(true);
?>
<br />
<center><?php $lama = $akhir-$awal; echo "Lama Proses Stemming : $lama detik"; ?></center>
</body>
</html>