<?php

require_once("GenerateFile.php");
/**
 * Generar un elemento (archivo).
 */
abstract class GenerateFileEntity extends GenerateFile{

  protected $entity;

  public function __construct($directorio, $nombreArchivo, Entity $entity){
    parent::__construct($directorio, $nombreArchivo);
    $this->entity = $entity;
  }



  function getEntity() {
    return $this->entity;
  }


}
