<?php

namespace App\Utils;

class Slugger {

    private $toLower;

    public function __construct($toLower /*, $logger*/)
    {
       $this->toLower =  $toLower;

    }

    public function sluggify($strToConvert){

        //je souhaite pouvoir activer ou non la mise en minuscule de ma chaine
        if($this->toLower){
            $strToConvert = strtolower($strToConvert);
        }
        
        $convertedString = preg_replace( '/[^a-zA-Z0-9]+(?:-[a-zA-Z0-9]+)*/', '-', trim(strip_tags($strToConvert)));

        return $convertedString;
    }
}