<?php

require_once("class/model/db/My.php");
require_once("class/tools/Aliases.php");

class Tablas {

  protected $db; //DbSqlMy. Conexion con base de datos mysql
  protected $tablesInfo; //array. Informacion de las tablas
  protected $reserved = array(); //array. Tablas reservadas, no seran tenidas en cuenta en la generacion

  public function __construct()  {
    $this->db = new DbSqlMy (DATA_HOST, DATA_USER, DATA_PASS, DATA_DBNAME, DATA_SCHEMA);
    $this->reserved = explode(" ", DISABLE_ENTITIES);
    array_push($this->reserved, "transaction", "transaccion");
    $this->defineTablesInfo();
  }

  protected function defineTablesInfo(){
    $this->tablesInfo = array();
    $tableAliases = array();
    $tableNames = $this->db->tablesName(); //nombre de las tablas
    foreach($tableNames as $tableName){
      if(in_array($tableName, $this->reserved)) continue; //omitimos la tablas reservadas
      $tableInfo = array();
      $tableInfo["name"] = $tableName;
      $tableInfo["alias"] = Aliases::createAndGetAlias($tableName, $tableAliases, 4);
      array_push($tableAliases, $tableInfo["alias"]);

      $fieldAliases = array(); //alias de los fields de la tabla
      $fieldsInfo = $this->db-> fieldsInfo ( $tableName ) ;
      $fieldsInfo_ = array();

      foreach ( $fieldsInfo as $f) {
        $f["alias"] = Aliases::createAndGetAlias($f["field_name"], $fieldAliases);

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
        array_push($fieldAliases, $f["alias"]);
      }
      $tableInfo["fields"] = $fieldsInfo_;
      array_push($this->tablesInfo, $tableInfo);
    }
  }


  protected function entities(){
    require_once("tablas/entity/Main.php");
    require_once("tablas/entity/Entity.php");

    foreach($this->tablesInfo as $tableInfo){
      $self = new ClassEntityMain($tableInfo["name"], $tableInfo["alias"], $tableInfo["fields"]);
      $self->generate();

      $gen = new ClassEntity($tableInfo["name"]);
      $gen->generateIfNotExists();
    }
  }

  //
  protected function fields(){
    require_once("tablas/field/Field.php");
    require_once("tablas/field/Main.php");

    foreach($this->tablesInfo as $tableInfo){
      foreach ( $tableInfo["fields"] as $fieldInfo) {

        $gen = new GenerateClassFieldMain($tableInfo["name"], $fieldInfo);
        $gen->generate();

        $gen = new GenerateClassField($tableInfo["name"], $fieldInfo);
        $gen->generateIfNotExists();
      }
    }
  }


  public function structure(){
    require_once("tablas/Structure.php");
    $gen = new GenerateConfigStructure($this->tablesInfo);
    $gen->generate();
  }

  public function includes(){
    require_once("tablas/EntityClasses.php");
    $gen = new IncludeEntityClasses($this->tablesInfo);
    $gen->generate();

    require_once("tablas/UserEntityClasses.php");
    $gen = new UserEntityClasses($this->tablesInfo);
    $gen->generateIfNotExists();
  }



  //generar archivos
  public function generate(){
    $this->entities();
    $this->fields();
    $this->structure();
    //$this->includes();
  }


}
