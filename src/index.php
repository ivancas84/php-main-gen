<?php

//controlador para generar la estructura php de mapeo de base de datos
require("../config/config.php"); 
require("class/model/Db.php"); 


require_once("Tablas.php");
$gen = new Tablas();
$gen->db = new Db();
$gen->generate();
