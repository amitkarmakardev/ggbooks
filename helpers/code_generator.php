<?php


function generateISBN10($isbn_part)
{
    $interimISBN = str_pad($isbn_part, 9, '0', STR_PAD_LEFT);

    if (is_string($interimISBN) === false) {
        throw new Exception('Invalid parameter type.');
    }

    //Verify length
    $isbnLength = strlen($interimISBN);
    if ($isbnLength < 9 or $isbnLength > 10) {
        throw new Exception('Invalid ISBN-10 format.');
    }

    //Calculate check digit

    $check = 0;

    for ($i = 0; $i < 9; $i++) {
        if ($interimISBN[$i] === 'X') {
            $check += 10 * intval(10 - $i);
        } else {
            $check += intval($interimISBN[$i]) * intval(10 - $i);
        }
    }

    $check = 11 - ($check % 11);

    if ($check === 10) {
        return $interimISBN . 'X';
    } elseif ($check === 11) {
        return $interimISBN . '0';
    }

    return $interimISBN . $check;
}

function generateISSN($issn_part)
{
    $interimISSN = str_pad($isbn_part, 7, '0', STR_PAD_LEFT);

    if (is_string($interimISSN) === false) {
        throw new Exception('Invalid parameter type.');
    }

    //Verify length
    $issnLength = strlen($interimISSN);
    if ($issnLength != 7) {
        throw new Exception('Invalid ISSN-8 format.');
    }

    //Calculate check digit

    $check = 0;

    for ($i = 8; $i > 1; $i--) {
        $check += intval($interimISSN[$i]) * intval($i);
    }

    $check = 11 - ($check % 11);

    if ($check === 10) {
        return $interimISSN . 'X';
    } elseif ($check === 11) {
        return $interimISSN . '0';
    }

    return $interimISSN . $check;
}
