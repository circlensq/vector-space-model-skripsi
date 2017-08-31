<?php 
date_default_timezone_set('Asia/Jakarta');
include "connection.php";


//====================================================================  
//             Menghitung TF-IDF term description
//====================================================================  
function doWeighting($terms,$conn,$all){
    // $all = semua reduplication dari tabel 
    // $docs = document yang mau dicari bobot setiap katanya
    
    if($terms->num_rows > 0){
        // print_r($terms);
        while($array_filter = $terms->fetch_assoc())
        {                   
            $save_checked   = '';
            $term      = trim($array_filter['term']);          
            $term_id   = $array_filter['term_id'];      
            getTfidf($term_id,$term,$all,$conn);         
            $query_update = "UPDATE terms_dict SET status_weighting ='done' WHERE term_id LIKE '$term_id' ";
            $update = $conn->query($query_update);
            echo "</br>";
            if($update){
                echo "</br>UPDATE term_id : $term_id </br></br>";;
            }else{
                echo "Error : ".mysqli_error($conn)."</br>";
            }
        }
    }    
}

function getIndex($documents) {

    $dictionary = array();
    $docCount = array();

    foreach($documents as $docID => $doc) {

        $terms = explode(' ', $doc);
        $docCount[$docID] = count($terms);
       
        foreach($terms as $term) {
            if(!isset($dictionary[$term])) {
                $dictionary[$term] = array('df' => 0, 'postings' => array());
            }
            if(!isset($dictionary[$term]['postings'][$docID])) {
                // menghitung semua dokumen yang memiliki term
                $dictionary[$term]['df']++; 
                //menghitung jumlah frekuensi term pada 1 dokumen
                $dictionary[$term]['postings'][$docID] = array('tf' => 0); 
            }

            $dictionary[$term]['postings'][$docID]['tf']++;
        }
    }

    return array('docCount' => $docCount, 'dictionary' => $dictionary);
}



function getTfidf($term_id,$term,$docs,$conn) {
  
    $index = getIndex($docs);

    $docCount = count($index['docCount']);
    $entry = $index['dictionary'][$term];
    
    foreach($entry['postings'] as  $docID => $postings) {

        $term_weight = ($postings['tf'] * log($docCount / $entry['df'], 10));

        $query_insert = "INSERT INTO weight(term_id,doc_id,weight) 
                        VALUES('$term_id','$docID','$term_weight')";
        $insert = $conn->query($query_insert);
        if($insert){
            echo "Document biblio_id : $docID and term $term ($term_id) give TFIDF: " .
                ($postings['tf'] * log($docCount / $entry['df'], 10));
            echo "</br>";
        }else{
            echo "Error : ".mysqli_error($conn)."</br>";
        }  
    }
}



// Perhitungan bobot term pada description
$query_desc = "SELECT term_id,term FROM terms_dict WHERE status_weighting !='done' ";
$terms = $conn->query($query_desc);

$all_docs_query = "SELECT biblio_id, stemming FROM buku_research_uji";
$all_docs = $conn->query($all_docs_query);
$all = array();
while($array_filter = $all_docs->fetch_assoc()){
     $all[$array_filter['biblio_id']]=$array_filter['stemming'];
}
doWeighting($terms,$conn,$all);

 ?>