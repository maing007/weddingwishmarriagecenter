<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css">

<style>
.mega-box-new{
    background:#fff;
    border-radius:12px;
    padding:30px;
    box-shadow:0 4px 15px rgba(0,0,0,.05);
    border:1px solid #eee;
    margin-bottom:25px;
}

.new-width{
    width:100%;
    padding:0;
    margin:0;
}

.contact-tab-nav2{
    display:flex;
    gap:40px;
    padding-left:0;
    margin-bottom:0;
}

.contact-tab-nav2 li{
    list-style:none;
}

.contact-tab-nav2 li a{
    font-size:18px;
    font-weight:600;
    color:#333;
    text-decoration:none;
}

.contact-tab-nav2 li.active a{
    color:#e6005c;
    border-bottom:3px solid #e6005c;
    padding-bottom:10px;
}

.search-hr{
    margin:20px 0 30px;
    border-top:1px solid #eee;
}

.form-control{
    height:44px;
    border-radius:8px;
    border:1px solid #ddd;
}

.chosen-container{
    width:100% !important;
}

.chosen-container-multi .chosen-choices{
    min-height:44px !important;
    border-radius:8px !important;
    border:1px solid #ddd !important;
    padding:6px 10px !important;
}

.add-w-btn{
    background:#e6005c;
    border:none;
    height:44px;
    border-radius:8px;
    color:#fff;
    width:100%;
}

.ad-name{
    line-height:44px;
    font-weight:500;
}

.photo-check input[type="checkbox"]{
    width:18px;
    height:18px;
    visibility: visible;
    margin-right:10px;
    accent-color:#e6005c;
}
.search-accordion{margin-top:8px;}
.search-accordion .panel-heading{cursor:pointer;padding:12px 15px;}
.search-accordion .panel-title{font-size:16px;margin:0;}
.search-accordion .panel-title a{color:#333;text-decoration:none;display:block;}
.search-accordion .panel-body{padding-top:8px;}
</style>

<div class="container mt-4 mb-5">
<div class="row">

<div class="col-md-8">
<div class="mega-box-new">

<div class="new-width">
<ul class="nav nav-tabs contact-tab-nav2">
<li class="active">
<a href="#"><i class="fas fa-search"></i> Quick Search</a>
</li>
<li>
<a href="#"><i class="fas fa-search-plus"></i> Advance Search</a>
</li>
</ul>
</div>

<hr class="search-hr">

<?php
$looking_for_opts = ['Does not matter', 'Unmarried', 'Widow/Widower', 'Divorcee', 'Separated', 'Married'];
$employee_in_opts = ['Does not matter', 'Private', 'Government', 'Business', 'Defence', 'Not Employed in', 'Others'];
$diet_opts = ['Does not matter', 'Occasionally Non-Veg', 'Veg', 'Eggetarian', 'Non-Veg'];
$drink_opts = ['Does not matter', 'No', 'Yes', 'Occasionally'];
$smoke_opts = ['Does not matter', 'No', 'Yes', 'Occasionally'];
$complexion_opts = ['Does not matter', 'Wheatish', 'Very Fair', 'Fair', 'Wheatish Brown', 'Dark'];
$bodytype_opts = ['Does not matter', 'Slim', 'Average', 'Athletic', 'Heavy'];
$house_type_fixed = ['Does not matter', 'Rented', 'Owned', 'On Lease'];
?>

<form action="<?= BASE_URL ?>/search/search" method="POST" enctype="multipart/form-data">

<input type="hidden" name="search_mode" value="advanced">

<!-- Gender -->
<div class="row mt-3">
<div class="col-md-4"><p class="ad-name">Gender:</p></div>
<div class="col-md-4">
<label><input type="radio" name="gender" value="Male" checked> Male</label>
</div>
<div class="col-md-4">
<label><input type="radio" name="gender" value="Female"> Female</label>
</div>
</div>

<!-- Age -->
<div class="row mt-4">
<div class="col-md-4"><p class="ad-name">Age:</p></div>
<div class="col-md-3">
<select class="form-control" name="from_age" id="from_age">
<?php for ($i = 18; $i <= 65; $i++): ?>
<option value="<?= $i ?>" <?= $i === 18 ? 'selected' : '' ?>><?= $i ?> Year</option>
<?php endfor; ?>
</select>
</div>

<div class="col-md-2 text-center hidden-sm hidden-xs">
<label style="line-height:44px;">To</label>
</div>

<div class="col-md-3">
<select class="form-control" name="to_age" id="to_age">
<?php for ($i = 18; $i <= 65; $i++): ?>
<option value="<?= $i ?>" <?= $i === 30 ? 'selected' : '' ?>><?= $i ?> Year</option>
<?php endfor; ?>
</select>
</div>
</div>

<!-- Height -->
<div class="row mt-4">
<div class="col-md-4"><p class="ad-name">Height:</p></div>

<div class="col-md-3">
<select class="form-control" name="from_height">
<option value="">From</option>
<option value="48">Below 4ft</option>
<option value="49">4ft 1in</option>
<option value="50">4ft 2in</option>
<option value="51">4ft 3in</option>
<option value="52">4ft 4in</option>
<option value="53">4ft 5in</option>
<option value="54">4ft 6in</option>
<option value="55">4ft 7in</option>
<option value="56">4ft 8in</option>
<option value="57">4ft 9in</option>
<option value="58">4ft 10in</option>
<option value="59">4ft 11in</option>
<option value="60">5ft</option>
<option value="61">5ft 1in</option>
<option value="62">5ft 2in</option>
<option value="63">5ft 3in</option>
<option value="64">5ft 4in</option>
<option value="65">5ft 5in</option>
<option value="66">5ft 6in</option>
<option value="67">5ft 7in</option>
<option value="68">5ft 8in</option>
<option value="69">5ft 9in</option>
<option value="70">5ft 10in</option>
<option value="71">5ft 11in</option>
<option value="72">6ft</option>
<option value="73">6ft 1in</option>
<option value="74">6ft 2in</option>
<option value="75">6ft 3in</option>
<option value="76">6ft 4in</option>
<option value="77">6ft 5in</option>
<option value="78">6ft 6in</option>
<option value="79">6ft 7in</option>
<option value="80">6ft 8in</option>
<option value="81">6ft 9in</option>
<option value="82">6ft 10in</option>
<option value="83">6ft 11in</option>
<option value="84">7ft</option>
<option value="85">Above 7ft</option>
</select>
</div>

<div class="col-md-2 text-center hidden-sm hidden-xs">
<label style="line-height:44px;">To</label>
</div>

<div class="col-md-3">
<select class="form-control" name="to_height">
<option value="">To</option>
<option value="48">Below 4ft</option>
<option value="49">4ft 1in</option>
<option value="50">4ft 2in</option>
<option value="51">4ft 3in</option>
<option value="52">4ft 4in</option>
<option value="53">4ft 5in</option>
<option value="54">4ft 6in</option>
<option value="55">4ft 7in</option>
<option value="56">4ft 8in</option>
<option value="57">4ft 9in</option>
<option value="58">4ft 10in</option>
<option value="59">4ft 11in</option>
<option value="60">5ft</option>
<option value="61">5ft 1in</option>
<option value="62">5ft 2in</option>
<option value="63">5ft 3in</option>
<option value="64">5ft 4in</option>
<option value="65">5ft 5in</option>
<option value="66">5ft 6in</option>
<option value="67">5ft 7in</option>
<option value="68">5ft 8in</option>
<option value="69">5ft 9in</option>
<option value="70">5ft 10in</option>
<option value="71">5ft 11in</option>
<option value="72">6ft</option>
<option value="73">6ft 1in</option>
<option value="74">6ft 2in</option>
<option value="75">6ft 3in</option>
<option value="76">6ft 4in</option>
<option value="77">6ft 5in</option>
<option value="78">6ft 6in</option>
<option value="79">6ft 7in</option>
<option value="80">6ft 8in</option>
<option value="81">6ft 9in</option>
<option value="82">6ft 10in</option>
<option value="83">6ft 11in</option>
<option value="84">7ft</option>
<option value="85">Above 7ft</option>
</select>
</div>
</div>

<!-- Marital status -->
<div class="row mt-4">
<div class="col-md-4"><p class="ad-name">Marital status:</p></div>
<div class="col-md-8">
<select data-placeholder="Select Marital Status" name="looking_for[]" class="chosen-select form-control" multiple tabindex="4">
<?php foreach ($looking_for_opts as $opt): ?>
<option value="<?= htmlspecialchars($opt, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($opt, ENT_QUOTES, 'UTF-8') ?></option>
<?php endforeach; ?>
</select>
</div>
</div>

<!-- Religion -->
<div class="row mt-4">
<div class="col-md-4"><p class="ad-name">Religion:</p></div>
<div class="col-md-8">
<select data-placeholder="Select Religion" name="religion[]" id="religion" class="chosen-select form-control" multiple tabindex="4">
<?php foreach ($religions ?? [] as $r): ?>
<option value="<?= htmlspecialchars((string) $r, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $r, ENT_QUOTES, 'UTF-8') ?></option>
<?php endforeach; ?>
</select>
</div>
</div>

<!-- Sect -->
<div class="row mt-4">
<div class="col-md-4"><p class="ad-name">Sect:</p></div>
<div class="col-md-8">
<select data-placeholder="Select Sect" name="sect[]" id="sect" class="chosen-select form-control" multiple tabindex="4">
<?php foreach ($sects ?? [] as $s): ?>
<option value="<?= htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8') ?></option>
<?php endforeach; ?>
</select>
</div>
</div>

<!-- Mother Tongue -->
<div class="row mt-4">
<div class="col-md-4"><p class="ad-name">Mother Tongue:</p></div>
<div class="col-md-8">
<select data-placeholder="Select Mother Tongue" name="mother_tongue[]" class="chosen-select form-control" multiple tabindex="4">
<?php foreach ($mother_tongues ?? [] as $lang): ?>
<option value="<?= htmlspecialchars((string) $lang, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $lang, ENT_QUOTES, 'UTF-8') ?></option>
<?php endforeach; ?>
</select>
</div>
</div>

<!-- Photo -->
<div class="row mt-4">
<div class="col-md-4"><p class="ad-name">Photo Setting:</p></div>
<div class="col-md-8">
<label class="photo-check">
<input type="checkbox" name="photo_search" value="photo_search"> With Photo
</label>
</div>
</div>

<!-- Accordions -->
<div class="row mt-3">
<div class="col-md-12">
<div class="panel-group search-accordion" id="search-accordion">

<div class="panel panel-default">
<div class="panel-heading">
<h4 class="panel-title">
<a data-toggle="collapse" data-parent="#search-accordion" href="#adv_location"> Location Details</a>
</h4>
</div>
<div id="adv_location" class="panel-collapse collapse">
<div class="panel-body">

<div class="row mt-4">
<div class="col-md-4"><p class="ad-name">Country:</p></div>
<div class="col-md-8">
<select data-placeholder="Select Country" name="country[]" id="country" class="chosen-select form-control" multiple tabindex="4">
<?php foreach ($countries ?? [] as $country): ?>
<option value="<?= htmlspecialchars((string) $country, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $country, ENT_QUOTES, 'UTF-8') ?></option>
<?php endforeach; ?>
</select>
</div>
</div>

<div class="row mt-4">
<div class="col-md-4"><p class="ad-name">State:</p></div>
<div class="col-md-8">
<select data-placeholder="Select State" name="state[]" id="state" class="chosen-select form-control" multiple tabindex="4">
<?php foreach ($states ?? [] as $st): ?>
<option value="<?= htmlspecialchars((string) $st, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $st, ENT_QUOTES, 'UTF-8') ?></option>
<?php endforeach; ?>
</select>
</div>
</div>

<div class="row mt-4">
<div class="col-md-4"><p class="ad-name">City:</p></div>
<div class="col-md-8">
<select data-placeholder="Select City" name="city[]" id="city" class="chosen-select form-control" multiple tabindex="4">
<?php foreach ($cities ?? [] as $ct): ?>
<option value="<?= htmlspecialchars((string) $ct, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $ct, ENT_QUOTES, 'UTF-8') ?></option>
<?php endforeach; ?>
</select>
</div>
</div>

<div class="row mt-4">
<div class="col-md-4"><p class="ad-name">House Area:</p></div>
<div class="col-md-8">
<select data-placeholder="Select House Area" name="house_area[]" id="house_area" class="chosen-select form-control" multiple tabindex="4">
<?php foreach ($areas ?? [] as $ar): ?>
<option value="<?= htmlspecialchars((string) $ar, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $ar, ENT_QUOTES, 'UTF-8') ?></option>
<?php endforeach; ?>
</select>
</div>
</div>

<div class="row mt-4">
<div class="col-md-4"><p class="ad-name">House Type:</p></div>
<div class="col-md-8">
<select class="form-control" name="house_type" id="house_type">
<option value="">Select House Type</option>
<?php foreach ($house_type_fixed as $ht): ?>
<option value="<?= htmlspecialchars($ht, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($ht, ENT_QUOTES, 'UTF-8') ?></option>
<?php endforeach; ?>
<?php foreach ($house_types_list ?? [] as $ht): ?>
<?php if ($ht === '' || in_array($ht, $house_type_fixed, true)) { continue; } ?>
<option value="<?= htmlspecialchars((string) $ht, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $ht, ENT_QUOTES, 'UTF-8') ?></option>
<?php endforeach; ?>
</select>
</div>
</div>

<div class="row mt-4">
<div class="col-md-4"><p class="ad-name">House Size Range (Marla):</p></div>
<div class="col-md-3">
<input type="text" class="form-control" name="house_size_from" placeholder="From">
</div>
<div class="col-md-2 text-center hidden-sm hidden-xs">
<label style="line-height:44px;">To</label>
</div>
<div class="col-md-3">
<input type="text" class="form-control" name="house_size_to" placeholder="To">
</div>
</div>

</div>
</div>
</div>

<div class="panel panel-default mt-4">
<div class="panel-heading">
<h4 class="panel-title">
<a data-toggle="collapse" data-parent="#search-accordion" href="#adv_education"> Education / Occupation / Annual Income Details</a>
</h4>
</div>
<div id="adv_education" class="panel-collapse collapse">
<div class="panel-body">

<div class="row mt-4">
<div class="col-md-4"><p class="ad-name">Education:</p></div>
<div class="col-md-8">
<select data-placeholder="Select Education" name="education[]" class="chosen-select form-control" multiple tabindex="4">
<?php foreach ($education ?? [] as $edu): ?>
<option value="<?= htmlspecialchars((string) $edu, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $edu, ENT_QUOTES, 'UTF-8') ?></option>
<?php endforeach; ?>
</select>
</div>
</div>

<div class="row mt-4">
<div class="col-md-4"><p class="ad-name">Occupation:</p></div>
<div class="col-md-8">
<select data-placeholder="Select Occupation" name="occupation[]" class="chosen-select form-control" multiple tabindex="4">
<?php foreach ($occupations ?? [] as $occ): ?>
<option value="<?= htmlspecialchars((string) $occ, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $occ, ENT_QUOTES, 'UTF-8') ?></option>
<?php endforeach; ?>
</select>
</div>
</div>

<div class="row mt-4">
<div class="col-md-4"><p class="ad-name">Employee In:</p></div>
<div class="col-md-8">
<select data-placeholder="Select Employee In" name="employee_in[]" class="chosen-select form-control" multiple tabindex="4">
<?php foreach ($employee_in_opts as $opt): ?>
<option value="<?= htmlspecialchars($opt, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($opt, ENT_QUOTES, 'UTF-8') ?></option>
<?php endforeach; ?>
</select>
</div>
</div>

<div class="row mt-4">
<div class="col-md-4"><p class="ad-name">Annual Income:</p></div>
<div class="col-md-8">
<select data-placeholder="Select Annual Income" name="income[]" class="chosen-select form-control" multiple tabindex="4">
<option value="Does not matter">Does not matter</option>
<option value="Less than PKR 10,000">Less than PKR 10,000</option>
<option value="PKR 10,000 - 50,000">PKR 10,000 - 50,000</option>
<option value="PKR 50,000 - 1,00,000">PKR 50,000 - 1,00,000</option>
<option value="PKR 1,00,000 - 2,00,000">PKR 1,00,000 - 2,00,000</option>
<option value="PKR 2,00,000 - 5,00,000">PKR 2,00,000 - 5,00,000</option>
<option value="PKR 5,00,000 - 10,00,000">PKR 5,00,000 - 10,00,000</option>
<option value="PKR 10,00,000 - 50,00,000">PKR 10,00,000 - 50,00,000</option>
<option value="PKR 50,00,000 - 1,00,00,000">PKR 50,00,000 - 1,00,00,000</option>
<option value="Above Rs 1,00,00,000">Above PKR 1,00,00,000</option>
<?php foreach ($annual_incomes ?? [] as $inc): ?>
<?php if ($inc === '') { continue; } ?>
<option value="<?= htmlspecialchars((string) $inc, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $inc, ENT_QUOTES, 'UTF-8') ?></option>
<?php endforeach; ?>
</select>
</div>
</div>

</div>
</div>
</div>

<div class="panel panel-default mt-4">
<div class="panel-heading">
<h4 class="panel-title">
<a data-toggle="collapse" data-parent="#search-accordion" href="#adv_eating"> Eating habits / Drinking / Smoking Details</a>
</h4>
</div>
<div id="adv_eating" class="panel-collapse collapse">
<div class="panel-body">

<div class="row mt-4">
<div class="col-md-4"><p class="ad-name">Eating Habits:</p></div>
<div class="col-md-8">
<select data-placeholder="Select Eating Habits" name="diet[]" class="chosen-select form-control" multiple tabindex="4">
<?php foreach ($diet_opts as $opt): ?>
<option value="<?= htmlspecialchars($opt, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($opt, ENT_QUOTES, 'UTF-8') ?></option>
<?php endforeach; ?>
</select>
</div>
</div>

<div class="row mt-4">
<div class="col-md-4"><p class="ad-name">Drinking:</p></div>
<div class="col-md-8">
<select data-placeholder="Select Drinking Habits" name="drink[]" class="chosen-select form-control" multiple tabindex="4">
<?php foreach ($drink_opts as $opt): ?>
<option value="<?= htmlspecialchars($opt, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($opt, ENT_QUOTES, 'UTF-8') ?></option>
<?php endforeach; ?>
</select>
</div>
</div>

<div class="row mt-4">
<div class="col-md-4"><p class="ad-name">Smoking:</p></div>
<div class="col-md-8">
<select data-placeholder="Select Smoking Habits" name="smoking[]" class="chosen-select form-control" multiple tabindex="4">
<?php foreach ($smoke_opts as $opt): ?>
<option value="<?= htmlspecialchars($opt, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($opt, ENT_QUOTES, 'UTF-8') ?></option>
<?php endforeach; ?>
</select>
</div>
</div>

</div>
</div>
</div>

<div class="panel panel-default mt-4">
<div class="panel-heading">
<h4 class="panel-title">
<a data-toggle="collapse" data-parent="#search-accordion" href="#adv_appearance"> Appearance</a>
</h4>
</div>
<div id="adv_appearance" class="panel-collapse collapse">
<div class="panel-body">

<div class="row mt-4">
<div class="col-md-4"><p class="ad-name">Complexion:</p></div>
<div class="col-md-8">
<select data-placeholder="Select Complexion" name="complexion[]" class="chosen-select form-control" multiple tabindex="4">
<?php foreach ($complexion_opts as $opt): ?>
<option value="<?= htmlspecialchars($opt, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($opt, ENT_QUOTES, 'UTF-8') ?></option>
<?php endforeach; ?>
<?php foreach ($complexions ?? [] as $cx): ?>
<?php if ($cx === '' || in_array($cx, $complexion_opts, true)) { continue; } ?>
<option value="<?= htmlspecialchars((string) $cx, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $cx, ENT_QUOTES, 'UTF-8') ?></option>
<?php endforeach; ?>
</select>
</div>
</div>

<div class="row mt-4">
<div class="col-md-4"><p class="ad-name">Body type:</p></div>
<div class="col-md-8">
<select data-placeholder="Select Body type" name="bodytype[]" class="chosen-select form-control" multiple tabindex="4">
<?php foreach ($bodytype_opts as $opt): ?>
<option value="<?= htmlspecialchars($opt, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($opt, ENT_QUOTES, 'UTF-8') ?></option>
<?php endforeach; ?>
<?php foreach ($body_types ?? [] as $bt): ?>
<?php if ($bt === '' || in_array($bt, $bodytype_opts, true)) { continue; } ?>
<option value="<?= htmlspecialchars((string) $bt, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $bt, ENT_QUOTES, 'UTF-8') ?></option>
<?php endforeach; ?>
</select>
</div>
</div>

</div>
</div>
</div>

<div class="panel panel-default mt-4">
<div class="panel-heading">
<h4 class="panel-title">
<a data-toggle="collapse" data-parent="#search-accordion" href="#adv_horoscope"> Horoscope Details</a>
</h4>
</div>
<div id="adv_horoscope" class="panel-collapse collapse">
<div class="panel-body">

<div class="row mt-4">
<div class="col-md-4"><p class="ad-name">Star:</p></div>
<div class="col-md-8">
<select data-placeholder="Select Star" name="star[]" class="chosen-select form-control" multiple tabindex="4">
</select>
</div>
</div>

<div class="row mt-4">
<div class="col-md-4"><p class="ad-name">Manglik:</p></div>
<div class="col-md-8">
<select data-placeholder="Select Manglik" name="manglik[]" class="chosen-select form-control" multiple tabindex="4">
</select>
</div>
</div>

</div>
</div>
</div>

</div>
</div>
</div>

<!-- Search Button -->
<div class="row mt-5">
<div class="col-md-4"></div>
<div class="col-md-4">
<button type="submit" class="add-w-btn">
<i class="fas fa-search"></i> Search
</button>
</div>
</div>

<input type="hidden" name="csrf_new_matrimonial" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
<input type="hidden" name="search_page_nm" value="Advance Search">
<input type="hidden" name="save_search" id="adv_save_search" value="">

</form>
</div>
</div>

<!-- Sidebar -->
<div class="col-md-4">

<div class="mega-box-new">
<p><strong>Profile Id Search</strong></p>
<hr class="search-hr">

<form action="<?= BASE_URL ?>/search/search" method="POST">
<input type="hidden" name="search_mode" value="id">
<div class="row">
<div class="col-md-9">
<input type="text" class="form-control" name="txt_id_search" placeholder="Enter Profile ID">
</div>
<div class="col-md-3">
<button type="submit" class="add-w-btn">
<i class="fas fa-search"></i>
</button>
</div>
</div>
</form>
</div>

<div class="mega-box-new">
<p><strong>Keyword Search</strong></p>
<hr class="search-hr">

<form action="<?= BASE_URL ?>/search/search" method="POST">
<input type="hidden" name="search_mode" value="name">
<div class="row">
<div class="col-md-9">
<input type="text" class="form-control" name="keyword" placeholder="Keyword Search">
</div>
<div class="col-md-3">
<button type="submit" class="add-w-btn">
<i class="fas fa-search"></i>
</button>
</div>
</div>
</form>
</div>

</div>

</div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>

<script>
$(document).ready(function(){
    $(".chosen-select").chosen({
        width:"100%"
    });
    $('#search-accordion').on('shown.bs.collapse', function () {
        $(this).find('.chosen-select').trigger('chosen:updated');
    });
});
</script>
