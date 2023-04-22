<?php

/**
 * Generar un elemento (archivo).
 */
abstract class GenerateEntity extends Generate{
  
  protected $entity;

  public function __construct(Entity $entity){
    $this->entity = $entity;
    parent::__construct();
  }

  function getEntity() { return $this->entity; }

  function setEntity(Entity $entity) { $this->entity = $entity; }
}
