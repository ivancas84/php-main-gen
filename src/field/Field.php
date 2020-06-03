<?php




/**
 * Generar clase
 */
class GenerateClassField extends GenerateFile{
  
  protected $tableName;
  protected $fieldInfo; //array. Informacion del field, directamente extraido de la base de datos
    /**
     * $field["field_name"] //nombre del field
     * $field["field_default"] //valor por defecto
     * $field["data_type"] //tipo de datos
     * $field["not_null"] //flag para indicar si es no nulo
     * $field["primary_key"] //flag para indicar si es clave primaria
     * $field["unique"] //flag para indicar si es clave unica
     * $field["foreign_key"] //flag para indicar si es clave foranea
     * $field["referenced_table_name"] //nombre de la tabla referenciada
     * $field["referenced_field_name"] //nombre del field referenciado
     */
  protected $fieldAlias; //string. alias del field (debe ser unico para la tabla)
  
  public function __construct($tableName, array $fieldInfo) {
    $this->tableName = $tableName;
    $this->fieldInfo = $fieldInfo;
    
    $dirName = $_SERVER["DOCUMENT_ROOT"]."/".PATH_ROOT."/class/model/field/" . snake_case_to("xxYy", $this->tableName) . "/";
    $fileName = snake_case_to("XxYy", $this->fieldInfo["field_name"]).".php";
    parent::__construct($dirName, $fileName);
  }
  
  
  protected function generateCode(){  
    $this->string .= "<?php

require_once(\"class/model/field/" . snake_case_to("xxYy", $this->tableName) . "/_" . snake_case_to("XxYy", $this->fieldInfo["field_name"]) . ".php\");

class Field" . snake_case_to("XxYy", $this->tableName) . snake_case_to("XxYy", $this->fieldInfo["field_name"]) . " extends _Field" . snake_case_to("XxYy", $this->tableName) . snake_case_to("XxYy", $this->fieldInfo["field_name"]) . " {
  
}
";
    
  }
  
  
  

}
