<?php

require_once("GenerateFile.php");

/**
 * Generar estructura
 */
class EntityTreeJson extends GenerateFile {

  public function __construct() {
    parent::__construct($_SERVER["DOCUMENT_ROOT"]."/".PATH_ROOT."/model/","entity-tree.json");
  }

  protected function generateCode() {
    $container = new Container();
    $this->start();
    foreach($container->getStructure() as $entity) {
      $this->attribRel($entity);
    }
    $this->string = substr($this->string, 0,strrpos($this->string,","));
    
    $this->end();
  }

  protected function start() {
    $this->string .= "{
";
  }

  protected function attribRel($entity){
    require_once("entityTreeJson/EntityTreeJsonElement.php");
    $gen = new EntityTreeJsonElement($entity);
    $this->string .= $gen->generate();


  }

  protected function end() {
    $this->string .= "}
";
  }


}
