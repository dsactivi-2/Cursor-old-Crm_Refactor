<?php
    abstract class Bank{
        protected $currencyMultiplier;
        abstract function parse($inputFileName);
        abstract static function isFileValid($inputFileName);
        // constructor
        public function __construct($currencyMultiplier){
            $this->currencyMultiplier = $currencyMultiplier;
        }
    }
?>