<?php

class Filestore {

     public $filename = '';

     function __construct($files = '')
     {
         // Sets $this->filename
        $this->filename = $files;
     }

     /**
      * Returns array of lines in $this->filename
      */
     function read_lines()
     {
        $handle = fopen($this->filename, 'r');
        $content = trim(fread($handle,filesize($this->filename)));
        fclose($handle);
        return explode("\n",$content);
     }

     /**
      * Writes each element in $array to a new line in $this->filename
      */
     function write_lines($arrays)
     {
        $handle = fopen($this->filename, 'w');
        foreach ($arrays as $array) 
        {
            fwrite($handle, PHP_EOL . htmlspecialchars((strip_tags($array))));
        }
        fclose($handle);
     }

     /**
      * Reads contents of csv $this->filename, returns an array
      */
     function read_csv()
     {
        if (filesize($this->filename) == 0) 
        {
            $array = [];
        }
        
        else 
        {
            $handle = fopen($this->filename, 'r');
            
            while(!feof($handle)) 
            {
                $row = fgetcsv($handle);
                
                if (!empty($row)) 
                {
                    $array[] = $row;
                }
            }
            fclose($handle);
        }
        
        return $array;
     }

     /**
      * Writes contents of $array to csv $this->filename
      */
     function write_csv($array)
     {
        $handle = fopen($this->filename, 'w');
        foreach ($array as $rows) 
        {
            fputcsv($handle, $rows);
        }
        fclose($handle);
        return $array;
     }

 }


?>
