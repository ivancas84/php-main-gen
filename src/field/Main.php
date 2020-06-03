<?php

require_once("function/snake_case_to.php");

class GenerateClassFieldMain extends GenerateFile {

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
     *
      * $field->getAlias() //alias del field
     * $field["field_type"] //tipo del field
     */
  protected $fieldType; //string. alias del field (debe ser unico para todos los fields de todas las tablas)

  public function __construct($tableName, array $fieldInfo) {
    $this->tableName = $tableName;
    $this->fieldInfo = $fieldInfo;

    $dirName = $_SERVER["DOCUMENT_ROOT"]."/".PATH_ROOT."/class/model/field/" . snake_case_to("xxYy", $this->tableName) . "/";
    $fileName = "_" . snake_case_to("XxYy", $this->fieldInfo["field_name"]) . ".php";

    parent::__construct($dirName, $fileName);
  }

  protected function start() {



    $this->string = "<?php

require_once(\"class/model/Field.php\");

class _Field" . snake_case_to("XxYy", $this->tableName) . snake_case_to("XxYy", $this->fieldInfo["field_name"]) . " extends Field {
";
  }

    protected function end(){
    $this->string .= "

}
" ;
  }

  protected function attributes(){
    $unique = ($this->fieldInfo["unique"])? "true":"false";
    $not_null = ($this->fieldInfo["not_null"])? "true":"false";
    $default = (!empty($this->fieldInfo["field_default"]) && strtolower($this->fieldInfo["field_default"]) != "null" ) ? "\"" . $this->fieldInfo["field_default"] . "\"" : "false";
    $length = (!empty($this->fieldInfo["length"])) ? "\"" . $this->fieldInfo["length"] . "\"" : "false";
    $main = ($this->fieldInfo["field_type"] == "pk") ? "true" : "false";

    $this->string .= "
  public \$type = \"" . $this->fieldInfo["data_type"] . "\";
  public \$fieldType = \"" . $this->fieldInfo["field_type"] . "\";
  public \$unique = " . $unique . ";
  public \$notNull = " . $not_null . ";
  public \$default = " . $default . ";
  public \$length = " . $length . ";
  public \$main = " . $main . ";
  public \$name = \"" . $this->fieldInfo["field_name"] . "\";
  public \$alias = \"" . $this->fieldInfo["alias"] . "\";

";


  }




  protected function getEntity(){
    $this->string .= "
  public function getEntity(){ return new " . snake_case_to("XxYy", $this->tableName) . "Entity; }
";
  }

  protected function getEntityRef(){
    if(($this->fieldInfo["field_type"] == "mu") || ($this->fieldInfo["field_type"] == "_u")){
      $this->string .= "
  public function getEntityRef(){ return new " . snake_case_to("XxYy", $this->fieldInfo["referenced_table_name"]) . "Entity; }
";
    }
  }

  /**
   * generar codigo de la clase
   * @return type
   */
  protected function generateCode(){
    $this->start();
    $this->attributes();
    $this->getEntity();
    $this->getEntityRef();
    $this->end();
  }


}
