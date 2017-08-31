<?php

include "connection.php";
//====================================================================  
//             Case Folding dan Tokenizing description buku 
//====================================================================  
function tokenizing(){

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
    REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(tokenization,'Ã',' '),'Â',' '),'‚',' '),'¿',' '),'ã',' '),'â',' ');

    UPDATE buku_research_uji 
    SET tokenization = 
    REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(tokenization,'€',' '),'œ',' '),'™',' '),'#',' '),'¢',' '),'¬',' ');

    UPDATE buku_research_uji 
    SET tokenization = 
    REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(tokenization,',',' '),'„',' '),'!',' '),'%',' '),'c',' '),'[',' ');

    UPDATE buku_research_uji 
    SET tokenization = 
    REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(tokenization,']',' '),'@',' '),'1',' '),'2',' '),'3',' '),'4',' ');

    UPDATE buku_research_uji 
    SET tokenization = 
    REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(tokenization,'5',' '),'6',' '),'7',' '),'8',' '),'9',' '),'0',' ');

    UPDATE buku_research_uji 
    SET tokenization = 
    REPLACE(REPLACE(REPLACE(tokenization,'’',' '),'‘',' '),'†',' ');

    UPDATE buku_research_uji 
    SET tokenization = REPLACE(REPLACE(REPLACE(tokenization, '_', ' '), '—', ' '),'·',' ');

    UPDATE buku_research_uji 
    SET tokenization = REPLACE(tokenization, '.', ' ');

    UPDATE buku_research_uji
    SET tokenization = REPLACE(tokenization, '-', ' ');

    UPDATE buku_research_uji 
    SET tokenization = 
    REPLACE(REPLACE(REPLACE(tokenization,'\\',''),'/',''),'''',' ');

    UPDATE buku_research_uji 
    SET tokenization = REPLACE(REPLACE(REPLACE(tokenization, CHAR(13), ' '), CHAR(10), ' '),'-',' ');

    UPDATE buku_research_uji 
    SET tokenization = REPLACE(REPLACE(REPLACE(tokenization, '.', ' '), '  ', ' '),'   ',' ');

    UPDATE buku_research_uji 
    SET tokenization = REPLACE(REPLACE(tokenization, '     ', ' '), '      ', ' ');

    UPDATE buku_research_uji 
    SET tokenization = LOWER(tokenization);
    ";

    $tokenizing = $conn->query($query);
}

$status  = tokenizing();
if($status){
    echo "Tokenization berhasil";
}
else{
    echo "Tokenization gagal";
    
}
?>