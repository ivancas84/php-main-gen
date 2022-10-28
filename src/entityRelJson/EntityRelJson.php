<?php

require_once("GenerateFile.php");

/**
 * Generar estructura
 */
class EntityRelJson extends GenerateFile {

  public $tree;

  public function __construct() {
    $string = file_get_contents($_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . PATH_SRC . DIRECTORY_SEPARATOR . "model" . DIRECTORY_SEPARATOR . "entity-tree.json");
    $this->tree = json_decode($string, true);
    parent::__construct($_SERVER["DOCUMENT_ROOT"]."/".PATH_ROOT."/model/","entity-relations.json");
  }

  protected function generateCode() {
    $container = new Container();
    $this->start();
    foreach($container->getStructure() as $entity) $this->attribRel($entity);
    $this->end();
  }

  protected function start() {
    $this->string .= "{
";
  }

  protected function attribRel($entity){
    require_once("entityRelJson/EntityRelJsonElement.php");
    if(!array_key_exists($entity->getName(), $this->tree)) return;
    $gen = new EntityRelJsonElement($entity, $this->tree[$entity->getName()]);
    $this->string .= $gen->generate();
  }
  protected function end() {
    $this->string = substr($this->string, 0,strrpos($this->string,","));

    $this->string .= "
}
";
  }


}