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
 *
 * @author  Hudhaifa Shatnawi <hudhaifa.shatnawi@gmail.com>
 * @version 1.0, Sep 21, 2016 - 9:58:19 AM
 * @link    https://github.com/samt/barcode
 */
class Codabar
        extends BarcodeBase {

    private static $binaryMap = array(
        '1' => [1, 1, 1, 1, 2, 2, 1],
        '2' => [1, 1, 1, 2, 1, 1, 2],
        '3' => [2, 2, 1, 1, 1, 1, 1],
        '4' => [1, 1, 2, 1, 1, 2, 1],
        '5' => [2, 1, 1, 1, 1, 2, 1],
        '6' => [1, 2, 1, 1, 1, 1, 2],
        '7' => [1, 2, 1, 1, 2, 1, 1],
        '8' => [1, 2, 2, 1, 1, 1, 1],
        '9' => [2, 1, 1, 2, 1, 1, 1],
        '0' => [1, 1, 1, 1, 1, 2, 2],
        '-' => [1, 1, 1, 2, 2, 1, 1],
        '$' => [1, 1, 2, 2, 1, 1, 1],
        ':' => [2, 1, 1, 1, 2, 1, 2],
        '/' => [2, 1, 2, 1, 1, 1, 2],
        '.' => [2, 1, 2, 1, 2, 1, 1],
        '*' => [1, 1, 1, 2, 1, 2, 2],
        '+' => [1, 1, 2, 1, 2, 1, 2],
        'A' => [1, 1, 2, 2, 1, 2, 1],
        'B' => [1, 2, 1, 2, 1, 1, 2],
        'C' => [1, 1, 1, 2, 1, 2, 2],
        'D' => [1, 1, 1, 2, 2, 2, 1],
        'E' => [1, 1, 1, 2, 2, 2, 1],
        'N' => [1, 2, 1, 2, 1, 1, 2],
        'T' => [1, 1, 2, 2, 1, 2, 1],
    );

    /**
     * @var data - to be set
     */
    private $code = '';
    private $binary = array();
    private $start = 'A';
    private $stop = 'B';
    private $font = 5;
    private $textHeight = 0;

    public function setData($data) {
        $d2 = strtoupper($data);

        $this->code = trim($d2);
        $chars = str_split($this->start . $d2 . $this->stop);

        foreach ($chars as $char) {
            foreach (self::$binaryMap[$char] as $v) {
                $this->binary[] = $v;
            }
            $this->binary[] = "1";
        }
    }

    /**
     * Draws the barcode image
     * @throws \LogicException
     */
    public function draw() {

        // Pad the edges of the barcode
        if ($this->humanText) {
            $this->textHeight = imagefontheight($this->font);
        } else {
            $this->textHeight = 0;
        }

        $codeLength = 0;
        foreach ($this->binary as $b) {
            $codeLength += $b;
        }

        $padding = 10;
        $barWidth = $this->x - ($padding * 2);
        $scale = $barWidth / $codeLength;

        $this->img = @imagecreate($this->x, $this->y);
        $white = imagecolorallocate($this->img, 255, 255, 255);
        $black = imagecolorallocate($this->img, 0, 0, 0);

        imagefill($this->img, 0, 0, $white);

        $x1 = $padding;

        for ($pos = 1; $pos <= count($this->binary); $pos++) {
            $x2 = $x1 + $this->binary[$pos - 1] * $scale;

            imagefilledrectangle($this->img, $x1, 0, $x2, $this->y - $this->textHeight, ($pos % 2 == 0 ? $white : $black));
            $x1 = $x2;
        }

        if ($this->humanText) {
            $this->drawCode();
        }
    }

    private function drawCode() {
        $text = $this->getPrintable();
        $black = imagecolorallocate($this->img, 0, 0, 0);
        $width = imagefontwidth($this->font) * strlen($text);

        $x = ($this->x - $width) / 2;

        imagestring($this->img, $this->font, $x, $this->y - $this->textHeight, $text, $black);
    }

    private function getPrintable() {
        $text = substr($this->code, 0, 1);
        $text .= ' ';
        $text .= substr($this->code, 1, 4);
        $text .= ' ';
        $text .= substr($this->code, 5, 5);
        $text .= ' ';
        $text .= substr($this->code, 10, 5);
        return $text;
    }

}