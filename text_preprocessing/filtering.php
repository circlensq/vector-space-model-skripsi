<?php


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
            
?>
