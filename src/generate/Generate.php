<?php

/**
 * Generar un elemento (archivo).
 */
abstract class Generate{
	
	protected $string;
	
	public function __construct() {
		$this->string = "";
	}

	/**
	 * Generar codigo y archivo
	 */
	public function generate(){
		return $this->string;
	}
  
  
  public function getString(){
    return $this->string;
  }
}
