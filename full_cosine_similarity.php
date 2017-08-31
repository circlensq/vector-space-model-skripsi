<?php 
date_default_timezone_set('Asia/Jakarta');
include "connection.php";
//====================================================================  
/*Cosine Similarity function*/
//====================================================================  
function matrixDoc($all_docs_id,$start_docs_id,$done_all_id,$first, $doc_vector,$conn){
    $epsilon = 0;
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
                
                if(abs($result) <= $epsilon){

                }
                else{
                    $created_dt   = date('Y-m-d H:i:s');
                    $last_updated = date('Y-m-d H:i:s');
                    $query_insert = "INSERT INTO similarity(doc_id,doc_id_2,similarity_score,created_dt,last_updated) 
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
                    $query_insert = "INSERT INTO similarity(doc_id,doc_id_2,similarity_score,created_dt,last_updated) VALUES('$all_docs_id[$doc_id_1]','$all_docs_id[$doc_id_2]','$result','$created_dt','$last_updated')";
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
                    $query_insert = "INSERT INTO similarity(doc_id,doc_id_2,similarity_score,created_dt,last_updated) VALUES('$all_docs_id[$doc_id_1]','$all_docs_id[$doc_id_2]','$result','$created_dt','$last_updated')";
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