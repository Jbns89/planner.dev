<?php

class AddressDataStore{
    public $filename = '';
    
    public function __construct($files = FILE) {
        $this->filename = $files;
    }
    
    public function read_address_book() {

        if (filesize($this->filename) == 0) {
            $array = [];
        }
        
        else {
            $handle = fopen($this->filename, 'r');
            
            while(!feof($handle)) {
                $row = fgetcsv($handle);
                
                if (!empty($row)) {
                    $array[] = $row;
                }
            }
            fclose($handle);
        }
        
        return $array;
    }
    public function write_address_book($array){
        $handle = fopen($this->filename, 'w');
        foreach ($array as $rows) {
            fputcsv($handle, $rows);
        }
        fclose($handle);
        return $array;
    }
}

?>
