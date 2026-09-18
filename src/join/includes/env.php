<?php
class Env {
  public $APP_ENV;
  public $JOIN_URL;
  public $CRM_URL;
  public $JS_URL;

  public function __construct() {
    $reflection = new ReflectionClass($this);
    foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
      $envVarName = $property->getName();
      $envValue = getenv($envVarName);

      if ($envValue === false || $envValue === null) {
        throw new Exception("Missing required environment variable: $envVarName");
      } else {
        $this->$envVarName = $envValue;
      }
    }
  }
}

$envConfig = new Env();
?>
