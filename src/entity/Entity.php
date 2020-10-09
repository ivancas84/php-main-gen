<?php

require_once("GenerateFile.php");
require_once("function/snake_case_to.php");

class ClassEntity extends GenerateFile{

  protected $tableName; //string. Nombre de la tabla
  protected $tableAlias; //string. Alias de la tabla
  protected $fieldsInfo; //array. informacion de los fields

  public function __construct($tableName, $tableAlias, array $fieldsInfo) {
    $this->tableName = $tableName;
    $this->tableAlias = $tableAlias;
    $this->fieldsInfo =  $fieldsInfo;
    $dir = $_SERVER["DOCUMENT_ROOT"]."/".PATH_SRC."/class/model/entity/";
    $file = "_".snake_case_to("XxYy", $this->tableName).".php";
    parent::__construct($dir, $file);


  }

  protected function generateCode(){
    $this->start();
    $this->attribNf();
    $this->attribMu();
    $this->attrib_u();
    $this->attribNotNull();
    $this->attribUnique();
    $this->attribAdmin();
    $this->end();
  }

  protected function start() {
    $this->string .= "<?php

require_once(\"class/model/Entity.php\");
require_once(\"class/model/Field.php\");

class _" . snake_case_to("XxYy", $this->tableName) . "Entity extends Entity {
  public \$name = \"" . $this->tableName . "\";
  public \$alias = \"" . $this->tableAlias . "\";
";
  }

  protected function end(){
    $this->string .= "

}
" ;
}

  protected function attribAdmin() {
    $fields = [];
    foreach($this->fieldsInfo as $fieldInfo){
      array_push($fields, $fieldInfo["field_name"]);
    }
    $this->string .= "  public \$admin = ['" . implode("', '", $fields) . "'];

";
  }

  protected function attribNotNull() {
    $fields = [];
    foreach($this->fieldsInfo as $fieldInfo){
      if($fieldInfo["not_null"]) array_push($fields, $fieldInfo["field_name"]);
    }
    $this->string .= "  public \$notNull = ['" . implode("', '", $fields) . "'];
";
  }

  protected function attribUnique() {
    $unique = [];
    foreach($this->fieldsInfo as $fieldInfo){
      if($fieldInfo["unique"]) array_push($unique, $fieldInfo["field_name"]);
    }
    $this->string .= "  public \$unique = ['" . implode("', '", $unique) . "'];
";
  }

  protected function attribNf(){
    $fields = array();
    foreach($this->fieldsInfo as $fieldInfo){
      if ((!$fieldInfo["primary_key"]) && (!$fieldInfo["foreign_key"])) {
        array_push($fields, $fieldInfo["field_name"]);
      }
    }

    $value = (!count($fields)) ? "" : "'" . implode("', '", $fields) . "'";
    $this->string .= "  public \$nf = [{$value}];
";
  }

  protected function attribMu(){
    $fields = array();
    foreach($this->fieldsInfo as $fieldInfo){
      if ( ( $fieldInfo["foreign_key"] ) && ( !$fieldInfo["unique"] ) && ( !$fieldInfo["primary_key"] ) ) {
        array_push($fields, $fieldInfo["field_name"]);
      }
    }

    $value = (!count($fields)) ? "" : "'" . implode("', '", $fields) . "'";
    $this->string .= "  public \$mu = [{$value}];
";
  }

  protected function attrib_u(){
    $fields = array();
    foreach($this->fieldsInfo as $fieldInfo){
      if ( ( $fieldInfo["foreign_key"] ) && ( $fieldInfo["unique"] ) && ( !$fieldInfo["primary_key"] ) ) {
        array_push($fields, $fieldInfo["field_name"]);
      }
    }

    $value = (!count($fields)) ? "" : "'" . implode("', '", $fields) . "'";
    $this->string .= "  public \$_u = [{$value}];
";
  }



}
