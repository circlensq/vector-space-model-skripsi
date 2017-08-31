<?php

date_default_timezone_set('Asia/Jakarta');
include "connection.php";
//====================================================================  
//             Case Folding dan Tokenizing description buku 
//====================================================================  
function tokenizing($conn){

    $query="
    UPDATE buku_research_uji
    SET tokenization = 
    REPLACE(REPLACE(REPLACE(
    REPLACE(REPLACE(
    REPLACE(description,'<p>',' '),'</p>',' '),'<b>',' '),'</b>',' '),'<i>',' '),'</i>',' ');

    UPDATE buku_research_uji
    SET tokenization = 
    REPLACE(REPLACE(
    REPLACE(REPLACE(
    REPLACE(REPLACE(title,'<p>',' '),'</p>',' '),'<b>',' '),'</b>',' '),'<i>',' '),'</i>',' ')
    WHERE description LIKE '';

 

    UPDATE buku_research_uji 
    SET tokenization = 
    REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(tokenization,'<br />',' '),'<strong>',' '),'</strong>',' '),'<em>',''),'</em>',' '),'<blockquote>',' '),'</blockquote>',' ');

    UPDATE buku_research_uji 
    SET tokenization = 
    REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(tokenization,'<div>',' '),'</div>',' '),'&',' '),'?',' '),'!',' '),':',' '),';',' ');

    

    UPDATE buku_research_uji 
    SET tokenization = 
    REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(tokenization,'•',' '),'<',' '),'>',' '),',',' '),'“',' '),'”',' ');

    UPDATE buku_research_uji 
    SET tokenization = 
    REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(tokenization,'+',' '),'(',' '),')',' '),'*',' '),'\"',' '),'$',' ');

    UPDATE buku_research_uji 
    SET tokenization = 
    REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(tokenization,'Ã',' '),'Â',' '),'\‚',' '),'¿',' '),'ã',' '),'â',' ');



    UPDATE buku_research_uji 
    SET tokenization = 
    REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(tokenization,'€',' '),' ',' '),'™',' '),'#',' '),'¢',' '),'¬',' ');

    UPDATE buku_research_uji 
    SET tokenization = 
    REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(tokenization,',',' '),'„',' '),'!',' '),'%',' '),'[',' ');


    UPDATE buku_research_uji 
    SET tokenization = 
    REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(tokenization,']',' '),'@',' '),'1',' '),'2',' '),'3',' '),'4',' ');

    UPDATE buku_research_uji 
    SET tokenization = 
    REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(tokenization,'5',' '),'6',' '),'7',' '),'8',' '),'9',' '),'0',' ');

    UPDATE buku_research_uji 
    SET tokenization = 
    REPLACE(REPLACE(REPLACE(tokenization,'\’',' '),'\‘',' '),'\†',' ');
   

    UPDATE buku_research_uji 
    SET tokenization = REPLACE(REPLACE(REPLACE(REPLACE(tokenization, '_', ' '), '—', ' '),'·',' '),'–',' ');

    UPDATE buku_research_uji 
    SET tokenization = REPLACE(tokenization, '.', ' ');

    UPDATE buku_research_uji
    SET tokenization = REPLACE(tokenization, '-', ' ');

    

    UPDATE buku_research_uji 
    SET tokenization = REPLACE(REPLACE(REPLACE(tokenization, CHAR(13), ' '), CHAR(10), ' '),'-',' ');

    UPDATE buku_research_uji 
    SET tokenization = REPLACE(REPLACE(REPLACE(tokenization, '.', ' '), '  ', ' '),'   ',' ');

    UPDATE buku_research_uji 
    SET tokenization = REPLACE(REPLACE(tokenization, '     ', ' '), '      ', ' ');

    UPDATE buku_research_uji 
    SET tokenization = LOWER(tokenization);

    UPDATE buku_research_uji 
    SET tokenization = 
    REPLACE(REPLACE(tokenization,'/',' '),'''',' ');

    UPDATE buku_research_uji 
    SET tokenization = 
    REPLACE(tokenization,'\\',' ');

    UPDATE buku_research_uji 
    SET tokenization = 
    REPLACE(tokenization,'œ',' ');
    ";

    $tokenizing = $conn->multi_query($query);
    var_dump($tokenizing);
    if($tokenizing){
    echo "</br>";
    echo "status : $tokenizing";
    echo "<br>Tokenization berhasil";
    }
    else{
        echo "</br>";
        echo "status : $tokenizing";
        echo "</br>Tokenization gagal";
        
    }

    // exclude character 'œ'
}

tokenizing($conn);

?>