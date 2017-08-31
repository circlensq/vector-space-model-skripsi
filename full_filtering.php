<?php
date_default_timezone_set('Asia/Jakarta');
include "connection.php";

function filtering($biblio_id,$input,$arr_stopword){
	//$input adalah array tokenization

	$filtering_res ='';
	$input = explode(" ",$input);
	foreach($input as $words){
		if(in_array(trim($words), $arr_stopword)){
				
		}else{
			$filtering_res .= trim($words).' ';
		}
	}
	return $filtering_res;
}


//====================================================================  
//      Function untuk melakukan filtering token buku dari database
//====================================================================  
function doFiltering($docs,$conn){
    
    $query = "SELECT word FROM mst_stoplist";
    $stopword= $conn->query($query);

    $arr_stopword = array();
    if($stopword->num_rows > 0){
        while($kata = $stopword->fetch_assoc())
        {     
            array_push($arr_stopword,trim($kata["word"]));	
        }
    }

	if($docs->num_rows > 0){
		while($array_token = $docs->fetch_assoc())
		{       
            $biblio_id      = $array_token['biblio_id'];
            $tokenization   = $array_token['tokenization'];
            //memanggil function filtering
            $filtered_words = filtering($biblio_id,$tokenization,$arr_stopword);

            $result = $filtered_words;
            $query_update = "UPDATE buku_research_uji SET filtering='$result' WHERE biblio_id LIKE '$biblio_id' ";
            $update = $conn->query($query_update);

            if($update){
            	echo "</br>filtering biblio_id $biblio_id BERHASIL";
            }else{
            	echo "</br>filtering biblio_id $biblio_id GAGAL";
            }
		}
	}

}

// Filtering description
$query_desc = "SELECT biblio_id, tokenization FROM buku_research_uji WHERE filtering LIKE '' ";
$docs = $conn->query($query_desc); 
doFiltering($docs,$conn);
            
?>
