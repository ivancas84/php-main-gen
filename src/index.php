<?php

/**
 * Mapear la estructura de la base de datos en archivos JSON
 * Respetar el orden 
 *
 */
require("../config/config.php"); 

require_once("Tables.php");
$tables = new Tables();


require_once("Entity.php");
$self = new ClassEntity($tables->tablesInfo);
$self->generate();

require_once("Field.php");
foreach($tables->tablesInfo as $tableInfo){
    $gen = new GenerateClassField($tableInfo);
    $gen->generate();
}

require_once("entityTreeJson/EntityTreeJson.php");
$gen = new EntityTreeJson();
$gen->generate();

require_once("entityRelJson/EntityRelJson.php");
$gen = new EntityRelJson();
$gen->generate();


require_once("PublicScope.php");
$gen = new PublicScope();
$gen->generateIfNotExists();