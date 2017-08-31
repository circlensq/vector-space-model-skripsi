<?php


$GLOBALS['regex_consonant'] = '(?:[bcdfghjklmnpqrstvwxz]|(?<=[aeiou])y|^y)';

$GLOBALS['regex_vowel'] = '(?:[aeiou]|(?<![aeiou])y)';

function stemming_english($word){
    $result = Stem($word);
    return $result;
}

function Stem($word)
{
    if (strlen($word) <= 2) {
        return $word;
    }

    $word = step1ab($word);
    $word = step1c($word);
    $word = step2($word);
    $word = step3($word);
    $word = step4($word);
    $word = step5($word);

    return $word;
}


function step1ab($word)
{
    //tahap 1a
    if (substr($word, -1) == 's') {
        replace($word, 'sses', 'ss')
        OR replace($word, 'ies', 'i')
        OR replace($word, 'ss', 'ss')
        OR replace($word, 's', '');
    }

    //tahap 1b
    if (substr($word, -2, 1) != 'e' OR !replace($word, 'eed', 'ee', 0)) { 
        $v = $GLOBALS['regex_vowel'];
        if (   preg_match("#$v+#", substr($word, 0, -3)) && replace($word, 'ing', '')
            OR preg_match("#$v+#", substr($word, 0, -2)) && replace($word, 'ed', '')) { 
            if (    !replace($word, 'at', 'ate')
                AND !replace($word, 'bl', 'ble')
                AND !replace($word, 'iz', 'ize')) {
                if (    doubleConsonant($word)
                    AND substr($word, -2) != 'll'
                    AND substr($word, -2) != 'ss'
                    AND substr($word, -2) != 'zz') {
                    $word = substr($word, 0, -1);
                } else if (m($word) == 1 AND cvc($word)) {
                    $word .= 'e';
                }
            }
        }
    }
    return $word;
}


function step1c($word)
{
    $v = $GLOBALS['regex_vowel'];

    if (substr($word, -1) == 'y' && preg_match("#$v+#", substr($word, 0, -1))) {
        replace($word, 'y', 'i');
    }

    return $word;
}


function step2($word)
{
    switch (substr($word, -2, 1)) {
        case 'a':
                replace($word, 'ational', 'ate', 0)
            OR replace($word, 'tional', 'tion', 0);
            break;

        case 'c':
                replace($word, 'enci', 'ence', 0)
            OR replace($word, 'anci', 'ance', 0);
            break;

        case 'e':
            replace($word, 'izer', 'ize', 0);
            break;

        case 'g':
            replace($word, 'logi', 'log', 0);
            break;

        case 'l':
                replace($word, 'entli', 'ent', 0)
            OR replace($word, 'ousli', 'ous', 0)
            OR replace($word, 'alli', 'al', 0)
            OR replace($word, 'bli', 'ble', 0)
            OR replace($word, 'eli', 'e', 0);
            break;

        case 'o':
                replace($word, 'ization', 'ize', 0)
            OR replace($word, 'ation', 'ate', 0)
            OR replace($word, 'ator', 'ate', 0);
            break;

        case 's':
                replace($word, 'iveness', 'ive', 0)
            OR replace($word, 'fulness', 'ful', 0)
            OR replace($word, 'ousness', 'ous', 0)
            OR replace($word, 'alism', 'al', 0);
            break;

        case 't':
                replace($word, 'biliti', 'ble', 0)
            OR replace($word, 'aliti', 'al', 0)
            OR replace($word, 'iviti', 'ive', 0);
            break;
    }

    return $word;
}



function step3($word)
{
    switch (substr($word, -2, 1)) {
        case 'a':
            replace($word, 'ical', 'ic', 0);
            break;

        case 's':
            replace($word, 'ness', '', 0);
            break;

        case 't':
                replace($word, 'icate', 'ic', 0)
            OR replace($word, 'iciti', 'ic', 0);
            break;

        case 'u':
            replace($word, 'ful', '', 0);
            break;

        case 'v':
            replace($word, 'ative', '', 0);
            break;

        case 'z':
            replace($word, 'alize', 'al', 0);
            break;
    }

    return $word;
}



function step4($word)
{
    switch (substr($word, -2, 1)) {
        case 'a':
            replace($word, 'al', '', 1);
            break;

        case 'c':
                replace($word, 'ance', '', 1)
            OR replace($word, 'ence', '', 1);
            break;

        case 'e':
            replace($word, 'er', '', 1);
            break;

        case 'i':
            replace($word, 'ic', '', 1);
            break;

        case 'l':
                replace($word, 'able', '', 1)
            OR replace($word, 'ible', '', 1);
            break;

        case 'n':
                replace($word, 'ant', '', 1)
            OR replace($word, 'ement', '', 1)
            OR replace($word, 'ment', '', 1)
            OR replace($word, 'ent', '', 1);
            break;

        case 'o':
            if (substr($word, -4) == 'tion' OR substr($word, -4) == 'sion') {
                replace($word, 'ion', '', 1);
            } else {
                replace($word, 'ou', '', 1);
            }
            break;

        case 's':
            replace($word, 'ism', '', 1);
            break;

        case 't':
                replace($word, 'ate', '', 1)
            OR replace($word, 'iti', '', 1);
            break;

        case 'u':
            replace($word, 'ous', '', 1);
            break;

        case 'v':
            replace($word, 'ive', '', 1);
            break;

        case 'z':
            replace($word, 'ize', '', 1);
            break;
    }

    return $word;
}



function step5($word)
{
    //tahap 5a Porter Stemmer
    if (substr($word, -1) == 'e') {
        if (m(substr($word, 0, -1)) > 1) {
            replace($word, 'e', '');

        } else if (m(substr($word, 0, -1)) == 1) {

            if (!cvc(substr($word, 0, -1))) {
                replace($word, 'e', '');
            }
        }
    }

    //tahap 5b Porter Stemmer
    if (m($word) > 1 AND doubleConsonant($word) AND substr($word, -1) == 'l') {
        $word = substr($word, 0, -1);
    }

    return $word;
}

function replace(&$str, $check, $repl, $m = null)
{
    $len = 0 - strlen($check);

    if (substr($str, $len) == $check) {
        $substr = substr($str, 0, $len);
        if (is_null($m) OR m($substr) > $m) {
            $str = $substr . $repl;
        }

        return true;
    }

    return false;
}


function m($str)
{
    $c = $GLOBALS['regex_consonant'];
    $v = $GLOBALS['regex_vowel'];

    $str = preg_replace("#^$c+#", '', $str);
    $str = preg_replace("#$v+$#", '', $str);

    preg_match_all("#($v+$c+)#", $str, $matches);

    return count($matches[1]);
}


function doubleConsonant($str)
{
    $c = $GLOBALS['regex_consonant'];

    return preg_match("#$c{2}$#", $str, $matches) AND $matches[0]{0} == $matches[0]{1};
}


function cvc($str)
{
    $c = $GLOBALS['regex_consonant'];
    $v = $GLOBALS['regex_vowel'];

    return     preg_match("#($c$v$c)$#", $str, $matches)
            AND strlen($matches[1]) == 3
            AND $matches[1]{2} != 'w'
            AND $matches[1]{2} != 'x'
            AND $matches[1]{2} != 'y';
}

?>