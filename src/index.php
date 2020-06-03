<?php

//controlador para generar la estructura php de mapeo de base de datos
require("../config/config.php"); 

require_once("tablas/Tablas.php");
$gen = new Tablas();
$gen->generate();
