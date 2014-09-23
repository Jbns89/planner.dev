<?php

class Filestore {

    private $filename = '';
    private $is_csv = '';

    public function __construct($files = '')
    {
       
       if (substr($files, -3) == 'csv')
       {
           $this->is_csv = TRUE;
       }
       else
       {
           $this->is_csv = FALSE;
       }
       $this->filename = $files;
    }

    public function read()
    {
      if ($this->is_csv)
      {
        return $this->read_csv();
      }
      else
      {
        return $this->read_lines();
      }
    }
    
    public function write($array)
    {
        if ($this->is_csv)
        {
          $this->write_csv($array);
        }
        else
        {
          $this->write_lines($array);
        }
    }
    
    
    private function read_lines()
    {
       if (filesize($this->filename) > 0 )
       {
         $handle = fopen($this->filename, 'r');
         $content = trim(fread($handle,filesize($this->filename)));
         fclose($handle);
         return explode("\n",$content);
       } else {
        return [];
       }
       
    }
    


    private function write_lines($arrays)
     {
        $handle = fopen($this->filename, 'w');
        foreach ($arrays as $array) 
        {
            fwrite($handle, PHP_EOL . htmlspecialchars((strip_tags($array))));
        }
        fclose($handle);
     }




    private function read_csv()
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


    private function write_csv($arrays)
     {
        $handle = fopen($this->filename, 'w');
        foreach ($arrays as $rows) 
        {
            fputcsv($handle, $rows);
        }
        fclose($handle);
        return $arrays;
     }

 }


?>
