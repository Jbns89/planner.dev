<?php

class Filestore {

    private $filename = '';
    private $is_csv = '';

    public function __construct($files = '')
    {
      
      // This is checking whether the uploaded file is csv or txt
       
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
    
//---------------------------------------------------------------------------------------
    // These are my two main functions, that will then call the 
    //read and write for either csv or txt
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
//---------------------------------------------------------------------------------------
    
    
 

//---------------------------------------------------------------------------------------
    // These functions read and write txt files
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
//---------------------------------------------------------------------------------------



//---------------------------------------------------------------------------------------
     //These functions read and write csv files
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
