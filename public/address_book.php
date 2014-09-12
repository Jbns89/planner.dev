<?php

define('FILE', 'address_book.csv');

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
//You dont need to call the construct function here
//because the AddressDataStore will already do that.
//You also need to pass a variable in the 
//AddressDataStore object but I already have a default 
//file set.
$ads_bk = new AddressDataStore();
$address_book = $ads_bk->read_address_book();

if (!empty($_POST)) {
    $newAddress = [
        htmlspecialchars(strip_tags($_POST['Name'])),
        htmlspecialchars(strip_tags($_POST['Street'])),
        htmlspecialchars(strip_tags($_POST['City'])),
        htmlspecialchars(strip_tags($_POST['State'])),
        htmlspecialchars(strip_tags($_POST['Zipcode'])),
        htmlspecialchars(strip_tags($_POST['Phone']))
    ];
    $address_book[] = $newAddress;
    $ads_bk->write_address_book($address_book);
}

if (isset($_GET['remove'])) {
        $keyRemoved = $_GET['remove'];
        unset($address_book[$keyRemoved]);
        array_values($address_book);
        $ads_bk->write_address_book($address_book);
    }
if (count($_FILES) > 0 && $_FILES['uploaded']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = '/vagrant/sites/planner.dev/public/uploads/';
        $filename = basename($_FILES['uploaded']['name']);
        $saved_filename = $upload_dir . $filename;
        move_uploaded_file($_FILES['uploaded']['tmp_name'], $saved_filename);
        //everytime you create a new object i.e new AddressDataStore you must 
        //pass it to a new variable
        $new_ads_bk = new AddressDataStore($saved_filename);
        $newAds = $new_ads_bk->read_address_book();
        $address_book = array_merge($address_book, $newAds);
        $ads_bk->write_address_book($address_book);
    }
    
?>
<html>
<head>
    <title> Address Book </title>
    <link rel="stylesheet" type="text/css" href="/css2/address_style.css">
</head>
<body>
    <table>
        <tr>
            <th>Name</th>
            <th>Street</th>
            <th>City</th>
            <th>State</th>
            <th>Zipcode</th>
            <th>Phone</th>
            <th>Remove</th>
        </tr>
        <!-- Loop Time! -->
        <!-- Needed to nested foreach loops to access each ndividual 
        contact since it was a multidimensional array -->
            <?php foreach ($address_book as $index => $contacts) :?>
                <!-- This causes a new row to be established 
                each time it loops through the foreach -->
                <tr><?php foreach ($contacts as $key => $value) :?>
                    <!-- This causes new table data area with each value 
                    instead of it being just one long data cell -->
                    <td><?= $value?></td>
                <?php endforeach; ?>
                <td><a href=?remove=<?=$index?>>Delete</a></td>
            <?php endforeach; ?>
           
            </tr>
    </table>
    
    <h2>Enter your new contact</h2>
        <form method="POST" action="address_book.php">
            <label for="Addresses"></label>
                <input id="Addresses" name="Name" type="text" placeholder="Name">
                <input id="Addresses" name="Street" type="text" placeholder="Street">
                <input id="Addresses" name="City" type="text" placeholder="City">
                <input id="Addresses" name="State" type="text" placeholder="State">
                <input id="Addresses" name="Zipcode" type="text" placeholder="Zipcode">
                <input id="Addresses" name="Phone" type="text" placeholder="(555)555-5555">
            <button type="Submit">Add</button>
        </form>
        <form clas="uploads" method="POST" enctype="multipart/form-data" action="address_book.php">
            <label for="uploaded">File to upload </label>
            <input type="file" id="uploaded" name="uploaded">
            <input type="submit" value="Upload">
            <?php if (isset($saved_filename)): ?>
                    <!-- Here you would need single quotes in the anchor tag-->
                    <p>You can download your file <a href='uploads/<?=$filename?>'>here</a.</p>
            <?php endif; ?>
        </form>

</body>
</html>
