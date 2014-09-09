<?php 
	var_dump($_POST); 
	var_dump($_GET);
	var_dump($_FILES);
	define('FILE', 'list_items.txt');
	
	function open_file($file) 
	{                              
	   $handle=fopen($file, 'r');
	   $content=trim(fread($handle,filesize($file)));
	   fclose($handle);
	   return explode("\n",$content);
	}
	
	function save_file($items, $file = FILE)
	{
		$handle=fopen($file, 'w');
        foreach ($items as $item) 
        {
           fwrite($handle, PHP_EOL . $item);
        }
        fclose($handle);
	}
	$items = open_file(FILE);
	if (isset($_POST['Todo'])) {
		$items[] = $_POST['Todo'];
		save_file($items);
	}
	if (isset($_GET['remove'])) {
		$keyRemoved = $_GET['remove'];
		unset($items[$keyRemoved]);
		$items = array_values($items);
		save_file($items);
	}
	if (count($_FILES) > 0 && $_FILES['file1']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = '/vagrant/sites/planner.dev/public/uploads/';
        $filename = basename($_FILES['file1']['name']);
        $saved_filename = $upload_dir . $filename;
        move_uploaded_file($_FILES['file1']['tmp_name'], $saved_filename);
    }
    if (isset($saved_filename)) {
    	echo "<p>You can download your file <a href='/uploads/{$filename}'>here</a>.</p>";
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
			<?php 
				foreach ($items as $key => $value) {
					echo "<li>$value<a href='?remove=$key'>Complete</a></li>";
				}
			?>
		</ul>
		<h2>Enter your to do list item</h2>
		<form class="addItem" method="post" action="todo_list.php">
			<label for="Todo"></label>
        		<input id="Todo" name="Todo" type="text" placeholder="Enter todo item">
			<button type="Submit">Add</button>
		</form>
		<form clas="uploads" method="POST" enctype="multipart/form-data" action="todo_list.php">
			<p>
				<label for="file1">File to upload </label>
				<input type="file" id="file1" name="file1">
			</p>
			<p>
				<input type="submit" value="Upload">
			</p>
		</form>
	</body>
</html>
