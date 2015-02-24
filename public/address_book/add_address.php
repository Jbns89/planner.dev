<?php

require 'inc/config.inc.php';
require 'inc/dbconnect.php';
require_once 'inc/person.class.php';
require_once 'inc/address.class.php';

$errors = array();

if (!empty($_POST)) {
    $person = Person::find($_POST['person_id']);

    if ($_POST['existing_address'] == -1) {
        $address = new Address($dbc);

        try {
            $address->street = $_POST['street'];
        } catch (Exception $e) {
            $errors['street'] = $e->getMessage();
        }
        try {
            $address->apt = $_POST['apt'];
        } catch (Exception $e) {
            $errors['apt'] = $e->getMessage();
        }
        try {
            $address->city = $_POST['city'];
        } catch (Exception $e) {
            $errors['city'] = $e->getMessage();
        }
        try {
            $address->state = $_POST['state'];
        } catch (Exception $e) {
            $errors['state'] = $e->getMessage();
        }
        try {
            $address->zip = $_POST['zip'];
        } catch (Exception $e) {
            $errors['zip'] = $e->getMessage();
        }
        try {
            $address->plus_four = $_POST['plus_four'];
        } catch (Exception $e) {
            $errors['plus_four'] = $e->getMessage();
        }

        if (empty($errors)) {
            $address->save();
        }
    } else {
        $address = Address::find($_POST['existing_address']);
    }

    if (empty($errors)) {
        $address->linkPerson($person->id);

        $_SESSION['infoMessage'] = "Added $address->street $address->apt to $person->first_name $person->last_name";
        header('Location: index.php');
        exit();
    } else {
        $errorMessage = "Failed to link address with $person->first_name $person->last_name";
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
}

$existingAddresses = Address::all();

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
            <h2>Add an Address to <?= $person->first_name; ?> <?= $person->last_name; ?></h2>

            <form action="<?= basename(__FILE__) ?>" method="post" accept-charset="utf-8" class="form-horizontal">
                <input type="hidden" name="person_id" value="<?= $person->id ?>" />

                <div class="form-group">
                    <label for="existing_address" class="control-label col-sm-2">Existing Address</label>
                    <div class="col-sm-10">
                        <select name="existing_address" id="existing_address" class="form-control">
                            <option value="-1">Assign an Existing Address</option>
                            <? foreach ($existingAddresses as $address): ?>
                                <option value="<?= $address->id ?>" <?= $address->isLinked($person->id) ? 'disabled="disabled"' : ''; ?>>
                                    <?= $address->street ?> <?= $address->apt ?> - <?= $address->city ?>, <?= $address->state ?> <?= $address->zip ?>
                                </option>
                            <? endforeach ?>
                        </select>
                    </div>
                </div>

                <hr />

                <?php include 'templates/address.form.php'; ?>

                <div class="form-group">
                    <div class="col-sm-10 col-sm-offset-2">
                        <a href="index.php" class="btn btn-default">Nevermind</a>
                        <button type="submit" class="btn btn-primary">Add Address</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
