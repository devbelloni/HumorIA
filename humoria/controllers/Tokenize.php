<?php

class Tokenize {

    private $tamanho;
    private $id;
    private $up;


    public function __construct($tamanho=15, $id="", $up=false) {
        $this->tamanho = $tamanho;
        $this->id = $id;
        $this->up = $up;

        // test
        // echo "<br>Tokenize construct!";
    }

    public function token() {

        // test
        // echo "<br>token function!";

        $characters = $this->id.'abcdefghijklmnopqrstuvwxyz0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $this->tamanho; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        if($this->up === true) {
          return strtoupper($this->id.$randomString);
        } else {
          return $this->id.$randomString;
        }
      }
}

?>