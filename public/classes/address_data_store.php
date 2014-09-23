<?php

// TODO: require Filestore class

 class AddressDataStore extends Filestore 
 {
    function __construct ($files = '')
    {
        //this is overwriting the parent __construct
        //so any file names are automatically set to lowercase
        parent::__construct(strtolower($files));
    }
    
 }

?>
