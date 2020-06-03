<?php

require_once("Generate.php");

class IncludeEntityClasses extends GenerateFile{

  protected $tablesInfo; //array. Nombres de tabla
  public function __construct($tablesInfo){
    $this->tablesInfo = $tablesInfo;
    parent::__construct($_SERVER["DOCUMENT_ROOT"]."/".PATH_ROOT."config/", "entityClasses.php");

  }

  protected function generateCode(){
    $this->string = "<?php
//Para facilitar la manipulación de archivos, se define un archivo independiente para incluir las clases concretas susceptibles de sufrir modificaciones
//Se recomienda registrar en un archivo aparte los cambios efectuados a las clases Entity y Field
";

    foreach ( $this->tablesInfo as $tableInfo ) {

      $this->string .= "require_once(\"class/model/entity/" . str_replace(" ", "", lcfirst(ucwords(str_replace("_", " ", strtolower($tableInfo["name"]))))) . "/" . snake_case_to("XxYy", $tableInfo["name"]) . ".php\");
" ;

      foreach($tableInfo["fields"] as $fieldInfo){
        $this->string .= "require_once(\"class/model/field/" . str_replace(" ", "", lcfirst(ucwords(str_replace("_", " ", strtolower($tableInfo["name"]))))) . "/" . str_replace(" ", "", lcfirst(ucwords(str_replace("_", " ", strtolower($fieldInfo["field_name"]))))) . "/" . snake_case_to("XxYy", $fieldInfo["field_name"]) . ".php\");
" ;
      }

      $this->string .= "
";
    }
  }

}
