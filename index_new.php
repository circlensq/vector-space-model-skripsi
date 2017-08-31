<?php
date_default_timezone_set('Asia/Jakarta');
include "connection.php";
include "text_preprocessing/filtering.php";
include "text_preprocessing/stemming_code/stemming_indonesia.php";
include "text_preprocessing/stemming_code/stemming_english_2.php";

//tambahkan function untuk tokenization

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
		}
	}

}

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
        }
    }
}

//====================================================================  
// Function untuk menyimpan terms ke dalam tabel terms_dict
//====================================================================  
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


//====================================================================  
/*Cosine Similarity function*/
//====================================================================  
function matrixDoc($all_docs_id,$start_docs_id,$done_all_id,$first, $doc_vector,$conn){
    $epsilon = 0.1;
    $count_done     = count($done_all_id);
    $count_start    = count($start_docs_id);
    $count_all      = count($all_docs_id);
    if($first == true){
        foreach(array_keys($start_docs_id) as $start_key){
            $i = 1;
            while($i < $count_start){
                $doc_id_1 = $start_key ;
                $doc_id_2 = $doc_id_1 + $i;
                if($doc_id_2 == $count_start){
                    break;
                }
                else{
                    $i++;
                }
                $result = similarity($doc_vector[$all_docs_id[$doc_id_1]],$doc_vector[$all_docs_id[$doc_id_2]]);
                
                if(abs($result) < $epsilon){

                }
                else{
                    $created_dt   = date('Y-m-d H:i:s');
                    $last_updated = date('Y-m-d H:i:s');
                    $query_insert = "INSERT INTO similarity_3(doc_id,doc_id_2,similarity_score,created_dt,last_updated) 
                    VALUES('$all_docs_id[$doc_id_1]','$all_docs_id[$doc_id_2]','$result','$created_dt','$last_updated')";
                    $insert = $conn->query($query_insert);
                    if($insert){
                    
                    }else{
                    echo "Gagal karena : ".mysqli_error($conn)."</br>";
                    }
                }
            }
            
            $query_update = "UPDATE buku_research_uji SET status_similarity='done' WHERE biblio_id LIKE $all_docs_id[$doc_id_1] ";
            $update = $conn->query($query_update);
            if($update){
            echo "Doc ID 1 : $all_docs_id[$doc_id_1] DONE </br>";
            }else{
            echo "Gagal karena : ".mysqli_error($conn)."</br>";
            }
        }
        $query_update = "UPDATE buku_research_uji SET status_similarity='done' WHERE biblio_id LIKE $all_docs_id[$doc_id_2] ";
        $update = $conn->query($query_update);
        if($update){
        echo "Doc ID 1 : $all_docs_id[$doc_id_1] DONE </br>";
        }else{
        echo "Gagal karena : ".mysqli_error($conn)."</br>";
        }
       
    }
    else{
        foreach(array_keys($start_docs_id) as $start_key){
            $i=1;
            while($i < $count_start){
                $doc_id_1 = $start_key + $count_done;
                $doc_id_2 = $doc_id_1 + $i;
                if($doc_id_2 == $count_all ){
                    break;
                }
                else{
                    $i++;
                }
                $result = similarity($doc_vector[$all_docs_id[$doc_id_1]],$doc_vector[$all_docs_id[$doc_id_2]]);
                if(abs($result) < $epsilon){
                  
                }
                else{
                    $created_dt   = date('Y-m-d H:i:s');
                    $last_updated = date('Y-m-d H:i:s');
                    $query_insert = "INSERT INTO similarity_3(doc_id,doc_id_2,similarity_score,created_dt,last_updated) VALUES('$all_docs_id[$doc_id_1]','$all_docs_id[$doc_id_2]','$result','$created_dt','$last_updated')";
                    $insert = $conn->query($query_insert);
                    if($insert){
                    
                    }else{
                    echo "Gagal karena : ".mysqli_error($conn)."</br>";
                    }
                }

            }
        }

        $kurang = 1;
        foreach(array_keys($start_docs_id) as $start_key){
            $i = $kurang;
            
            while($i <= $start_key + $count_done){
                $doc_id_1 = $start_key + $count_done ;
                $doc_id_2 = $doc_id_1 - $i;
                $i++;
                $result = similarity($doc_vector[$all_docs_id[$doc_id_1]],$doc_vector[$all_docs_id[$doc_id_2]]);
                if(abs($result) < $epsilon){
                   
                }
                else{
                    $created_dt   = date('Y-m-d H:i:s');
                    $last_updated = date('Y-m-d H:i:s');
                    $query_insert = "INSERT INTO similarity_3(doc_id,doc_id_2,similarity_score,created_dt,last_updated) VALUES('$all_docs_id[$doc_id_1]','$all_docs_id[$doc_id_2]','$result','$created_dt','$last_updated')";
                    $insert = $conn->query($query_insert);
                    if($insert){
                    // echo "masuk masuk else 2</br>";
                    // echo "Doc ID 1 : $all_docs_id[$doc_id_1] </br>";
                    // echo "Doc ID 2 : $all_docs_id[$doc_id_2] </br>";
                    // echo "Result   : $result </br></br>";
                    }else{
                    echo "Gagal karena : ".mysqli_error($conn)."</br>";
                    }
                }
            }
            $kurang++;

            $query_update = "UPDATE buku_research_uji SET status_similarity='done' WHERE biblio_id LIKE $all_docs_id[$doc_id_1] ";
            $update = $conn->query($query_update);
            if($update){
            echo "Doc ID 1 : $all_docs_id[$doc_id_1] DONE </br>";
            }else{
            echo "Gagal karena : ".mysqli_error($conn)."</br>";
            }
        }  
    }
}

function similarity(array $vector1, array $vector2) {

    return dotProduct($vector1, $vector2) / (absVector($vector1) * absVector($vector2));
}
  
function dotProduct(array $vector1, array $vector2) {
    $result = 0;

    foreach (array_keys($vector1) as $key1) {
        foreach (array_keys($vector2) as $key2) {
            if ($key1 === $key2) $result += $vector1[$key1] * $vector2[$key2];
        }
    }

    return $result;
}
  
function absVector(array $vector) {
    $result = 0;

    foreach (array_values($vector) as $value) {
        $result += $value * $value;
    }

    return sqrt($result);
}


// Filtering description
$query_desc = "SELECT biblio_id, tokenization FROM buku_research_uji WHERE filtering LIKE '' LIMIT 100";
$docs = $conn->query($query_desc); 
doFiltering($docs,$conn);

// Stemming Description
$query_desc = "SELECT biblio_id, filtering,language_desc FROM buku_research_uji WHERE stemming LIKE '' AND biblio_id LIKE '23' LIMIT 1";
$docs = $conn->query($query_desc);
doStemming($docs,$conn);


// Mengambil data stemming untuk disimpan dalam database
$query_desc = "SELECT biblio_id, stemming,language_desc FROM buku_research_uji WHERE status_weighting !='done' ";
$docs = $conn->query($query_desc);
saveTerm($docs,$conn);


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

// Tahap perhitungan kemiripan dengan Cosine Similarity

// menampung semua id yang telah selesai dihitung similarity-nya
$all_id = array();
$first = true;
$done_id = "SELECT biblio_id FROM buku_research_uji  WHERE status_similarity LIKE 'done' GROUP BY biblio_id ORDER BY biblio_id  ";
$done = $conn->query($done_id);
$done_array = array();
if($done->num_rows > 0){
    while($array_done = $done->fetch_assoc()){
        array_push($done_array,$array_done['biblio_id']);
        array_push($all_id,$array_done['biblio_id']);
    }
    $first = false;
}

//menampung start id yang belum dihitung similaritynya dan akan diberikan limit
$start_docs_id = "SELECT biblio_id FROM buku_research_uji  WHERE status_similarity !='done' GROUP BY biblio_id ORDER BY biblio_id LIMIT 1500";
$start = $conn->query($start_docs_id);
$start_id = array();
while($array_start = $start->fetch_assoc()){
    array_push($start_id,$array_start['biblio_id']);

    //menampung kedua hasil query ke dalam satu array $all_id
    array_push($all_id,$array_start['biblio_id']);
}

$doc_vector = array(array());
foreach($all_id as $id){
    $all_weight = "SELECT term_id,doc_id,weight FROM weight WHERE doc_id LIKE '$id' AND weight !='0' ";
    $all = $conn->query($all_weight);
    while($get_all_id = $all->fetch_assoc()){
        $term_id = $get_all_id['term_id'];
        $doc_id = $get_all_id['doc_id'];
        $weight = $get_all_id['weight'];
        $doc_vector[$doc_id][$term_id] = $weight;   
    }
}
matrixDoc($all_id,$start_id,$done_array,$first, $doc_vector,$conn);


?>