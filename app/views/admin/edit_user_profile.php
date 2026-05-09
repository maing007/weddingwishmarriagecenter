
<?php
// $user, $error, $success available
$profileImgUrl = '';
if (!empty($user['avatar'])) {
    $profileImgUrl = public_url_for_path((string) $user['avatar']);
}
if ($profileImgUrl === '') {
    $g = strtolower(trim((string) ($user['gender'] ?? '')));
    $profileImgUrl = ($g === 'female' || strncmp($g, 'female', 6) === 0)
        ? public_url_for_path('assets/images/female.png')
        : public_url_for_path('assets/images/male.png');
}
    $currentReligion = $user['religion'] ?? ''; // jo DB mein save hai
    $phone = $user['phone'] ?? '';
$countryCode = $user['country_code'] ?? '';
require __DIR__.'/partials/header.php';
require __DIR__.'/partials/sidebar.php';
?>
<script>
    const savedNumber = "<?= $phone ?>";
const savedCountry = "<?= $countryCode ?>";

if (savedCountry !== "") {
    iti.setCountry(savedCountry.toLowerCase());
}

if (savedNumber !== "") {
    iti.setNumber(savedNumber);
}
document.querySelector("#login_form")?.addEventListener("submit", function () {
    document.querySelector("#mobile_number").value = iti.getNumber();
    document.querySelector("#country_code").value = iti.getSelectedCountryData().iso2.toUpperCase();
});

</script>
<div class="container mt-4">
  <div class="row">
    <div class="ml-5 col-md-8 col-sm-12 col-xs-12"  style="">
      <h2>Edit Profile</h2>

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
          <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($success)): ?>
        <div class="alert alert-success">
          <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
        </div>
      <?php endif; ?>

      <div class="panel panel-default">
        <div class="panel-body">

          <div class="text-center mb-3">
            <img src="<?= htmlspecialchars($profileImgUrl, ENT_QUOTES, 'UTF-8') ?>"
                 alt="Profile"
                 class="img-circle"
                 style="width:120px;height:120px;object-fit:cover;border:3px solid #ddd;">
          </div>

          <form
            action="<?= BASE_URL ?>/dashboard/profile"
            method="post"
            enctype="multipart/form-data"
          >
       <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token_user_'.$userId] ?>">

            <div class="form-group">
              <label for="first_name">First name</label>
              <input type="text"
                     class="form-control"
                     id="first_name"
                     name="first_name"
                     value="<?= htmlspecialchars($user['first_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                     required>
            </div>

            <div class="form-group">
              <label for="last_name">Last name</label>
              <input type="text"
                     class="form-control"
                     id="last_name"
                     name="last_name"
                     value="<?= htmlspecialchars($user['last_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                     required>
            </div>
  <div class="form-group">
    <label for="phone_input">Number</label>

    <!-- Visible phone input for intlTelInput -->
    <input
        type="tel"
        name="phone"
        id="phone_input"
        class="cstm-form"
        minlength="7"
        maxlength="13"
        required
        value="<?= htmlspecialchars($phone, ENT_QUOTES, 'UTF-8') ?>"
    />

    <!-- Hidden fields populated before submit -->
    <input type="hidden" name="mobile_number" id="mobile_number"
           value="<?= htmlspecialchars($phone, ENT_QUOTES, 'UTF-8') ?>">

    <input type="hidden" name="country_code" id="country_code"
           value="<?= htmlspecialchars($countryCode, ENT_QUOTES, 'UTF-8') ?>">
</div>
                <div class="form-group">
            <div class="">
  <select
    class="cstm-form select2 select-cust"
    required
    name="religion"
    id="religion"
    onchange="dropdownChange('religion','sect','sect_list')"
    style="width: 100%"
  >
    <option value="">Select Religion</option>

    <option value="Muslim"
      <?= $currentReligion === 'Muslim' ? 'selected' : '' ?>>
      Muslim
    </option>

    <option value="Sikh"
      <?= $currentReligion === 'Sikh' ? 'selected' : '' ?>>
      Sikh
    </option>

    <option value="Hindu"
      <?= $currentReligion === 'Hindu' ? 'selected' : '' ?>>
      Hindu
    </option>

    <option value="Christian"
      <?= $currentReligion === 'Christian' ? 'selected' : '' ?>>
      Christian
    </option>

    <option value="Qadiyani"
      <?= $currentReligion === 'Qadiyani' ? 'selected' : '' ?>>
      Qadiyani
    </option>
  </select>
</div>
            </div>

            <div class="form-group">
              <label for="bio">Bio</label>
              <textarea class="form-control"
                        id="bio"
                        name="bio"
                        rows="3"><?= htmlspecialchars($user['bio'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <div class="form-group">
              <label for="profile_image">Profile Image</label>
              <input type="file"
                     class="form-control"
                     id="profile_image"
                     name="avatar"
                     accept="image/*">
              <small class="text-muted">Max 2MB, JPG/PNG/GIF/WEBP</small>
            </div>

            <button type="submit" class="btn btn-primary">
              Save Profile
            </button>
          </form>

        </div>
      </div>

    </div>
  </div>
</div>
