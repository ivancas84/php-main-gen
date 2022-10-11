<?php

//controlador para generar la estructura php de mapeo de base de datos
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

