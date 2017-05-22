<?php
    
    function makeISBNarray ($start, $limit) {
	$iarray = array();
	for($x = $start; $x <= $limit; $x++){
	 	//pads string to 9 chars long
	 	$interimISBN = str_pad($x,9,'0',STR_PAD_LEFT);
	 	$iarray[] = make10($interimISBN);
	 }
	 return $iarray;
	 echo "completed FINALLY!!";
	
    }
    
    function makeISBNquerystyle ($start, $limit) {
	$pstring = makeISBN ($start, $limit);
	d($pstring);
	return "isbn:".str_replace(PHP_EOL, " OR isbn:", $pstring);
    }
    
    function makeISBN ($start, $limit) {
    	 /**
	 * Main Class
	 * creates X amount of ISBNs according to $limit set
	 * Can be easily put in a function to adhere to OO design
	 */
	 
	// $limit = 999999999;
	 $ISBNstring = "";
	 for($x = $start; $x <= $limit; $x++){
	 	//pads string to 9 chars long
	 	$interimISBN = str_pad($x,9,'0',STR_PAD_LEFT);
	 	$ISBNstring .= make10($interimISBN).PHP_EOL;

	 }
	 return substr($ISBNstring,0,-1);
	 echo "completed FINALLY!!";
    }	 
	 
	/**
     * Calculate the check digit of the ISBN-10 $isbn.
     *
     * @param string $isbn
     * @return string|int
     * @throws Exception
    */
    function make10($isbn)
    {
        if(is_string($isbn) === false) {
            throw new Exception('Invalid parameter type.');
        }
        //Verify length
        $isbnLength = strlen($isbn);
        if ($isbnLength < 9 or $isbnLength > 10) {
            throw new Exception('Invalid ISBN-10 format.');
        }
        //Calculate check digit
        //calculate sum product of values
        $check = 0;
        for ($i = 0; $i < 9; $i++) {
            if ($isbn[$i] === 'X') {
                $check += 10 * intval(10 - $i);
            } else {
                $check += intval($isbn[$i]) * intval(10 - $i);
            }
        }
        $check = 11 - $check % 11;
        if ($check === 10) {
            return $isbn.'X';
        } elseif ($check === 11) {
            return $isbn.'0';
        }
        
        return $isbn.$check;
    }
?>
