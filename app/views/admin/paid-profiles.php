<?php
$title = "Paid Profiles";
require __DIR__.'/partials/header.php';
require __DIR__.'/partials/sidebar.php';
?>

<style>

.admin-content{
background:#f5f5f5;
min-height:100vh;
padding:25px;
}

.page-title{
font-size:24px;
font-weight:600;
color:#444;
margin-bottom:20px;
}

/* TOP SEARCH */

.top-bar{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:25px;
flex-wrap:wrap;
gap:10px;
}

.search-box{
max-width:350px;
width:100%;
}

.search-box input{
height:42px;
border-radius:6px;
border:1px solid #ddd;
}

/* CARDS GRID */

.profile-grid{
display:grid;
grid-template-columns:repeat(auto-fill,minmax(260px,1fr));
gap:20px;
}

.profile-card{
background:#fff;
border-radius:8px;
padding:20px;
box-shadow:0 1px 4px rgba(0,0,0,.08);
text-align:center;
transition:.2s;
}

.profile-card:hover{
transform:translateY(-3px);
}

/* USER IMAGE */

.user-img{
width:90px;
height:90px;
border-radius:50%;
object-fit:cover;
margin-bottom:12px;
border:3px solid #eee;
}

/* NAME */

.user-name{
font-size:17px;
font-weight:600;
color:#444;
margin-bottom:5px;
}

/* PACKAGE */

.package{
font-size:14px;
color:#777;
margin-bottom:12px;
}

/* DATES */

.date{
font-size:13px;
color:#666;
margin-bottom:3px;
}

/* STATUS */

.badge-active{
background:#2ecc71;
color:#fff;
padding:5px 10px;
font-size:12px;
border-radius:4px;
display:inline-block;
margin-top:8px;
}

.badge-expired{
background:#e74c3c;
color:#fff;
padding:5px 10px;
font-size:12px;
border-radius:4px;
display:inline-block;
margin-top:8px;
}

/* ACTION BUTTONS */

.actions{
margin-top:15px;
display:flex;
justify-content:center;
gap:8px;
}

.btn-edit{
background:#2196f3;
border:none;
color:white;
padding:6px 12px;
font-size:12px;
}

.btn-delete{
background:#e74c3c;
border:none;
color:white;
padding:6px 12px;
font-size:12px;
}

.btn-edit:hover{
background:#1976d2;
color:#fff;
}

.btn-delete:hover{
background:#c0392b;
color:#fff;
}

</style>


<div class="admin-content">

<div class="page-title">Paid Profiles</div>


<!-- TOP SEARCH BAR -->

<div class="top-bar">

<div class="search-box">
<input type="text" id="searchInput" class="form-control" placeholder="Search user...">
</div>

</div>



<!-- PROFILE CARDS -->

<div class="profile-grid" id="profileGrid">

<?php foreach ($profiles as $p): ?>

<?php

$statusLabel="";
$statusClass="";

if(!empty($p['expires_at']) && strtotime($p['expires_at']) < time()){
$statusLabel="Expired";
$statusClass="badge-expired";
}else{
$statusLabel="Active";
$statusClass="badge-active";
}

?>

<div class="profile-card" data-name="<?= strtolower($p['first_name'].' '.$p['last_name']) ?>">

<img class="user-img" src="<?= BASE_URL ?>/uploads/users/<?= $p['image'] ?? 'default.png' ?>">

<div class="user-name">
<?= htmlspecialchars($p['first_name'].' '.$p['last_name']) ?>
</div>

<div class="package">
<?= htmlspecialchars($p['package_name']) ?>
</div>

<div class="date">
Start: <?= $p['started_at'] ?? '-' ?>
</div>

<div class="date">
End: <?= $p['expires_at'] ?? '-' ?>
</div>

<span class="<?= $statusClass ?>">
<?= $statusLabel ?>
</span>

<div class="actions">

<a class="btn btn-edit"
href="<?= BASE_URL ?>/admin/paid-profiles/edit?id=<?= $p['id'] ?>">
Edit
</a>

<form method="post"
action="<?= BASE_URL ?>/admin/paid-profiles/delete"
onsubmit="return confirm('Delete this package?')">

<input type="hidden" name="id" value="<?= $p['id'] ?>">

<button class="btn btn-delete">
Delete
</button>

</form>

</div>

</div>

<?php endforeach; ?>

</div>

</div>



<script>

/* SEARCH FILTER */

document.getElementById("searchInput").addEventListener("keyup",function(){

let value=this.value.toLowerCase();

document.querySelectorAll(".profile-card").forEach(function(card){

let name=card.getAttribute("data-name");

card.style.display=name.includes(value) ? "block" : "none";

});

});

</script>

<?php require __DIR__.'/partials/footer.php'; ?>