<div class="form-group">
    <label for="first_name" class="control-label col-sm-2">Name</label>
    <div class="col-sm-5 <?= isset($errors['first_name']) ? 'has-error' : '' ?>">
        <input type="text" name="first_name" id="first_name" class="form-control" placeholder="First" maxlength="31" required="required" value="<?= isset($_POST['first_name']) ? $_POST['first_name'] : '' ?>" />

        <?php if (isset($errors['first_name'])): ?>
            <p class="help-block"><?= $errors['first_name']; ?></p>
        <?php endif; ?>
    </div>
    <div class="col-sm-5 <?= isset($errors['last_name']) ? 'has-error' : '' ?>">
        <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Last" maxlength="63" required="required" value="<?= isset($_POST['last_name']) ? $_POST['last_name'] : '' ?>" />
        <?php if (isset($errors['last_name'])): ?>
            <p class="help-block"><?= $errors['last_name']; ?></p>
        <?php endif; ?>
    </div>
</div>

<div class="form-group <?= isset($errors['phone']) ? 'has-error' : '' ?>">
    <label for="phone" class="control-label col-sm-2">Phone</label>
    <div class="col-sm-10">
        <input type="tel" name="phone" id="phone" class="form-control" placeholder="(999) 999-9999" pattern="\(?\d{3}[\).\- ]{0,2}\d{3}[\-. ]?\d{4}" value="<?= isset($_POST['phone']) ? $_POST['phone'] : '' ?>" />
        <?php if (isset($errors['phone'])): ?>
            <p class="help-block"><?= $errors['phone']; ?></p>
        <?php endif; ?>
    </div>
</div>
