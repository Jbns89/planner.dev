<?php

require '../dbconn_address.php';

if (!empty($_POST)) 
{
    
    $save = true;
    
    foreach ($_POST as $key => $value) {
        //echo "<p>{$_POST[$key]}</p>";
        $_POST[$key] = htmlspecialchars(strip_tags($value));
    }
    
    foreach ($_POST as $key => $value) {
        if (empty($value) || strlen($value) > 126) {
            $save = false;
            $error = "<p> Error! </p>";
        }
    }
    
    if ($save) {
        $newAddress = [
            $_POST['Name'],
            $_POST['Street'],
            $_POST['City'],
            $_POST['State'],
            $_POST['Zipcode'],
            $_POST['Phone']
        ];
        
        
        $stmt = $dbc->prepare('INSERT INTO contacts (name, street, city, state, zipcode, phone ) VALUES (:name, :street, :city, :state, :zip, :phone)');
        
        $stmt->bindValue(':name',  $_POST['Name'],  PDO::PARAM_STR);
        $stmt->bindValue(':street',  $_POST['Street'],  PDO::PARAM_STR);
        $stmt->bindValue(':city',  $_POST['City'],  PDO::PARAM_STR);
        $stmt->bindValue(':state',  $_POST['State'],  PDO::PARAM_STR);
        $stmt->bindValue(':zip',  $_POST['Zipcode'],  PDO::PARAM_STR);
        $stmt->bindValue(':phone',  $_POST['Phone'],  PDO::PARAM_STR);

        $stmt->execute();
    }
}

if (isset($_GET['remove'])) 
{
    $keyRemoved = $_GET['remove'];
    
    $stmt = $dbc->prepare("DELETE FROM contacts WHERE id = :id");
    
    $stmt->bindValue(':id', $keyRemoved, PDO::PARAM_INT);
    
    $stmt->execute();
}

/////////////FIX THIS AT SOME POINT//////////////////

if (count($_FILES) > 0 && $_FILES['uploaded']['error'] == UPLOAD_ERR_OK) 
{
    $upload_dir = '/vagrant/sites/planner.dev/public/uploads/';
    
    $filename = basename($_FILES['uploaded']['name']);
    
    $saved_filename = $upload_dir . $filename;
    
    move_uploaded_file($_FILES['uploaded']['tmp_name'], $saved_filename);
    
    //everytime you create a new object i.e new AddressDataStore you must 
    //pass it to a new variable
    $new_ads_bk = new AddressDataStore($saved_filename);
    
    $newAds = $new_ads_bk->read();
    
    $address_book = array_merge($address_book, $newAds);
    
    $ads_bk->write($address_book);
}

$stmt = $dbc->prepare("SELECT * FROM contacts");

$stmt->execute();

$address_book = $stmt->fetchall(PDO::FETCH_ASSOC);
    
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> Address Book </title>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/css2/address_style.css">
</head>
    <body>
        <div class="container">
            <h3>Welcome to your address book!</h3>
            <tbody>
                <table class="table table-hover">
                    
                    <tr>
                        <th>Name</th>
                        <th>Street</th>
                        <th>City</th>
                        <th>State</th>
                        <th>Zipcode</th>
                        <th>Phone</th>
                        <th>Remove</th>
                    </tr>

                    <?php foreach ($address_book as $index => $contacts) :?>
                        <!-- This causes a new row to be established 
                        each time it loops through the foreach -->
                        
                    <tr>
                        <td><?= $contacts['name']; ?></td>
                        <td><?= $contacts['street']; ?></td>
                        <td><?= $contacts['city']; ?></td>
                        <td><?= $contacts['state']; ?></td>
                        <td><?= $contacts['zipcode']; ?></td>
                        <td><?= $contacts['phone']; ?></td>
                        <td>
                            <a href=?remove=<?=$contacts['id']?>>
                                <span class="glyphicon glyphicon-trash"></span>
                            </a>
                        </td>
                    <?php endforeach; ?>
                        
                    </tr>
                </table>
                <?php  if (isset($_GET['remove'])): ?>
                    <div class="alert alert-info alert-dismissible col-md-6" role="alert">
                      <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                      <strong>You have sucessfully deleted a contact</strong>
                    </div>
                <?php endif; ?>
            </tbody>
        </div>
        <div class='container'>
            
            <?php if (isset($error)) : ?> 
            <h3><?= $error ?></h3>
            <?php endif; ?>
            
            <h3>Enter your new contact</h3>
            <form method="POST" action="address_book.php">
                <label for="Addresses"></label>
                    <input id="Addresses" name="Name" type="text" placeholder="Name">
                    <input id="Addresses" name="Street" type="text" placeholder="Street">
                    <input id="Addresses" name="City" type="text" placeholder="City">
                    <input id="Addresses" name="State" type="text" placeholder="State">
                    <input id="Addresses" name="Zipcode" type="text" placeholder="Zipcode">
                    <input id="Addresses" name="Phone" type="text" placeholder="(555)555-5555">
                <button type="submit" class="btn btn-info btn-sm">Add</button>
            </form>
            <form clas="uploads" method="POST" enctype="multipart/form-data" action="address_book.php">
                <label for="uploaded">File to upload </label>
                <input type="file" id="uploaded" name="uploaded">
                <input class="btn btn-info btn-sm" type="submit" value="Upload">
                <?php if (isset($saved_filename)): ?>
                    <!-- Here you would need single quotes in the anchor tag-->
                    <p>You can download your file <a href='uploads/<?=$filename?>'>here</a.</p>
                <?php endif; ?>
            </form>
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
            <!-- Include all compiled plugins (below), or include individual files as needed -->
            <script src="bootstrap/js/bootstrap.min.js"></script>
        </div>
    </body>
</html>
