<?php

require_once("GenerateFile.php");
require_once("class/model/db/My.php");

/**
 * Generar estructura
 */
class GenerateConfigStructure extends GenerateFile {

  protected $tablesInfo; //array. Nombres de tablas

  public function __construct(array $tablesInfo) {
    $this->tablesInfo = $tablesInfo;
    parent::__construct($_SERVER["DOCUMENT_ROOT"]."/".PATH_ROOT."/class/model/entity/","structure.php");
  }

  protected function generateCode() {
    $this->string .= "<?php

require_once(\"class/model/Entity.php\");

\$structure = array (
" ;

    foreach ( $this->tablesInfo as $tableInfo ) {
      $this->string .= "  Entity::getInstanceRequire(\"" . $tableInfo["name"] . "\"),
" ;
    }

    $this->string .= ");

  Entity::setStructure(\$structure);

";
  }


}
