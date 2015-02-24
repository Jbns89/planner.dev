<div class="form-group">
    <label for="street" class="control-label col-sm-2">New Address</label>
    <div class="col-sm-8 <?= isset($errors['street']) ? 'has-error' : '' ?>">
        <input type="text" name="street" id="street" class="form-control" placeholder="Street" maxlength="127" value="<?= isset($_POST['street']) ? $_POST['street'] : '' ?>" />

        <?php if (isset($errors['street'])): ?>
            <p class="help-block"><?= $errors['street']; ?></p>
        <?php endif; ?>
    </div>
    <div class="col-sm-2 <?= isset($errors['apt']) ? 'has-error' : '' ?>">
        <input type="text" name="apt" id="apt" class="form-control" placeholder="Apt" maxlength="15" value="<?= isset($_POST['apt']) ? $_POST['apt'] : '' ?>" />

        <?php if (isset($errors['apt'])): ?>
            <p class="help-block"><?= $errors['apt']; ?></p>
        <?php endif; ?>
    </div>
</div>

<div class="form-group">
    <div class="col-sm-4 col-sm-offset-2 <?= isset($errors['city']) ? 'has-error' : '' ?>">
        <input type="text" name="city" id="city" class="form-control" placeholder="City" maxlength="63" value="<?= isset($_POST['city']) ? $_POST['city'] : '' ?>" />

        <?php if (isset($errors['city'])): ?>
            <p class="help-block"><?= $errors['city']; ?></p>
        <?php endif; ?>
    </div>
    <div class="col-sm-3 <?= isset($errors['state']) ? 'has-error' : '' ?>">
        <select name="state" id="state" class="form-control">
            <? foreach ($states as $stateAbv => $state): ?>
                <option value="<?= $stateAbv ?>" <?= isset($_POST['state']) && $_POST['state'] == $stateAbv ? 'selected="selected"' : ''?>><?= $state ?></option>
            <? endforeach ?>
        </select>

        <?php if (isset($errors['state'])): ?>
            <p class="help-block"><?= $errors['state']; ?></p>
        <?php endif; ?>
    </div>
    <div class="col-sm-3">
        <div class="row">
            <div class="col-xs-6 <?= isset($errors['zip']) ? 'has-error' : '' ?>">
                <input type="text" name="zip" id="zip" class="form-control" placeholder="Zip" maxlength="5" pattern="\d{5}" value="<?= isset($_POST['zip']) ? $_POST['zip'] : '' ?>" />
            </div>
            <div class="col-xs-6 <?= isset($errors['plus_four']) ? 'has-error' : '' ?>">
                <input type="text" name="plus_four" id="plus_four" class="form-control" placeholder="+4" maxlength="4" pattern="\d{4}" value="<?= isset($_POST['plus_four']) ? $_POST['plus_four'] : '' ?>" />
            </div>

        </div>
        <div class="row">
            <div class="col-xs-12 <?= isset($errors['zip']) || isset($errors['plus_four']) ? 'has-error' : '' ?>">
                <?php if (isset($errors['zip'])): ?>
                    <p class="help-block"><?= $errors['zip']; ?></p>
                <?php endif; ?>
                <?php if (isset($errors['plus_four'])): ?>
                    <p class="help-block"><?= $errors['plus_four']; ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
