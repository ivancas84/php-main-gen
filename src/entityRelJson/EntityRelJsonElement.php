<?php

require_once("GenerateEntity.php");

class EntityRelJsonElement extends GenerateEntity {

  protected $subtree;

  public function __construct($entity, $subtree) {
    parent::__construct($entity);
    $this->subtree = $subtree;
  }


  public function generate() {
    $this->start();
    $this->body($this->subtree);
    $this->end();

    return $this->string;
  }

  protected function start() {
    $this->string .= "  \"" . $this->entity->getName() . "\": {
";
  }
  
  protected function body(array $fields) {
    foreach ($fields as $fieldId => $value) {
      $this->string .= "    \"{$fieldId}\": {\"field_name\":\"{$value['field_name']}\", \"entity_name\":\"{$value['entity_name']}\"},
";
      if(count($value["children"])) $this->body($value["children"]);
    }
   
  }

  protected function end() {
    $this->string = substr($this->string, 0,strrpos($this->string,","));

    $this->string .= "
  },

";
  }
}
