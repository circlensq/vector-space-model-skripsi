<?php
date_default_timezone_set('Asia/Jakarta');
include "connection.php";
include "text_preprocessing/stemming_code/stemming_indonesia.php";
include "text_preprocessing/stemming_code/stemming_english_2.php";

//====================================================================  
// Function untuk melakukan stemming filtered words buku dari database
//====================================================================  

function doStemming($docs,$conn){
    if($docs->num_rows > 0){
        while($array_filter = $docs->fetch_assoc())
        {       
            $save_stemming  = '';
            $biblio_id      = $array_filter['biblio_id'];
            $filtering      = trim($array_filter['filtering']);
            $language_desc  = $array_filter['language_desc'];
       
            $filtering = explode(" ",$filtering);
            foreach($filtering as $filter_word){
                 
                    if($language_desc == 'id'){
                            $stemming = stemming_indonesia($filter_word);
                    }else{
                            $stemming = stemming_english($filter_word);
                    }
                    $save_stemming .= $stemming.' ';
            }
            $query_update = "UPDATE buku_research_uji SET stemming='$save_stemming' 
                            WHERE biblio_id LIKE '$biblio_id' ";
            $update = $conn->query($query_update);

            if($update){
                echo "</br>stemming biblio_id $biblio_id BERHASIL";
            }else{
                echo "</br>stemming biblio_id $biblio_id GAGAL";
            }
        }
    }
}

// Stemming Description
$query_desc = "SELECT biblio_id, filtering,language_desc FROM buku_research_uji WHERE stemming LIKE '' ";
$docs = $conn->query($query_desc);
doStemming($docs,$conn);


?>