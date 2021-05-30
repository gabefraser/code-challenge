<?php

namespace App\Helpers;

class CSVParsingHelper{
    /**
     * Public list of the valid characters within a transaction code.
     * 
     * @var array
     */
    private static $validChars = [
        '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K','L', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
    ];

    /**
     * Verification of the key
     * 
     * @param string $key
     * 
     * @return boolean
     */
    public static function verifyKey(string $key)
    {
        // If the key is less than 10, return false
        if (strlen($key) != 10) {
            return false;
        }

        // Check for the 9th digit to verify the key
        $checkDigit = self::generateCheckCharacter(substr(strtoupper($key), 0, 9));

        return ($key[9] == $checkDigit);
    }

    /**
     * Implementation of algorithm for check digit
     * 
     * @param string $input
     * 
     * @return boolean
     */
    public static function generateCheckCharacter(string $input)
    {
        $factor = 2;
        $sum = 0;
        $n = count(self::$validChars);

        // Starting from the right and working leftwards is easier since
        // the initial "factor" will always be "2"
        for ($i = strlen($input) - 1; $i >= 0; $i--) {   
            $codePoint = array_search($input[$i], self::$validChars);
            $addend = ($factor * $codePoint);
            
            // Alternate the "factor" that each "codePoint" is multiplied by
            $factor = (($factor == 2) ? 1 : 2);
            
            // Sum the digits of the "addend" as expressed in base "n"
            $addend = (($addend / $n) + ($addend % $n));
            $sum += $addend;
        }

        // Calculate the number that must be added to the "sum"
        // to make it divisible by "n"
        $remainder = ($sum % $n);
        $checkCodePoint = (($n - $remainder) % $n);
        return self::$validChars[$checkCodePoint];
    }
}