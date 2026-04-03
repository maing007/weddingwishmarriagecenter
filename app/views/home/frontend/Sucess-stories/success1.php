
<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

/* Top Banner */
.story-banner {
    background: linear-gradient(rgba(123,0,28,0.85), rgba(123,0,28,0.85)),
                url('images/banner-bg.jpg');
    background-size: cover;
    background-position: center;
    padding: 70px 0;
    text-align: center;
    color: white;
}

.story-banner h1 {
    font-size: 38px;
    font-weight: 700;
    margin-bottom: 10px;
}

.story-banner p {
    font-size: 18px;
    margin: 0;
}

/* Main Section */
.story-detail-section {
    background: #efd8a8;
    padding: 80px 0;
}

/* Description */
.description-title {
    font-size: 32px;
    font-weight: 700;
    color: #000;
    margin-bottom: 20px;
}

.description-text {
    font-size: 24px;
    line-height: 1.8;
    color: #111;
    margin-bottom: 50px;
}

/* Cards */
.info-card {
    background: #e8b531;
    border-radius: 10px;
    padding: 35px;
    box-shadow: 0 5px 18px rgba(0,0,0,0.08);
    height: 100%;
}

.info-card h3 {
    text-align: center;
    font-size: 28px;
    font-weight: 700;
    color: white;
    margin-bottom: 30px;
}

/* White Inner Box */
.info-inner {
    background: #f3f3f3;
    border-radius: 12px;
    padding: 30px;
}

/* Table Styling */
.info-table {
    width: 100%;
}

.info-table tr {
    border-bottom: 1px dashed #ccc;
}

.info-table td {
    padding: 14px 0;
    font-size: 14px;
    color: #333;
}

.info-table td:last-child {
    text-align: right;
}

/* Responsive */
@media (max-width: 991px) {
    .story-banner h1 {
        font-size: 34px;
    }

    .description-title {
        font-size: 30px;
    }

    .description-text {
        font-size: 18px;
    }

    .info-card h3 {
        font-size: 28px;
    }

    .info-table td {
        font-size: 18px;
    }
}
</style>


<!-- Top Banner -->
<section class="story-banner">
    <div class="container">
        <h1>Bilal and Sana Iram</h1>
        <p><strong>Home</strong> – Bilal And Sana Iram</p>
    </div>
</section>

<!-- Detail Section -->
<section class="story-detail-section">
    <div class="container">

        <!-- Description -->
        <h2 class="description-title">Description</h2>

        <p class="description-text">
            Marriage ends with the life of sharing, caring, and endless love for each other.
            Thanks to the ShadiHub team. It was a great experience and given a good opportunity
            in finding my life partner.
        </p>

        <!-- Cards -->
        <div class="row g-4">

            <!-- Male Info -->
            <div class="col-lg-6">
                <div class="info-card">
                    <h3>Male Info</h3>

                    <div class="info-inner">
                        <table class="info-table">
                            <tr><td>Name</td><td>Bilal</td></tr>
                            <tr><td>Caste</td><td>Gujar</td></tr>
                            <tr><td>Profile Ow</td><td>Father</td></tr>
                            <tr><td>Age</td><td>29</td></tr>
                            <tr><td>Country</td><td>Pakistan</td></tr>
                            <tr><td>Education</td><td>IT Engineer</td></tr>
                            <tr><td>Occupation</td><td>Job</td></tr>
                            <tr><td>Language</td><td>Punjabi</td></tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Female Info -->
            <div class="col-lg-6">
                <div class="info-card">
                    <h3>Female Info</h3>

                    <div class="info-inner">
                        <table class="info-table">
                            <tr><td>Name</td><td>Sana Iram</td></tr>
                            <tr><td>Caste</td><td>Gujar</td></tr>
                            <tr><td>Profile Ow</td><td>Mother</td></tr>
                            <tr><td>Age</td><td>25</td></tr>
                            <tr><td>Country</td><td>Pakistan</td></tr>
                            <tr><td>Occupation</td><td>BBA & MBA</td></tr>
                            <tr><td>Occupation</td><td>Job In State Bank Of Pakistan</td></tr>
                            <tr><td>Language</td><td>Punjabi</td></tr>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>
</section>
