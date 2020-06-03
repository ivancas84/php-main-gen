<?php

require_once("Generate.php");

class UserEntityClasses extends GenerateFile{

  public function __construct(){
    parent::__construct($_SERVER["DOCUMENT_ROOT"]."/".PATH_ROOT."config/", "userEntityClasses.php");
  }

  protected function generateCode(){
    $this->string = "<?php
//Entitys y Fields definidos por el usuario se incluyen en este archivo
";

  }

}
