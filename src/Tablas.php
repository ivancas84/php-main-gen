<?php

require_once("class/model/Db.php");
require_once("class/Container.php");
require_once("class/tools/Aliases.php");

class Tablas {

  public $tablesInfo; //array. Informacion de las tablas
  public $reserved = array(); //array. Tablas reservadas, no seran tenidas en cuenta en la generacion

  public function __construct()  {
    $this->container = new Container();
    $this->reserved = explode(" ", DISABLE_ENTITIES);
    array_push($this->reserved, "transaction", "transaccion");
    $this->defineTablesInfo();
  }

  protected function defineTablesInfo(){
    $this->tablesInfo = array();
    $tableAliases = array();
    $tableNames = $this->container->getDb()->tables_name(); //nombre de las tablas
    foreach($tableNames as $tableName){
      if(in_array($tableName, $this->reserved)) continue; //omitimos la tablas reservadas
      $tableInfo = array();
      $tableInfo["name"] = $tableName;
      $tableInfo["alias"] = Aliases::createAndGetAlias($tableName, $tableAliases, 4);
      array_push($tableAliases, $tableInfo["alias"]);

      $fieldAliases = array( $tableInfo["alias"] ); //alias de los fields de la tabla
      $fieldsInfo = $this->container->getDb()-> fields_info ( $tableName ) ;
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


  public function entities(){
    require_once("entity/Entity.php");

    foreach($this->tablesInfo as $tableInfo){
      $self = new ClassEntity($tableInfo["name"], $tableInfo["alias"], $tableInfo["fields"]);
      $self->generate();
    }
  }

  public function fields(){
    require_once("field/Field.php");

    foreach($this->tablesInfo as $tableInfo){
      foreach ( $tableInfo["fields"] as $fieldInfo) {

        $gen = new GenerateClassField($tableInfo["name"], $fieldInfo);
        $gen->generate();
      }
    }
  }

  public function functionGetEntityNames(){
    require_once("function/GetEntityNames.php");
    $gen = new GenFunctionGetEntityNames($this->tablesInfo);
    $gen->generate();
  }


}
