<?php

/*
 * MIT License
 *  
 * Copyright (c) 2016 Hudhaifa Shatnawi
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

/**
 * Codabar (USD-4, NW-7, and 2 of 7 code)
 * Many libraries use the following system which includes <b>13</b> digits plus a checksum; here is a description of the method used by Ameritech Library Services (Provo, Utah).
 * 
 *  - Digit 1 indicates the type of barcode:  2 = patron, 3 = item (book)
 *  - Digits 2-5 (4 digits) identify the institution
 *  - Digits 6-13 (8 digits ex. 00010 586) identify the individual patron or item
 *  - Digit 14 is the checksum
 * 
 * @see     http://www.makebarcode.com/specs/codabar.html
 * @author  Hudhaifa Shatnawi <hudhaifa.shatnawi@gmail.com>
 * @version 1.0, Sep 26, 2016 - 12:53:53 PM
 */
class CodabarNumber {

    /**
     * First digit indicates the type of barcode:
     * - 2 = patron
     * - 3 = item (book)
     * - etc..
     */
    private $digit1;

    /**
     * Identify the institution
     */
    private static $libraryID = 1310;

    /**
     * Identify the individual patron or item
     */
    private $sn = 0;

    /**
     * Indicates the type of barcode.
     */
    private $checkDigit = 0;

    /**
     * @param type $sn the identify the individual patron or item
     * @param type $digit1 indicates the type of barcode:  2 = patron, 3 = item (book)
     */
    function __construct($sn, $digit1) {
        $this->sn = $this->checkSerialNumber($sn);
        $this->digit1 = $digit1;
        $this->calculateCheckDigit();
    }

    /**
     * Checks the serial number length, if its less than 8 digits add zeros to the left of the number string.
     * 
     * @param type $sn
     * @return type
     */
    private function checkSerialNumber($sn) {
        // Generates a random serial number
        if (!$sn) {
            $sn = rand(100, 99999999);
        }

        $count = 8 - strlen($sn);
        if (!$count) {
            return $sn;
        }

        $prefix = '';
        for ($i = 0; $i < $count; $i++) {
            $prefix .='0';
        }

        return $prefix . $sn;
    }

    /**
     * Returns the complete codabar code
     * @return the complete codabar code
     */
    public function getCodabar() {
        return $this->getDigit1() . $this->getLibraryID() . $this->sn . $this->checkDigit;
    }

    /**
     * Returns the first digit which indicates the type of barcode.
     * @return the first digit which indicates the type of barcode.
     */
    public function getDigit1() {
        return $this->digit1;
    }

    /**
     * Returns the 4 (2-5) digits which identify the institution
     * @return the 4 (2-5) digits which identify the institution
     */
    public function getLibraryID() {
        return self::$libraryID;
    }

    /**
     * Returns the 8 (6-13) digits serial number
     * @return the 8 (6-13) digits serial number
     */
    public function getSerialNumber() {
        return $this->sn;
    }

    /**
     * Returns the last (14th) digit which indicates the checksum
     * @return the last (14th) digit which indicates the checksum
     */
    public function getCheckDigit() {
        $this->checkDigit;
    }

    /**
     * Calculates the checksum digit, start with the total set to zero and scan the 13 digits from left to right:
     * 
     * - If the digit is in an even-numbered position (2, 4, 6...) add it to the total.
     * - If the digit is in an odd-numbered position (1, 3, 5...) multiply the digit by 2.  
     *      If the product is equal to or greater than 10, subtract 9 from the product.  
     *      Then add the product to the total.
     * - After all digits have been processed, divide the total by 10 and take the remainder.
     * - If the remainder = 0, that is the check digit.  If the remainder is not zero, the check digit is 10 minus the remainder.
     */
    private function calculateCheckDigit() {
        $digits = str_split($this->getDigit1() . $this->getLibraryID() . $this->sn);
        $total = 0;

        foreach ($digits as $i => $d) {
            // Consider it as odd
            if ($i % 2 == 0) {
                $d *= 2;
                if ($d >= 10) {
                    $d -= 9;
                }
                $total += $d;
            } else {
                $total += $d;
            }
        }

        $mod = $total % 10;

        $this->checkDigit = $mod == 0 ? $mod : 10 - $mod;
    }

}