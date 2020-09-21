<?php

//controlador para generar la estructura php de mapeo de base de datos
require("../config/config.php"); 

require_once("Tablas.php");
$gen = new Tablas();

$gen->entities();
$gen->fields();
$gen->functionGetEntityNames();