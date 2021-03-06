<?php

require_once("GenerateFile.php");

/**
 * Generar estructura
 */
class GenerateConfigStructure extends GenerateFile {

  protected $tablesInfo; //array. Nombres de tablas

  public function __construct(array $tablesInfo) {
    $this->tablesInfo = $tablesInfo;
    parent::__construct($_SERVER["DOCUMENT_ROOT"]."/".PATH_SRC."/class/model/entity/","structure.php");
  }

  protected function generateCode() {
    $this->string .= "<?php

require_once(\"class/Container.php\");

\$container = new Container();
\$structure = array (
" ;

    foreach ( $this->tablesInfo as $tableInfo ) {
      $this->string .= "  \$container->getEntity(\"" . $tableInfo["name"] . "\"),
" ;
    }

    $this->string .= ");

  Entity::setStructure(\$structure);

";
  }


}
