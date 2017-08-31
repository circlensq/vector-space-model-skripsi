<html>
<head><title>Stemming Porter Bahasa Inggris</title>

<script>
	no = 1;
	function tambah(){
		urut=no+1;
		document.getElementById(urut).innerHTML="<p>("+urut+") Input Kata : <input type='text' name='kata[]'></p></div><div id='"+(urut+1)+"'>";	
		no++;
	}
	
	function hapus(){
	  if(no!=1){
		document.getElementById(no).innerHTML="";
		no--;
	  }
	}
	
</script>
</head>
<body>
<h1 align='center'>Stemming dengan Algoritma Porter Bahasa Inggris</h1>

<form action="proses.php" method="POST">
<fieldset style="width:30%;margin:auto">
<legend>Masukkan Kata dalam Bahasa Inggris</legend>
<center>
     <div id="1">
		<p>(1) Input Kata : <input type="text" name="kata[]"></p>
     </div>
     
     <div id="2"></div>
     
	<p>
    	<a href="javascript:tambah()">Tambah</a>
    	<a href="javascript:hapus()">Hapus</a>
    </p>
    
<input type="submit" value="STEMMING!" /><input type="reset" value="RESET" />
</center>
 </fieldset>
</form>
	
</body>
</html>