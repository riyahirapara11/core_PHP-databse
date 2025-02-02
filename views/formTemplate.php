<div class="form_group">
    <label for="profilePhoto">Profile Photo :</label>
    <input type="file" id="profilePhoto" name="profilePhoto">
    <input type="hidden" name="existingFilePath" value="<?= $filePath ?? '' ?>">
</div>

<div class="form_group">
    <label for="firstName">First name:</label>
    <input type="text" id="firstName" name="firstName" value="<?= $firstName ?? '' ?>">
    <span class="error"><?= $errors['firstName'] ?? ''; ?></span>
</div>

<div class="form_group">
    <label for="lastName">Last name:</label>
    <input type="text" id="lastName" name="lastName" value="<?= $lastName ?? '' ?>">
    <span class="error"><?= $errors['lastName'] ?? ''; ?></span>
</div>

<div class="form_group">
    <label for="email">Email :</label>
    <input type="text" id="email" name="email" value="<?= $email ?? '' ?>">
    <span class="error"><?= $errors['email'] ?? ''; ?></span>
</div>

<div class="form_group">
    <label for="phone">Phone No. :</label>
    <input type="text" id="phone" name="phone" value="<?= $phoneNo ?? '' ?>">
    <span class="error"><?= $errors['phone'] ?? ''; ?></span>
</div>

<div class="form_group">
    <label for="address">Address :</label>
    <textarea name="address" id="address"><?= $address ?? '' ?></textarea>
    <span class="error"><?= $errors['address'] ?? ''; ?></span>
</div>

<div class="form_group">
    <label for="country">Country :</label>
    <select name="country" id="country">
        <option value="">Select Country</option>
        <?php // Dynamically populate country options ?>
        <!-- Populate selected country -->
        <option value="<?= $country_id ?? '' ?>" selected><?= $country_name ?? 'Select Country' ?></option>
    </select>
    <span class="error"><?= $errors['country'] ?? ''; ?></span>
</div>

<div class="form_group">
    <label for="state">State :</label>
    <select name="state" id="state">
        <option value="">Select State</option>
        <?php // Dynamically populate state options ?>
        <option value="<?= $state_id ?? '' ?>" selected><?= $state_name ?? 'Select State' ?></option>
    </select>
    <span class="error"><?= $errors['state'] ?? ''; ?></span>
</div>

<div class="form_group">
    <label for="pincode">Pincode :</label>
    <input type="text" name="pincode" id="pincode" value="<?= $pincode ?? '' ?>">
    <span class="error"><?= $errors['pincode'] ?? ''; ?></span>
</div>

<div class="form_group">
    <label for="password">Password :</label>
    <input type="password" id="password" name="password">
    <!-- Only show error if validation fails -->
    <span class="error"><?= $errors['password'] ?? ''; ?></span>
</div>

<div class="form_group">
    <label for="confirmPass">Confirm Password :</label>
    <input type="password" id="confirmPass" name="confirmPass">
    <!-- Only show error if validation fails -->
    <span class="error"><?= $errors['confirmPass'] ?? ''; ?></span>
</div>

<input type="hidden" name="id" value="<?= $id ?? '' ?>">

<div class="form_group">
    <button type="submit"><?= $buttonLabel ?? 'Update User'; ?></button>
</div>
