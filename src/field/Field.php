<?php

require_once("function/snake_case_to.php");

class GenerateClassField extends GenerateFile {

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

    $dirName = $_SERVER["DOCUMENT_ROOT"]."/".PATH_SRC."/class/model/field/" . snake_case_to("xxYy", $this->tableName) . "/";
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
    $default = (isset($this->fieldInfo["field_default"])
                && $this->fieldInfo["field_default"] != "" 
                && strtolower($this->fieldInfo["field_default"]) != "null" ) ? "\"" . trim($this->fieldInfo["field_default"], "'") . "\"" : "null";    

    $this->string .= "
  public \$type = \"" . $this->fieldInfo["data_type"] . "\";
  public \$fieldType = \"" . $this->fieldInfo["field_type"] . "\";
  public \$default = " . $default . ";
  public \$name = \"" . $this->fieldInfo["field_name"] . "\";
  public \$alias = \"" . $this->fieldInfo["alias"] . "\";
  public \$entityName = \"" . $this->tableName . "\";
";
  }




  protected function attribEntityRefName(){
    if(($this->fieldInfo["field_type"] == "mu") || ($this->fieldInfo["field_type"] == "_u")){
      $this->string .= "  public \$entityRefName = \"" . $this->fieldInfo["referenced_table_name"] . "\";  
";
    }
  }

  protected function attribDataType(){
    switch ( $this->fieldInfo["data_type"] ) {
      case "smallint":
      case "mediumint":
      case "int":
      case "integer":
      case "serial":
      case "bigint": $this->fieldInfo["generic_type"] = "integer"; break;
      case "tinyblob":
      case "blob":
      case "mediumblob":
      case "longblog": $this->fieldInfo["generic_type"] = "blob"; break;
      case "varchar":
      case "char":
      case "string":
      case "tinytext": $this->fieldInfo["generic_type"] = "string"; break;
      case "boolean":
      case "bool":
      case "tinyint": $this->fieldInfo["generic_type"] = "boolean"; break;
      case "float":
      case "real":
      case "decimal": $this->fieldInfo["generic_type"] = "float"; break;
      case "text": $this->fieldInfo["generic_type"] = "text"; break;
      case "datetime":
      case "timestamp": $this->fieldInfo["generic_type"] = "timestamp"; break;
      default: $this->fieldInfo["generic_type"] = $this->fieldInfo["data_type"];      
    }
    $this->string .= "  public \$dataType = \"" . $this->fieldInfo["generic_type"] . "\";  
";
  }

  protected function attribSubtype(){
    switch($this->fieldInfo["field_type"]){
      case "pk":
      case "nf":
        switch($this->fieldInfo["generic_type"]){
          case "string": $this->fieldInfo["subtype"] = "text"; break;
          case "integer": $this->fieldInfo["subtype"] = "integer"; break;
          case "float": $this->fieldInfo["subtype"] = "float"; break;
          case "date": $this->fieldInfo["subtype"] = "date"; break;
          case "timestamp": $this->fieldInfo["subtype"] = "timestamp"; break;
          case "text": $this->fieldInfo["subtype"] = "textarea"; break;
          case "blob": $this->fieldInfo["subtype"] = "file_db"; break;
          case "boolean": $this->fieldInfo["subtype"] = "checkbox"; break;
          case "time": $this->fieldInfo["subtype"] = "time"; break;
          case "year": $this->fieldInfo["subtype"] = "year"; break;
          default: $this->fieldInfo["subtype"] = false; break;
        }
      break;

      case "fk": case "mu": case "_u":
        $this->fieldInfo["subtype"] = "typeahead";
      break;
    }
    $this->string .= "  public \$subtype = \"" . $this->fieldInfo["subtype"] . "\";  
";
  }

  protected function attribLength(){
    if(empty($this->fieldInfo["length"])) {;
      switch ($this->fieldInfo["data_type"]) {
        case "text": case "blob": return $this->fieldInfo["length"] = 65535; //bytes (64KB)
        case "mediumtext": case "mediumblob": return $this->fieldInfo["length"] = 16777215; //bytes (16MB)
        case "longtext": case "longblog": return $this->fieldInfo["length"] = 4294967295; //bytes (4GB)
      }

      switch($this->fieldInfo["subtype"]){
        //case "text": return $this->length = 45;
        case "cuil": return $this->fieldInfo["length"] = 11;
        case "dni": return $this->fieldInfo["length"] = 8;
      }
    }
    if(!empty($this->fieldInfo["length"])) {
      $this->string .= "  public \$length = \"" . $this->fieldInfo["length"] . "\";  
";
    } 
  }


  /**
   * generar codigo de la clase
   */
  protected function generateCode(){
    $this->start();
    $this->attributes();
    $this->attribEntityRefName();
    $this->attribDataType();
    $this->attribSubtype();
    $this->attribLength();
    $this->end();
  }


}
