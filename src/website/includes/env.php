<?php
class Env {
  public $APP_ENV;
  public $WEBSITE_SERVER_NAME;
  public $WEBSITE_URL;
  public $JOIN_URL;
  public $CRM_URL;
  public $JS_URL;
  public $DB_HOST;
  public $DB_PORT;
  public $DB_DATABASE_BS;
  public $DB_DATABASE_EN;
  public $DB_DATABASE_DE;
  public $DB_DATABASE_SR;
  public $DB_USER;
  public $DB_PASSWORD;

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
