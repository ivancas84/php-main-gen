<?php
require_once("GenerateEntityRecursive.php");

//Comportamiento comun recursivo
abstract class GenerateEntityRecursiveFk extends GenerateEntityRecursive{

  //@override
  protected function hasRelations(){ return ($this->getEntity()->hasRelationsFk()) ? true : false; }

  //@override
  protected function recursive(Entity $entity, array $tablesVisited = NULL, $prefix = "", Field $field = null) {
    if (is_null($tablesVisited)) $tablesVisited = array();

    if(in_array($entity->getName(), $tablesVisited)) return;
    
    if (!empty($field)){
      $this->string .= $this->body($entity, $prefix, $field); //Genera codigo solo para las relaciones
      $prf = (empty($prefix)) ? $field->getAlias() : $prefix . "_" . $field->getAlias();
    } else {
      $prf = "";
    }


    $this->fk($entity, $tablesVisited, $prf);


  }
}
