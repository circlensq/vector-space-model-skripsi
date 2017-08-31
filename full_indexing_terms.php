<?php 

date_default_timezone_set('Asia/Jakarta');
include "connection.php";

function saveTerm($docs, $conn){
    if($docs->num_rows > 0){
        while($array_filter = $docs->fetch_assoc())
        {       
            $save_checked   = '';
            $biblio_id      = $array_filter['biblio_id'];
            $stemming      = trim($array_filter['stemming']);
            $language_desc  = $array_filter['language_desc'];

            $stemming = explode(" ",$stemming);
            foreach($stemming as $word){
                //memasukkan term ke dalam tabel terms_dict
                checkTerm($word,$conn); 
            } 
            $query_update = "UPDATE buku_research_uji 
                            SET save_term='done' 
                            WHERE biblio_id LIKE '$biblio_id' ";
            $update = $conn->query($query_update);              
        }
    }
}

function checkTerm($word,$conn){
    $word = trim($word);
    $created_dt   = date('Y-m-d H:i:s');
    $last_updated = date('Y-m-d H:i:s');
    $query_search = "SELECT term FROM terms_dict WHERE term LIKE '$word' ";
    $search = $conn->query($query_search);
    if($search->num_rows < 1){
        $query_insert = "INSERT INTO terms_dict(term,created_dt,last_updated) 
                        VALUES('$word','$created_dt','$last_updated')";
        $insert = $conn->query($query_insert);
        if($insert){
                echo"Berhasil menambahkan : ".var_dump($word)."</br>";
        }else{
                echo"Error data : ".mysqli_error($conn);
        } 
    }

}

// Mengambil data stemming untuk disimpan dalam database
$query_desc = "SELECT biblio_id, stemming,language_desc FROM buku_research_uji WHERE save_term !='done'";
$docs = $conn->query($query_desc);
saveTerm($docs,$conn);


 ?>