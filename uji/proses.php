<?php

function getIndex($documents) {

    $dictionary = array();
    $docCount = array();

    $arr_terms = array();
    foreach($documents as $docID => $doc) {

        $terms = explode(' ', $doc);
        $docCount[$docID] = count($terms);
        array_push($arr_terms, $terms);
        // var_dump($arr_terms);
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
    return array('docCount' => $docCount, 'dictionary' => $dictionary,'arr_terms'=>$arr_terms);
}

function getTfidf($docs) {
    
    $index = getIndex($docs);

    $docCount = count($index['docCount']);
    $arr_terms = $index['arr_terms'];
    
    foreach($arr_terms as $terms){
        foreach($terms as $term){
            $entry = $index['dictionary'][$term];
            foreach($entry['postings'] as  $docID => $postings) {
                $term_weight = ($postings['tf'] * log($docCount / $entry['df'], 10));

                echo "Document biblio_id : $docID and kata $term give TFIDF: " .$term_weight;
                echo "</br>";
                $save_doc[$docID][$term] = $term_weight;
            }
        }
    }


    return $save_doc;
}

function queryTfidf($query, $docs){
    $index = getIndex($docs);

    $docCount = count($index['docCount']);
    
    $query = explode(' ',$query);
    foreach($query as $q){
        if(!isset($count[$q])){
            $count[$q] = 0;
        }
        $count[$q]++;
    }

    foreach($query as $q){
        $entry = $index['dictionary'][$q];
        $query_weight = ($count[$q] * log($docCount / $entry['df'], 10));
        $save_query[$q] = $query_weight;
        echo "Query and kata $q give TFIDF: " .$query_weight;
        echo "</br>";
    }
    return $save_query;
    
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

if(!isset($_POST['query'])){
   
$data = $_POST['pertama'];
$data2 = $_POST['kedua'];
$data3 = $_POST['ketiga'];

$documents = array($data, $data2, $data3);
$array_weight = getTfidf($documents);

echo "</br>";
echo "</br>";
$length = count($array_weight);
// var_dump($array_weight);
for($i = 0; $i < $length ;  $i++){
    if(($i+1) < $length){
        $next_index = $i+1;
        $result =similarity($array_weight[$i], $array_weight[$next_index]);
        echo "Document[$i] dengan Document[$next_index] memiliki nilai similarity : $result";
        echo "</br>";
    }else{
        $result =similarity($array_weight[$i], $array_weight[0]);
        echo "Document[$i] dengan Document[0] memiliki nilai similarity : $result";
        echo "</br>";
        echo "</br>";
    }
}
}else{
  
    $query = $_POST['query'];
    $data  = $_POST['pertama'];
    $data2 = $_POST['kedua'];
    $data3 = $_POST['ketiga'];
    $documents = array($data, $data2, $data3);
    $array_weight = getTfidf($documents);
    $query_weight = queryTfidf($query,$documents);
    $length = count($array_weight);
    for($i = 0; $i < $length ;  $i++){
        $result =similarity($query_weight, $array_weight[$i]);
        echo "Query dengan Document[$i] memiliki nilai similarity : $result";
        echo "</br>";
    }

}


?>



