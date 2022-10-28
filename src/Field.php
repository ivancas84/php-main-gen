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

  public function __construct($table) {
    $this->table = $table;

    parent::__construct(
      $_SERVER["DOCUMENT_ROOT"]."/".PATH_ROOT."/model/fields/",
      "_". $this->table["name"] . ".json"
    );
  }

  /**
   * generar codigo de la clase
   */
  protected function generateCode(){

    $this->start();

    foreach($this->table["fields"] as $field ){
      $this->startField($field);
      $this->attributes($field);
      $this->attribEntityRefName($field);
      $this->attribDataType($field);
      $this->attribSubtype($field);
      $this->attribLength($field);
      $this->name($field);
      $this->endField();
    }
    $this->string = substr($this->string, 0,strrpos($this->string,","));

    $this->end();
  }

  protected function start() {
    $this->string = "{
";
  }

  protected function startField($field) {
    $this->string .= "  \"" . $field["field_name"] . "\": {
";
  }

  protected function endField(){
    $this->string .= "  },

" ;
  }

  protected function end(){
    $this->string .= "
}
" ;
  }

  protected function attributes($field){
    $default = (isset($field["field_default"])
                && $field["field_default"] != "" 
                && strtolower($field["field_default"]) != "null" ) ? "\"" . trim($field["field_default"], "'") . "\"" : "null";    

    $this->string .= "    \"type\": \"" . $field["data_type"] . "\",
    \"fieldType\": \"" . $field["field_type"] . "\",
    \"default\": " . $default . ",
    \"alias\": \"" . $field["alias"] . "\",
    \"entityName\": \"" . $this->table["name"] . "\",
";
  }


  /**
   * Separamos el nombre de los atributos principales para dejarlo al final y terminar sin la coma
   */
  protected function name($field){
    $this->string .= "    \"name\": \"" . $field["field_name"] . "\"
";
  }


  protected function attribEntityRefName($field){
    if(($field["field_type"] == "mu") || ($field["field_type"] == "_u")){
      $this->string .= "    \"entityRefName\": \"" . $field["referenced_table_name"] . "\",  
";
    }
  }


  protected function attribDataType(&$field){
    switch ( $field["data_type"] ) {
      case "smallint":
      case "mediumint":
      case "int":
      case "integer":
      case "serial":
      case "bigint": $field["generic_type"] = "integer"; break;
      case "tinyblob":
      case "blob":
      case "mediumblob":
      case "longblog": $field["generic_type"] = "blob"; break;
      case "varchar":
      case "char":
      case "string":
      case "tinytext": $field["generic_type"] = "string"; break;
      case "boolean":
      case "bool":
      case "tinyint": $field["generic_type"] = "boolean"; break;
      case "float":
      case "real":
      case "decimal": $field["generic_type"] = "float"; break;
      case "text": $field["generic_type"] = "text"; break;
      case "datetime":
      case "timestamp": $field["generic_type"] = "timestamp"; break;
      default: $field["generic_type"] = $field["data_type"];      
    }
    $this->string .= "    \"dataType\": \"" . $field["generic_type"] . "\",  
";
  }

  protected function attribSubtype(&$field){
    switch($field["field_type"]){
      case "pk":
      case "nf":
        switch($field["generic_type"]){
          case "string": $field["subtype"] = "text"; break;
          case "integer": $field["subtype"] = "integer"; break;
          case "float": $field["subtype"] = "float"; break;
          case "date": $field["subtype"] = "date"; break;
          case "timestamp": $field["subtype"] = "timestamp"; break;
          case "text": $field["subtype"] = "textarea"; break;
          case "blob": $field["subtype"] = "file_db"; break;
          case "boolean": $field["subtype"] = "checkbox"; break;
          case "time": $field["subtype"] = "time"; break;
          case "year": $field["subtype"] = "year"; break;
          default: $field["subtype"] = false; break;
        }
      break;

      case "fk": case "mu": case "_u":
        $field["subtype"] = "typeahead";
      break;
    }
    $this->string .= "    \"subtype\": \"" . $field["subtype"] . "\",  
";
  }

  protected function attribLength($field){
    if(empty($field["length"])) {;
      switch ($field["data_type"]) {
        case "text": case "blob": return $field["length"] = 65535; //bytes (64KB)
        case "mediumtext": case "mediumblob": return $field["length"] = 16777215; //bytes (16MB)
        case "longtext": case "longblog": return $field["length"] = 4294967295; //bytes (4GB)
      }

      switch($field["subtype"]){
        //case "text": return $this->length = 45;
        case "cuil": return $field["length"] = 11;
        case "dni": return $field["length"] = 8;
      }
    }
    if(!empty($field["length"])) {
      $field["length"] = str_replace(",",".",$field["length"]);
      $this->string .= "    \"length\": " . $field["length"] . ",
";
    } 
  }




}
