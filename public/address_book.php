<?php

define('FILE', 'address_book.csv');

$address_book = [
    ['Jill D','14396 Boonies Ln','San Antonio','TX','78221','210-555-5555'],
    ['Cy T','14396 Boonies Ln','San Antonio','TX','78221','210-555-5556'],
    ['Mike L','801 Shady Apts Rd','San Antonio','TX','78221','210-555-5557'],
    ['Michelle S','319 Michocana Dr','San Antonio','TX','78221','210-555-5558']
];

function save_csv($array, $file = FILE){
    $handle = fopen($file, 'w');
    foreach ($array as $rows) {
        fputcsv($handle, $rows);
    }
    fclose($handle);
    return $array;
}

if (!empty($_POST)) {
    $newAddress = [
        $_POST['Name'],
        $_POST['Street'],
        $_POST['City'],
        $_POST['State'],
        $_POST['Zipcode'],
        $_POST['Phone']
    ];
    array_push($address_book, $newAddress);
    save_csv($address_book);
}

function read_csv($array, $file = FILE){
    $handle = fopen(FILE, 'r');
    $array = [];
    while(!feof($handle)) {
      $array[] = fgetcsv($handle);
    }
    fclose($handle);
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
            <?php endforeach; ?></tr>
    </table>
    
    <h2>Enter your new contact</h2>
        <form method="post" action="address_book.php">
            <label for="Addresses"></label>
                <input id="Addresses" name="Name" type="text" placeholder="Name">
                <input id="Addresses" name="Street" type="text" placeholder="Street">
                <input id="Addresses" name="City" type="text" placeholder="City">
                <input id="Addresses" name="State" type="text" placeholder="State">
                <input id="Addresses" name="Zipcode" type="text" placeholder="Zipcode">
                <input id="Addresses" name="Phone" type="text" placeholder="Phone">
            <button type="Submit">Add</button>
        </form>
</body>
</html>
