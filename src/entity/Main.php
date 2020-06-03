<?php

require_once("GenerateFile.php");
require_once("function/snake_case_to.php");
require_once("tablas/entity/method/GetFields.php");

//***** Generacion de clase TableStructure para una determinada tabla *****
class ClassEntityMain extends GenerateFile{

  protected $tableName; //string. Nombre de la tabla
  protected $tableAlias; //string. Alias de la tabla
  protected $fieldsInfo; //array. informacion de los fields

  public function __construct($tableName, $tableAlias, array $fieldsInfo) {
    $this->tableName = $tableName;
    $this->tableAlias = $tableAlias;
    $this->fieldsInfo =  $fieldsInfo;
    parent::__construct($_SERVER["DOCUMENT_ROOT"]."/".PATH_ROOT."/class/model/entity/" . snake_case_to("xxYy", $this->tableName) . "/", "Main.php");


  }

  protected function generateCode(){
    $this->start();
    $this->methodGetPk($this->tableName, $this->fieldsInfo);
    $this->methodGetFieldsNf($this->tableName, $this->fieldsInfo);
    $this->methodGetFieldsMu($this->tableName, $this->fieldsInfo);
    $this->methodGetFields_U($this->tableName, $this->fieldsInfo);
    $this->end();
  }

  protected function start() {
    $this->string .= "<?php

require_once(\"class/model/Entity.php\");
require_once(\"class/model/Field.php\");

class " . snake_case_to("XxYy", $this->tableName) . "EntityMain extends Entity {
  public \$name = \"" . $this->tableName . "\";
  public \$alias = \"" . $this->tableAlias . "\";
 
";
  }

  protected function end(){
    $this->string .= "

}
" ;
}

  protected function methodGetPk($tableName, array $fieldsInfo){
    foreach($fieldsInfo as $fieldInfo){
      if($fieldInfo["primary_key"]){
        $this->string .= "  public function getPk(){
    return Field::getInstanceRequire(\"" . $tableName . "\", \"" . $fieldInfo["field_name"] . "\");
  }
";
      }
    }
  }

  protected function methodGetFieldsNf($tableName, array $fieldsInfo){
    $fieldsInfoNf = array();
    foreach($fieldsInfo as $fieldInfo){
      if ((!$fieldInfo["primary_key"]) && (!$fieldInfo["foreign_key"])) {
        array_push($fieldsInfoNf, $fieldInfo);
      }
    }

    if(!count($fieldsInfoNf)) $this->string .= "";
    $gen = new ClassEntity_getFields($tableName, $fieldsInfoNf, "getFieldsNf");
    $this->string .= $gen->generate();
  }

  protected function methodGetFieldsMu($tableName, array $fieldsInfo){
    $fieldsInfoMu = array();
    foreach($fieldsInfo as $fieldInfo){

      if ( ( $fieldInfo["foreign_key"] ) && ( !$fieldInfo["unique"] ) && ( !$fieldInfo["primary_key"] ) ) {
        array_push($fieldsInfoMu, $fieldInfo);
      }
    }

    if(!count($fieldsInfoMu)) return;
    $self = new ClassEntity_getFields($tableName, $fieldsInfoMu, "getFieldsMu");
    $this->string .= $self->generate();
  }

  protected function methodGetFields_U($tableName, array $fieldsInfo){
    $fieldsInfo_U = array();
    foreach($fieldsInfo as $fieldInfo){
      if ( ( $fieldInfo["foreign_key"] ) && ( $fieldInfo["unique"] ) && ( !$fieldInfo["primary_key"] ) ) {
        array_push($fieldsInfo_U, $fieldInfo);
      }
    }

    if(!count($fieldsInfo_U)) return;
    $self = new ClassEntity_getFields($tableName, $fieldsInfo_U, "getFields_U");
    $this->string .= $self->generate();
  }


}
