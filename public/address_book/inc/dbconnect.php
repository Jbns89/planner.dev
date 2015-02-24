<?php

// Get new instance of PDO object
$dbc = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);

// Tell PDO to throw exceptions on error
$dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$dbc->setAttribute(PDO::ATTR_STATEMENT_CLASS, array('CodeupPDOStatement'));

class CodeupPDOStatement extends PDOStatement {
    public function execute($params = null) {
        parent::execute($params);

        return $this;
    }

    public function conditionalBind($parameter, $value, $data_type = PDO::PARAM_STR) {
        if(empty($value) && !is_numeric($value)) {
            return $this->bindValue($parameter, null, PDO::PARAM_NULL);
        } else {
            return $this->bindValue($parameter, $value, $data_type);
        }
    }
}
