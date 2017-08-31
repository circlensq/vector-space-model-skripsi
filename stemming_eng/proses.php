<html>
<head>
	<title>Stemming Porter Bahasa Inggris</title>
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
 		<h1 align='center'>Tabel Hasil Stemming dengan Algoritma Porter Bahasa Inggris</h1>
 		<table border='1' align='center' style='border-collapse:collapse;' width='80%'>
  			<tr align='center' bgcolor='#00FFFF'>
				<th>No</th>
				<th>Inputan</th>
				<th>Step 1ab</th>
				<th>Step 1c</th>
				<th>Step 2</th>
				<th>Step 3</th>
				<th>Step 4</th>
				<th>Step 5</th>
			</tr>
			";
$warna = "#DFE3FF";
$i=1;
 foreach($a as $kata){
	 if($warna=="#DFE3FF"){$warna="#ffffff";}else{$warna="#DFE3FF";}	
 echo "<tr align='center' bgcolor='$warna' class='col'>
 				<td>$i</td>
				<td>$kata</td>
				<td>".step1ab($kata)."</td>
				<td>".step1c(step1ab($kata))."</td>
				<td>".step2(step1c(step1ab($kata)))."</td>
				<td>".step3(step2(step1c(step1ab($kata))))."</td>
				<td>".step4(step3(step2(step1c(step1ab($kata)))))."</td>
				<td>".Stem($kata)."</td>
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