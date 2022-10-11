<?php

require_once("class/model/Db.php");
require_once("class/Container.php");
require_once("class/tools/Aliases.php");

class Tables {

  public $tablesInfo; //array. Informacion de las tablas
  public $reserved = []; //array. Tablas reservadas, no seran tenidas en cuenta en la generacion
  public $tableAliases = [];
  public $fieldAliases = [];

  public function __construct()  {
    $this->container = new Container();
    $this->reserved = explode(" ", DISABLE_ENTITIES);
    array_push($this->reserved, "transaction", "transaccion");
    $this->defineTablesInfo();
  }

  /**
   * Retornar array con el nombre de las tablas de la base de datos
   */
  public function tablesName () {  
    $sql = "SHOW TABLES FROM " . $this->container->getDb()->dbname . ";";
    $result = $this->container->getDb()->query($sql);
    $response = (!$result) ? false : $this->container->getDb()->fetch_all_columns ( $result , 0 );
    $result->free();
    return $response;
  }

  /**
   * Retornar array multiple con informacion de los fields de una tabla de la base de datos
   * @param string $table: nombre de la tabla
   * @return false|array
   * No esta contemplado en la consulta a la base de datos el caso de que la pk sea clave foranea.
   */
  public function fieldsInfo ( $table ) {
     $db = $this->container->getDb();
      $sql = "
  SELECT
  DISTINCT COLUMNS.COLUMN_NAME, COLUMNS.COLUMN_DEFAULT, COLUMNS.IS_NULLABLE, COLUMNS.DATA_TYPE, COLUMNS.COLUMN_TYPE, COLUMNS.CHARACTER_MAXIMUM_LENGTH, COLUMNS.NUMERIC_PRECISION, COLUMNS.NUMERIC_SCALE, COLUMNS.COLUMN_KEY, COLUMNS.EXTRA,
  SUB.REFERENCED_TABLE_NAME, SUB.REFERENCED_COLUMN_NAME, COLUMNS.ORDINAL_POSITION
  FROM INFORMATION_SCHEMA.COLUMNS
  LEFT OUTER JOIN (
  SELECT KEY_COLUMN_USAGE.COLUMN_NAME, KEY_COLUMN_USAGE.REFERENCED_TABLE_NAME, KEY_COLUMN_USAGE.REFERENCED_COLUMN_NAME
  FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
  WHERE (CONSTRAINT_NAME != 'PRIMARY') AND (REFERENCED_TABLE_NAME IS NOT NULL) AND (REFERENCED_COLUMN_NAME IS NOT NULL)
  
  AND (KEY_COLUMN_USAGE.TABLE_SCHEMA = '" .  $db->dbname . "') AND (KEY_COLUMN_USAGE.TABLE_NAME = '" . $table . "')
  ) AS SUB ON (COLUMNS.COLUMN_NAME = SUB.COLUMN_NAME)
  WHERE (COLUMNS.TABLE_SCHEMA = '" .  $db->dbname . "') AND (COLUMNS.TABLE_NAME = '" . $table . "')
  ORDER BY COLUMNS.ORDINAL_POSITION;";
  
      $result = $db->query($sql);
      $r_aux =  $result->fetch_all(MYSQLI_ASSOC) ;
      $result->free();
      $r = array () ;
  
      foreach ($r_aux as $field_aux ) {
        $field = array ( ) ;
        $field["field_name"] = $field_aux["COLUMN_NAME"] ;
        $field["field_default"] = $field_aux["COLUMN_DEFAULT"] ;
        $field["data_type"] = $field_aux["DATA_TYPE"] ;
        $field["not_null"] = (!settypebool( $field_aux["IS_NULLABLE"] )) ? true : false;
        $field["primary_key"] = ($field_aux["COLUMN_KEY"] == "PRI" ) ? true : false;
        $field["unique"] = ($field_aux["COLUMN_KEY"] == "UNI" ) ? true : false;
        $field["foreign_key"] = (!empty($field_aux["REFERENCED_COLUMN_NAME"])) ? true : false;
        $field["referenced_table_name"] = $field_aux["REFERENCED_TABLE_NAME"] ;
        $field["referenced_field_name"] = $field_aux["REFERENCED_COLUMN_NAME"] ;
  
        if ( !empty( $field_aux["CHARACTER_MAXIMUM_LENGTH"] ) ) {
          $field["length"] = $field_aux["CHARACTER_MAXIMUM_LENGTH"] ;
        } elseif ( !empty( $field_aux["NUMERIC_PRECISION"] ) ) {
          $sub = substr($field_aux["COLUMN_TYPE"] , strpos($field_aux["COLUMN_TYPE"],"(")+strlen("("),strlen($field_aux["COLUMN_TYPE"]));
          $length = substr($sub,0,strpos($sub,")"));
          if(intval($field_aux["NUMERIC_PRECISION"]) <= intval($length)){
            $field["length"] = $field_aux["NUMERIC_PRECISION"];
          } else {
            $field["length"] = $length;
          }
  
          if ( (!empty ( $field_aux["NUMERIC_SCALE"])) && ( $field_aux["NUMERIC_SCALE"] != '0' ) ) {
            $field["length"] .= "," . $field_aux["NUMERIC_SCALE"] ;
          }
        } else {
          $field["length"] = false ;
        }
  
        array_push ( $r, $field);
      }
  
      return $r ;
    }


  public function defineTablesInfo(){
    $this->tablesInfo = array();
    $tableNames = $this->tablesName(); //nombre de las tablas
    foreach($tableNames as $tableName){
      if(in_array($tableName, $this->reserved)) continue; //omitimos la tablas reservadas
      $tableInfo = array();
      $tableInfo["name"] = $tableName;
      $tableInfo["alias"] = Aliases::createAndGetAlias($tableName, $this->tableAliases, 4);
      array_push($this->tableAliases, $tableInfo["alias"]);

      $this->fieldAliases[$tableName] = array( $tableInfo["alias"] ); //alias de los fields de la tabla
      $fieldsInfo = $this->fieldsInfo ( $tableName ) ;
      $fieldsInfo_ = array();

      foreach ( $fieldsInfo as $f) {
        $f["alias"] = Aliases::createAndGetAlias($f["field_name"], $this->fieldAliases[$tableName]);
        array_push($this->fieldAliases[$tableName], $f["alias"]);

        if($f["primary_key"]){
          $f["unique"] = true;
          $f["field_type"] = "pk";
        } else if ((!$f["primary_key"]) && (!$f["foreign_key"])) {
          $f["field_type"] = "nf";
        } else if ( ( $f["foreign_key"] ) && ( !$f["unique"] ) && ( !$f["primary_key"] ) ){
          if(in_array($f["referenced_table_name"], $this->reserved)) continue; //omitimos la tablas reservadas
          $f["unique"] = false;
          $f["field_type"] = "mu";
        } else if ( ( $f["foreign_key"] ) && ( $f["unique"] ) && ( !$f["primary_key"] ) ) {
          if(in_array($f["referenced_table_name"], $this->reserved)) continue; //omitimos la tablas reservadas
          $f["unique"] = true;
          $f["field_type"] = "_u";
        }

        array_push($fieldsInfo_, $f);
      }
      $tableInfo["fields"] = $fieldsInfo_;
      array_push($this->tablesInfo, $tableInfo);
    }
  }

}
