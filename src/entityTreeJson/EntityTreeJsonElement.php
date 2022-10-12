<?php

require_once("GenerateEntity.php");

class EntityTreeJsonElement extends GenerateEntity {
  protected $names = [];

  protected function defineName($name, $alias = null){
    if (!in_array($name, $this->names)){
      array_push($this->names, $name);
      return $name;
    } else {
      if($alias) $name = $name . "_" . $alias;
      else {
        $match = preg_split('/(?<=\D)(?=\d)/', $name);
        $name = (count($match) < 2) ? $name . "1" :  $match[0] . strval((intval($match[1]) + 1));
      }
      return $this->defineName($name);
    }
  }

  public function generate() {
    if(!$this->getEntity()->hasRelationsFk()) return "";

    $this->start();
    $this->defineName($this->getEntity()->getName()); //inicializar names con la entidad actual
    $this->fk($this->entity, array(), "    ");
    $this->end();

    return $this->string;
  }

  protected function start() {
    $this->string .= "  \"" . $this->entity->getName() . "\": {";
  }
  
  protected function fk($entity, array $tablesVisited, $tab, $fieldAlias=null) {
    array_push ($tablesVisited, $entity->getName());
    $fk = $entity->getFieldsFkNotReferenced($tablesVisited);

    foreach ($fk as $field) {
      $fieldId = $this->defineName($field->getName(), $fieldAlias);

      $this->string .= "
" .$tab . "\"{$fieldId}\": {\"field_name\":\"{$field->getName()}\", \"entity_name\":\"{$field->getEntityRef()->getName()}\", \"children\":{";
      
      if(!in_array($field->getEntityRef()->getName(), $tablesVisited)) $this->fk($field->getEntityRef(), $tablesVisited, $tab . "  ", $field->getAlias());

      $this->string .= "}},";
    }
    
    if(!empty($fk)) $this->string = substr($this->string, 0, -1);
  }

  protected function recursive(Entity $entity, array $tablesVisited = NULL, $tab = "    ") {
    if(is_null($tablesVisited)) $tablesVisited = array();
    array_push ($tablesVisited, $entity->getName());
    $fk = $entity->getFieldsFkNotReferenced($tablesVisited);
    //$u_ = $entity->getFieldsU_NotReferenced($tablesVisited);

    $this->fk($fk, $tablesVisited, $tab);
  }

  protected function end() {
    $this->string .= "  },

";
  }
}
