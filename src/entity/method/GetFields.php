<?php

require_once("function/snake_case_to.php");
class ClassEntity_getFields{
  
  protected $tableName; 
  protected $fieldsInfo; //array. Informacion de los fields
  
  public function  __construct($tableName, array $fieldsInfo, $methodName){
    $this->tableName =  $tableName;
    $this->fieldsInfo = $fieldsInfo;
    $this->methodName = $methodName;
    $this->string = "";
  }

  
  
  public function generate(){
    $this->start();
    $this->body();
    $this->end();

    return $this->string;
  }
  
  protected function start(){
    $this->string .= "
  public function " . $this->methodName . "(){
";
  }
  
  protected function body() {
    $this->string .= "    return array(
";
    
    foreach($this->fieldsInfo as $fieldInfo){
      $this->string .= "      Field::getInstanceRequire(\"" .$this->tableName . "\", \"" . $fieldInfo["field_name"] . "\"),
";      
    }

    $this->string .= "    );
";
  }
  
  protected function end(){
    $this->string .= "  }
";
  }
  

}

?>
