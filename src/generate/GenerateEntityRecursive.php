<?php

require_once("GenerateEntity.php");

abstract class GenerateEntityRecursive extends GenerateEntity{ //Comportamiento comun recursivo
  //public $hasRelations = false;

  protected function hasRelations(){ return ($this->getEntity()->hasRelations()) ? true : false; }

  public function generate(){
    if(!$this->hasRelations()) return "";

    $this->start();
    $this->recursive($this->getEntity());
    $this->end();

    return $this->string;
  }

  abstract protected function start();
  abstract protected function end();
  abstract protected function body(Entity $entity, $prefix, Field $field = null);


  protected function recursive(Entity $entity, array $tablesVisited = [], $prefix = "", Field $field = null){ //Metodo recursivo de generacion de codigo
    /**
     * Genera codigo solo para las relaciones
     * @param Entity $entity
     * @param array $tablesVisited
     * @param type $prefix
     * @return type
     */

    if(in_array($entity->getName(), $tablesVisited)) return;

    if (!empty($field)){
      $this->string .= $this->body($entity, $prefix, $field); //Genera codigo solo para las relaciones
      $prf = (empty($prefix)) ? $field->getAlias() : $prefix . "_" . $field->getAlias();
    } else {
      $prf = "";
    }


    $this->fk($entity, $tablesVisited, $prf);
    $this->u_($entity, $tablesVisited, $prf);
    /**
     * Para determinar que se este recorriendo una relacion en vez de la entidad actual, se utiliza el prefix
     * Si prefix esta vacio, significa que recien se comenzo a recorrer la tabla actual y no se debe generar el codigo
     * Si prefix no esta vacio, ya se esta recorriendo una relacion y debe generarse codigo
     * El codigo para la entidad actual se genera fuera de este metodo
     */


  }

  public function fk(Entity $entity, array $tablesVisited, $prefix){
    $fk = $entity->getFieldsFkNotReferenced($tablesVisited);
    array_push($tablesVisited, $entity->getName());

    foreach($fk as $field){
      $this->recursive($field->getEntityRef(), $tablesVisited, $prefix, $field);
    }
  }

  protected function u_(Entity $entity, array $tablesVisited, $prefix){
    $u_ = $entity->getFieldsOonNotReferenced($tablesVisited);
    $prf = (empty($prefix)) ? "" : $prefix . "_";
    array_push($tablesVisited, $entity->getName());

    foreach($u_ as $field) {
      $prf = (empty($prefix)) ? $field->getAlias("_") : $prefix . "_" . $field->getAlias("_");
      $this->body($field->getEntity(), $prf . $field->getAlias("_"), $field);
    }
  }
}
