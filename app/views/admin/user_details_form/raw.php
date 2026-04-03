    <div class="card shadow p-4">
        <h3 class="mb-4 text-primary">Basic Details</h3>

        <form id="userForm" action="<?= BASE_URL; ?>/admin/user/basic" method="POST">

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label>First Name</label>
                    <input type="text" name="first_name" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Second Name</label>
                    <input type="text" name="second_name" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Gender</label>
                    <select name="gender" class="form-control" required>
                        <option value="">Select</option>
                        <option>Male</option>
                        <option>Female</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Mobile Number</label>
                    <input type="text" name="mobile_number" class="form-control" required pattern="03[0-9]{9}">
                    <small class="text-muted">Format: 03XXXXXXXXX</small>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Date of Birth</label>
                    <input type="date" name="dob" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Religion</label>
                    <select name="religion" id="religion" class="form-control" required>
                        <option value="">Select</option>
                        <option value="Muslim">Muslim</option>
                        <option value="Hindu">Hindu</option>
                        <option value="Christian">Christian</option>
                        <option value="Sikh">Sikh</option>
                    </select>
                </div>

                <!-- CASTE -->
                <div class="col-md-6 mb-3">
                    <label>Caste</label>
                    <select name="caste" id="caste" class="form-control" required>
                        <option value="">Select religion first</option>
                    </select>

                    <!-- Custom caste -->
                    <input type="text" id="customCaste" class="form-control mt-2 d-none" placeholder="Enter your caste">
                </div>

                <!-- EDUCATION -->
                <div class="col-md-6 mb-3">
                    <label>Education</label>
                    <select name="education" id="education" class="form-control" required>
                        <option value="">Select</option>

                        <optgroup label="BS Programs (4 Years)">
                            <option value="bs-cs">BS Computer Science</option>
                            <option value="bs-se">BS Software Engineering</option>
                            <option value="bs-it">BS Information Technology</option>
                            <option value="bs-ba">BS Business Administration (BBA)</option>
                            <option value="bs-accf">BS Accounting & Finance</option>
                            <option value="bs-ee">BS Electrical Engineering</option>
                            <option value="bs-me">BS Mechanical Engineering</option>
                            <option value="bs-civil">BS Civil Engineering</option>
                            <option value="bs-phys">BS Physics</option>
                            <option value="bs-chem">BS Chemistry</option>
                            <option value="bs-math">BS Mathematics</option>
                            <option value="bs-bio">BS Biotechnology</option>
                            <option value="bs-eng">BS English</option>
                            <option value="bs-eco">BS Economics</option>
                            <option value="bs-soc">BS Sociology</option>
                            <option value="bs-law">BS Law (LLB - 5 Years)</option>
                        </optgroup>
                        <optgroup label="Associate Degrees (ADP - 2 Years)">
                            <option value="adp-cs">ADP Computer Science</option>
                            <option value="adp-ba">ADP Business Administration</option>
                            <option value="adp-com">ADP Commerce</option>
                            <option value="adp-sci">ADP General Science</option>
                        </optgroup>
                        <optgroup label="Diplomas & Technical (DAE/Medical)">
                            <option value="dae-elect">DAE Electronics</option>
                            <option value="dae-mech">DAE Mechanical</option>
                            <option value="dae-civil">DAE Civil</option>
                            <option value="dip-med">Medical Lab Technology (MLT)</option>
                            <option value="dip-pharm">Pharmacy Technician</option>
                            <option value="dip-rad">Radiology Technician</option>
                            <option value="dip-it">Information Technology Diploma</option>
                            <option value="pgd-mgt">Post Graduate Diploma (PGD) Management</option>
                        </optgroup>
                        <option value="other">Other</option>

                    </select>

                    <!-- Custom degree -->
                    <input type="text" id="customEducation" class="form-control mt-2 d-none" placeholder="Enter your degree">
                </div>

            </div>

            <button type="submit" class="btn btn-primary w-100 mt-3">Save</button>
        </form>
    </div>