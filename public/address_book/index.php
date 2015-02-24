<?php

require 'inc/config.inc.php';
require 'inc/dbconnect.php';
require_once 'inc/person.class.php';
require_once 'inc/address.class.php';

$errors = array();

if (!empty($_SESSION['errorMessage'])) {
    $errorMessage = $_SESSION['errorMessage'];
    unset($_SESSION['errorMessage']);
}

if (!empty($_SESSION['infoMessage'])) {
    $infoMessage = $_SESSION['infoMessage'];
    unset($_SESSION['infoMessage']);
}

if (!empty($_POST['action'])) {
    switch ($_POST['action']) {
        case 'add_person':
            $person = new Person($dbc);

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

                $_POST = array();

                $infoMessage = "Added $person->first_name $person->last_name";
            } else {
                $errorMessage = 'Failed to save new person!';
            }

            break;
        case 'delete_person':
            try {
                $person = Person::find($_POST['person_id']);
                $person->delete();

                $infoMessage = "Deleted $person->first_name $person->last_name";
            } catch (OutOfBoundsException $e) {
                $errorMessage = "Could not find person for id &ldquo;{$_POST['person_id']}&rdquo;. Perhaps they've already been deleted.";
            }

            break;
        case 'delete_address':
            try {
                $person = Person::find($_POST['person_id']);

                $address = Address::find($_POST['address_id']);
                $address->unlinkPerson($_POST['person_id']);

                $infoMessage = "Removed $address->street $address->apt from $person->first_name $person->last_name";
            } catch (OutOfBoundsException $e) {
                $errorMessage = "Could not find the specified person or address. Perhaps they've already been deleted.";
            }

            break;
        default:
            $errorMessage = "Unknown action &ldquo;{$_POST['action']}&rdquo;";
            break;
    }
}

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;

$offset = ($page - 1) * DEFAULT_LIMIT;

$people = Person::globalData(DEFAULT_LIMIT, $offset);

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

    <style type="text/css" media="screen">
        body { margin-bottom: 20px; }

        .table > tbody > tr > td.name-cell {
            position: relative;
            padding-bottom: 2.5em;
        }

        .name-buttons {
            position: absolute;
            bottom: .6em;
        }
    </style>

    <script type="text/javascript" src="js/jquery.maskedinput.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $("#phone").mask('(999) 999-9999');

            $(".delete-person-btn").click(function() {
                var personId   = $(this).data('person-id');
                var personName = $("#person-" + personId).text();

                $("#delete-confirm-label").text('Are You Sure You Want to Delete: ' + personName + '?')
                $("#delete-confirm-body").html('Clicking &ldquo;Delete&rdquo; will remove this person as well as any addresses associated exclusively with them.');

                $("#delete-person-id").val(personId);
                $("#delete-action").val('delete_person');

                $("#delete-confirm-modal").modal();
            });

            $(".delete-address-btn").click(function() {
                var personId      = $(this).data('person-id');
                var addressId     = $(this).data('address-id');
                var personName    = $("#person-" + personId).text();
                var addressStreet = $("#address-" + addressId).text();

                $("#delete-confirm-label").text('Are You Sure You Want to Delete: ' + addressStreet + '?')
                $("#delete-confirm-body").html('Clicking &ldquo;Delete&rdquo; will remove this address from ' + personName + '. If it is not associated with any other people, the address will also be deleted permanently.');

                $("#delete-person-id").val(personId);
                $("#delete-address-id").val(addressId);
                $("#delete-action").val('delete_address');
                
                $("#delete-confirm-modal").modal();
            });

            $("#confirm-delete-btn").click(function() {
                $("#delete-form").submit();
            });
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

                <table class="table table-bordered">
                    <tr><th>Person</th><th>Addresses</th></tr>
                    <? foreach ($people as $person): ?>
                        <tr>
                            <td rowspan="<?= count($person->addresses) ?>" class="name-cell">
                                <h5 class="text-primary" id="person-<?= $person->id; ?>"><?= $person->first_name ?> <?= $person->last_name; ?></h5>
                                <div class="text-muted"><small><?= $person->phone; ?></small></div>
                                <div class="btn-group btn-group-xs name-buttons">
                                    <a href="edit_person.php?id=<?= $person->id; ?>" class="btn btn-primary">Edit</a>
                                    <a href="add_address.php?id=<?= $person->id; ?>" class="btn btn-success">Add Address</a>
                                    <button type="button" class="btn btn-danger delete-person-btn" data-person-id="<?= $person->id; ?>">Delete</button>
                                </div>
                            </td>
                            <? $firstAddress = true; ?>

                            <? foreach ($person->addresses as $address): ?>
                                <? if (!$firstAddress): ?></tr><tr><? endif ?>

                                <td>
                                    <p>
                                        <div id="address-<?= $address->id; ?>"><?= $address->street; ?> <?= $address->apt ?></div>
                                        <?= $address->city; ?>, <?= $address->state ?>
                                        <?= $address->zip; ?><? if (!empty($address->plus_four)): ?>+<?= $address->plus_four; ?><? endif; ?>
                                    </p>
                                    <div class="btn-group btn-group-xs">
                                        <a href="edit_address.php?id=<?= $address->id; ?>" class="btn btn-primary edit-person-btn">Edit</a>
                                        <button type="button" class="btn btn-danger delete-address-btn" data-address-id="<?= $address->id; ?>" data-person-id="<?= $person->id; ?>">Delete</button>
                                    </div>
                                </td>
                                <? $firstAddress = false; ?>
                            <? endforeach ?>
                        </tr>
                    <? endforeach ?>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <h2>New Person</h2>

                <form action="index.php" method="post" class="form-horizontal">
                    <input type="hidden" name="action" value="add_person" />
                    <?php include 'templates/person.form.php'; ?>

                    <div class="form-group">
                        <div class="col-sm-10 col-sm-offset-2">
                            <button type="submit" class="btn btn-primary">Add Person</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="delete-confirm-modal" tabindex="-1" role="dialog" aria-labelledby="delete-confirm-label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="delete-confirm-label"></h4>
                </div>

                <div class="modal-body"><p id="delete-confirm-body"></p></div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirm-delete-btn">Delete</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <form action="/address_book/index.php" method="post" id="delete-form">
        <input type="hidden" name="action" id="delete-action" />
        <input type="hidden" name="person_id" id="delete-person-id" />
        <input type="hidden" name="address_id" id="delete-address-id" />
    </form>
</body>
</html>
