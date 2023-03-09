<?php

require_once("GenerateFile.php");
/**
 * Generar estructura
 */
class PublicScope extends GenerateFile {

  public $container;

  public function __construct() {
    parent::__construct($_SERVER["DOCUMENT_ROOT"]."/".PATH_CONFIG."/","public_scope.php");
  }

  protected function generateCode() {
    $this->container = new Container();
    $this->start();
    $this->body();
    $this->end();
  }

  protected function start() {
    $this->string .= "<?php

function public_scope() {
  return [
";
  }

  protected function body(){
    foreach($this->container->getEntityNames() as $entity_name){
      $this->string .= "    '{$entity_name}.rwx',  
";  
    }
    
  }

  protected function end() {
    $this->string .= "  ];
}
";
  }


}
