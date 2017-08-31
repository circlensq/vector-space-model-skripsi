<?php

function stemming_indonesia($kata){
	$kata_stemming = hapusakhiran(hapusawalan2(hapusawalan1(hapuskataganti(hapuspartikel($kata)))));
	return $kata_stemming;
}


// function untuk mencari kata-kata khusus 
//untuk meminimalkan kesalahan dalam proses stemming
function search_dict($kata){
	include "connection.php";
	$hasil = mysql_num_rows(mysql_query("SELECT * FROM tb_katadasar 
							WHERE katadasar='$kata'"));

	return $hasil;
}

//langkah 1 - hapus partikel "-kah", "-lah", "-pun"
function hapuspartikel($kata){
	if(search_dict($kata)!=1){
	if((substr($kata, -3) == 'kah' )
		||( substr($kata, -3) == 'lah' )
		||( substr($kata, -3) == 'pun' )
		||( substr($kata, -3) == 'tah' ))
		{
		$kata = substr($kata, 0, -3);			
		}
	}
	return $kata;
}

//langkah 2 - hapus kata ganti perorangan "-ku", "-mu", "-nya"
function hapuskataganti($kata){
	if(search_dict($kata)!=1){
		if(strlen($kata) > 4){
			if((substr($kata, -2)== 'ku')||(substr($kata, -2)== 'mu')){
				$kata = substr($kata, 0, -2);
			}else if((substr($kata, -3)== 'nya')){
				$kata = substr($kata,0, -3);
			}
		}
	}
	return $kata;
}

//langkah 3 hapus first order prefiks (awalan pertama)
function hapusawalan1($kata){
	if(search_dict($kata)!=1){
		if(substr($kata,0,4)=="meng"){
			if(substr($kata,4,1)=="e"||substr($kata,4,1)=="u"){
			$kata = "k".substr($kata,4);
			}else{
			$kata = substr($kata,4);
			}
		}else if(substr($kata,0,4)=="meny"){
			$kata = "s".substr($kata,4);
		}else if(substr($kata,0,3)=="men"){
			$kata = substr($kata,3);
		}else if(substr($kata,0,3)=="mem"){
			if(substr($kata,3,1)=="a" || substr($kata,3,1)=="i" || substr($kata,3,1)=="e" || substr($kata,3,1)=="u" || substr($kata,3,1)=="o"){
				$kata = "p".substr($kata,3);
			}else{
				$kata = substr($kata,3);
			}
		}else if(substr($kata,0,2)=="me"){
			$kata = substr($kata,2);
		}else if(substr($kata,0,4)=="peng"){
			if(substr($kata,4,1)=="e" || substr($kata,4,1)=="a"){
			$kata = "k".substr($kata,4);
			}else{
			$kata = substr($kata,4);
			}
		}else if(substr($kata,0,4)=="peny"){
			$kata = "s".substr($kata,4);
		}else if(substr($kata,0,3)=="pen"){
			if(substr($kata,3,1)=="a" || substr($kata,3,1)=="i" || substr($kata,3,1)=="e" || substr($kata,3,1)=="u" || substr($kata,3,1)=="o"){
				$kata = "t".substr($kata,3);
			}else{
				$kata = substr($kata,3);
			}
		}else if(substr($kata,0,3)=="pem"){
			if(substr($kata,3,1)=="a" || substr($kata,3,1)=="i" || substr($kata,3,1)=="e" || substr($kata,3,1)=="u" || substr($kata,3,1)=="o"){
				$kata = "p".substr($kata,3);
			}else{
				$kata = substr($kata,3);
			}
		}else if(substr($kata,0,2)=="di"){
			$kata = substr($kata,2);
		}else if(substr($kata,0,3)=="ter"){
			$kata = substr($kata,3);
		}else if(substr($kata,0,2)=="ke"){
			$kata = substr($kata,2);
		}
	}
	return $kata;
}
//langkah 4 hapus second order prefiks (awalan kedua)
function hapusawalan2($kata){
	if(search_dict($kata)!=1){
	
		if(substr($kata,0,3)=="ber"){
			$kata = substr($kata,3);
		}else if(substr($kata,0,3)=="bel"){
			$kata = substr($kata,3);
		}else if(substr($kata,0,2)=="be"){
			$kata = substr($kata,2);
		}else if(substr($kata,0,3)=="per" && strlen($kata) > 5){
			$kata = substr($kata,3);
		}else if(substr($kata,0,2)=="pe"  && strlen($kata) > 5){
			$kata = substr($kata,2);
		}else if(substr($kata,0,3)=="pel"  && strlen($kata) > 5){
			$kata = substr($kata,3);
		}else if(substr($kata,0,2)=="se"  && strlen($kata) > 5){
			$kata = substr($kata,2);
		}
	}
	return $kata;
}
////langkah 5 hapus suffiks
function hapusakhiran($kata){
	if(search_dict($kata)!=1){

		if (substr($kata, -3)== "kan" ){
			$kata = substr($kata, 0, -3);
		}
		else if(substr($kata, -1)== "i" ){
			$kata = substr($kata, 0, -1);
		}else if(substr($kata, -2)== "an"){
			$kata = substr($kata, 0, -2);
		}
	}	
	return $kata;
}


?>
