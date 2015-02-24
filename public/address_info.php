<?php

require '../dbconn_address.php';

if (!empty($_GET['id'])) {
    $infoId = (int) $_GET['id'];
                            //this is grabbing the first and last name 
    $stmt = $dbc->prepare("SELECT n.id, CONCAT(n.firstName, ' ', n.lastName) 
------this is grabbing the street, city, state and 
------zip from the addresses databse
    AS fullName, n.add_phone, a.address, a.city, a.state, a.zip 
    FROM names n
    -- this is where im actually grabbin the ids from
    -- the third table
    JOIN addresses_names na
        ON na.name_id = n.id
    JOIN addresses a
        ON a.id = na.address_id
    -- Here im saying I only want all the above info
    -- for the id that matched the contact they clicked on
    WHERE n.id = :id");
    // Here im binding the id with the $_GET['id'] they clicked on
    $stmt->bindValue(':id', $infoId, PDO::PARAM_INT);
    $stmt->execute();
    $con_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
} 
else 
{
    // redirect back to a safe URL
}


?>

<html>
<head>
    <title>Contact Info</title>
    
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> Address Book </title>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/css2/address_style.css">
    
</head>
<body>
    <div class="container">
    <table class="table table-hover">

        
        <?php foreach ($con_info as $index => $contacts) :?>
                        <!-- This causes a new row to be established 
                        each time it loops through the foreach -->
                <h3><?= $contacts['fullName']; ?></h3>
            <tr>
                <td><?= $contacts['add_phone']; ?></td>
            </tr>
            <tr>
                <td><?= $contacts['address']; ?></td>
            </tr>
            <tr>
                <td><?= $contacts['city']; ?></td>
            </tr>
            <tr>
                <td><?= $contacts['state']; ?></td>
            </tr>
            <tr>
                <td><?= $contacts['zip']; ?></td>
            </tr>
            <tr>
                <td><a href="/address_book.php">Back to address book</a></td>
            </tr>
        <?php endforeach; ?>
</div>

</body>
</html>
