<?php

require_once("GenerateFile.php");

/**
 * Generar estructura
 */
class GenFunctionGetEntityNames extends GenerateFile {

  protected $entityNames; //array. Nombres de tablas

  public function __construct(array $tablesInfo) {
    $this->entityNames = array_column($tablesInfo, "name");
    parent::__construct($_SERVER["DOCUMENT_ROOT"]."/".PATH_SRC."/function/","get_entity_names.php");
  }

  protected function generateCode() {
    $this->string .= "<?php

function get_entity_names () {
  return array (\"" . implode("\", \"", $this->entityNames) . "\");
}
";
  }


}
