<?php 

    // var_dump($_POST); 
    // var_dump($_GET);
    // var_dump($_FILES);
    define('FILE', 'list_items.txt');
    
    require_once ('../inc/filestore.php');
    
    //Here you need to put the file you want
    //because there isn't a default set in the
    // filestore class 
    $lists = new Filestore(FILE);
    $items = $lists->read();



    if (isset($_POST['Todo'])) 
    {
        $items[] = $_POST['Todo'];
        $lists->write($items);
    }
    if (isset($_GET['remove'])) 
    {
        $keyRemoved = $_GET['remove'];
        unset($items[$keyRemoved]);
        $items = array_values($items);
        $lists->write($items);
    }
    if (count($_FILES) > 0 && $_FILES['uploaded']['error'] == UPLOAD_ERR_OK) 
    {
        $upload_dir = '/vagrant/sites/planner.dev/public/uploads/';
        
        $filename = basename($_FILES['uploaded']['name']);
        
        $saved_filename = $upload_dir . $filename;
        
        move_uploaded_file($_FILES['uploaded']['tmp_name'], $saved_filename);
        
        
        //open_file is finding the new items by 
        //$filename being added onto the the uploads/ path
        $itemsNew = $lists->read("uploads/" . $filename);
        
        
        //need to create a new variable so that once the arrays merge
        //they'll be saved and over written the items array 
        $items = array_merge($items, $itemsNew);
        
        $lists->write($items, FILE);
    }
?>

<!doctype html>
<html>
    <head>
        <title>ToDo List</title>
        <link rel="stylesheet" type="text/css" href="/css2/todo_style.css">
    </head>
    <body>
        <h2>To Do List for today:</h2>
        <ul>
            <?php foreach ($items as $key => $value): ?>
                    <!-- Don't need to have quotes around $key
                    you will cause items to not be removed--> 
                    <li><?= "$value"?><a href=?remove=<?=$key?>>Complete</a></li>
            <?php endforeach; ?>
        </ul>
        <h2>Enter your to do list item</h2>
        <form class="addItem" method="post" action="todo_list.php">
            <label for="Todo"></label>
                <input id="Todo" name="Todo" type="text" placeholder="Enter todo item">
            <button type="Submit">Add</button>
        </form>
        <form clas="uploads" method="POST" enctype="multipart/form-data" action="todo_list.php">
            <p>
                <label for="uploaded">File to upload </label>
                <input type="file" id="uploaded" name="uploaded">
            </p>
            <p>
                <input type="submit" value="Upload">
                <?php if (isset($saved_filename)): ?>
                    <!-- Here you would need single quotes in the anchor tag-->
                    <p>You can download your file <a href='uploads/<?=$filename?>'>here</a.</p>
                <?php endif; ?>
            </p>
        </form>
    </body>
</html>
