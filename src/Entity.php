<?php

require_once("GenerateFile.php");
require_once("function/snake_case_to.php");

class ClassEntity extends GenerateFile{

  protected $tableName; //string. Nombre de la tabla
  protected $tableAlias; //string. Alias de la tabla
  protected $fieldsInfo; //array. informacion de los fields

  public function __construct($tables) {
    $this->tables = $tables;
    $dir = $_SERVER["DOCUMENT_ROOT"]."/".PATH_ROOT."/model/";
    $file = "_entities.json";
    parent::__construct($dir, $file);
  }

  protected function generateCode(){
    $this->start();

    foreach($this->tables as $table){
      $this->startTable($table);
      $this->attribNf($table);
      $this->attribMu($table);
      $this->attrib_u($table);
      $this->attribNotNull($table);
      $this->attribUnique($table);
      $this->endTable();

    }
    $this->string = substr($this->string, 0,strrpos($this->string,","));

    $this->end();
  }

  protected function start() {
    $this->string .= "{
";
  }

  protected function startTable($table) {
    $this->string .= "  \"" . $table["name"] . "\": {
    \"name\": \"" . $table["name"] . "\",
    \"alias\": \"" . $table["alias"] . "\",
";
  }

  protected function endTable(){
    $this->string .= "  },

" ;
}

  protected function end(){
    $this->string .= "
}
" ;
  }

  protected function attribNotNull($table) {
    $fields = [];
    foreach($table["fields"] as $field){
      if($field["not_null"]) array_push($fields, $field["field_name"]);
    }
    $this->string .= "    \"not_null\": [\"" . implode("\", \"", $fields) . "\"],
";
  }

  protected function attribUnique($table) {
    $unique = [];
    foreach($table["fields"] as $field){
      if($field["unique"]) array_push($unique, $field["field_name"]);
    }
    $this->string .= "    \"unique\": [\"" . implode("\", \"", $unique) . "\"]
";
  }

  protected function attribNf($table){
    $fields = array();
    foreach($table["fields"]  as $field){
      if ((!$field["primary_key"]) && (!$field["foreign_key"])) {
        array_push($fields, $field["field_name"]);
      }
    }

    $value = (!count($fields)) ? "" : "\"" . implode("\", \"", $fields) . "\"";
    $this->string .= "    \"nf\": [{$value}],
";
  }

  protected function attribMu($table){
    $fields = array();
    foreach($table["fields"] as $field){
      if ( ( $field["foreign_key"] ) && ( !$field["unique"] ) && ( !$field["primary_key"] ) ) {
        array_push($fields, $field["field_name"]);
      }
    }

    $value = (!count($fields)) ? "" : "\"" . implode("\", \"", $fields) . "\"";
    $this->string .= "    \"mo\": [{$value}],
";
  }

  protected function attrib_u($table){
    $fields = array();
    foreach($table["fields"] as $field){
      if ( ( $field["foreign_key"] ) && ( $field["unique"] ) && ( !$field["primary_key"] ) ) {
        array_push($fields, $field["field_name"]);
      }
    }

    $value = (!count($fields)) ? "" : "\"" . implode("\", \"", $fields) . "\"";
    $this->string .= "    \"oo\": [{$value}],
";
  }


}
