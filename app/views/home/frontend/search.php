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

<form action="<?= BASE_URL ?>/search/search" method="POST">

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
<select class="form-control" name="from_age">
<?php for($i=18;$i<=65;$i++): ?>
<option value="<?= $i ?>"><?= $i ?> Year</option>
<?php endfor; ?>
</select>
</div>

<div class="col-md-2 text-center">
<label style="line-height:44px;">To</label>
</div>

<div class="col-md-3">
<select class="form-control" name="to_age">
<?php for($i=18;$i<=65;$i++): ?>
<option value="<?= $i ?>" <?= $i==30?'selected':'' ?>><?= $i ?> Year</option>
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

<div class="col-md-2 text-center">
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

<!-- Religion -->
<div class="row mt-4">
<div class="col-md-4"><p class="ad-name">Religion:</p></div>
<div class="col-md-8">
<select name="religion[]" class="chosen-select form-control" multiple>
<?php foreach($religions ?? [] as $r): ?>
<option value="<?= $r['id'] ?>"><?= $r['name'] ?></option>
<?php endforeach; ?>
</select>
</div>
</div>

<!-- Caste -->
<div class="row mt-4">
<div class="col-md-4"><p class="ad-name">Caste:</p></div>
<div class="col-md-8">
<select name="caste[]" class="chosen-select form-control" multiple>
<?php foreach($castes ?? [] as $c): ?>
<option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
<?php endforeach; ?>
</select>
</div>
</div>

<!-- Mother Tongue -->
<div class="row mt-4">
<div class="col-md-4"><p class="ad-name">Mother Tongue:</p></div>
<div class="col-md-8">
<select name="mothertongue[]" class="chosen-select form-control" multiple>
<?php foreach($languages ?? [] as $lang): ?>
<option value="<?= $lang['id'] ?>"><?= $lang['name'] ?></option>
<?php endforeach; ?>
</select>
</div>
</div>

<!-- Education -->
<div class="row mt-4">
<div class="col-md-4"><p class="ad-name">Education:</p></div>
<div class="col-md-8">
<select name="education[]" class="chosen-select form-control" multiple>
<?php foreach($education ?? [] as $edu): ?>
<option value="<?= $edu['id'] ?>"><?= $edu['name'] ?></option>
<?php endforeach; ?>
</select>
</div>
</div>

<!-- Country -->
<div class="row mt-4">
<div class="col-md-4"><p class="ad-name">Country:</p></div>
<div class="col-md-8">
<select name="country[]" class="chosen-select form-control" multiple>
<?php foreach($countries ?? [] as $country): ?>
<option value="<?= $country['id'] ?>"><?= $country['name'] ?></option>
<?php endforeach; ?>
</select>
</div>
</div>

<!-- Photo -->
<div class="row mt-4">
<div class="col-md-4"><p class="ad-name">Photo Setting:</p></div>
<div class="col-md-8">
<label class="photo-check">
<input type="checkbox" name="photo_search" value="1"> With Photo
</label>
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

</form>
</div>
</div>

<!-- Sidebar -->
<div class="col-md-4">

<div class="mega-box-new">
<p><strong>Profile Id Search</strong></p>
<hr class="search-hr">

<form action="<?= BASE_URL ?>/search/search" method="POST">
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
});
</script>