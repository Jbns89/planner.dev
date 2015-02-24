<?php

require 'inc/config.inc.php';
require 'inc/dbconnect.php';
require_once 'inc/person.class.php';

$errors = array();

if (!empty($_POST)) {
    $person = Person::find($_POST['id']);

    try {
        $person->first_name = $_POST['first_name'];
    } catch (Exception $e) {
        $errors['first_name'] = $e->getMessage();
    }

    try {
        $person->last_name = $_POST['last_name'];
    } catch (Exception $e) {
        $errors['last_name'] = $e->getMessage();
    }

    try {
        $person->phone = $_POST['phone'];
    } catch (Exception $e) {
        $errors['phone'] = $e->getMessage();
    }

    if (empty($errors)) {
        $person->save();

        $_SESSION['infoMessage'] = "Saved $person->first_name $person->last_name";

        header('Location: index.php');
        exit();
    } else {
        $errorMessage = "Failed to save $person->first_name $person->last_name!";
    }
} else {
    if (!isset($_GET['id'])) {
        $_SESSION['errorMessage'] = 'Cannot edit a person without their ID!';

        header('Location: index.php');
        exit();
    }

    try {
        $person = Person::find($_GET['id']);
    } catch (OutOfBoundsException $e) {
        $_SESSION['errorMessage'] = "Could not find a person for ID &ldquo;{$_GET['id']}&rdquo;";
        header('Location: index.php');
        exit();
    }

    $_POST['first_name'] = $person->first_name;
    $_POST['last_name']  = $person->last_name;
    $_POST['phone']      = $person->phone;
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>Address Book</title>

    <!-- Bootstrap -->
    <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link href="/bootstrap/css/bootstrap-theme.min.css" rel="stylesheet" />


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="js/jquery-2.1.1.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="/bootstrap/js/bootstrap.min.js"></script>
    
    <script type="text/javascript" src="js/jquery.maskedinput.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $("#phone").mask('(999) 999-9999');
        });
    </script>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <h1 class="page-header">Address Book!</h1>

                <? if (isset($infoMessage)): ?>
                    <div class="alert alert-info"><?= $infoMessage; ?></div>
                <? endif ?>

                <? if (isset($errorMessage)): ?>
                    <div class="alert alert-danger"><?= $errorMessage; ?></div>
                <? endif ?>
            </div>
        </div>
        <div class="col-md-8 col-md-offset-2">
            <h2>Edit <?= $person->first_name; ?> <?= $person->last_name; ?></h2>

            <form action="<?= basename(__FILE__) ?>" method="post" accept-charset="utf-8" class="form-horizontal">
                <input type="hidden" name="id" value="<?= $person->id ?>" />

                <?php include 'templates/person.form.php' ?>

                <div class="form-group">
                    <div class="col-sm-10 col-sm-offset-2">
                        <a href="index.php" class="btn btn-default">Nevermind</a>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
