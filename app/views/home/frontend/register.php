<section class="register-section">

<div class="container">








<div class="row justify-content-center">

<div class="col-lg-7">

<div class="register-card">
    
      <div class="tab-pane active" id="tab_default_1">
                          <?php if (!empty($_SESSION['success'])): ?>
  <div class="alert alert-success">
    <?= htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8') ?>
  </div>
  <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['error'])): ?>
  <div class="alert alert-danger">
    <?= htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8') ?>
  </div>
  <?php unset($_SESSION['error']); ?>
<?php endif; ?>

                          <form
                            method="post"
                            id="register_step1"
                            name="register_step1"
                            action="<?= BASE_URL ?>/register-user"
                          >
                            <div
                              id="phone_error"
                              class="text-danger"
                              style="
                                display: none;
                                margin-top: 5px;
                                margin-bottom: 5px;
                              "
                            ></div>
                            <div
                              id="reponse_message_step1"
                              class="snackbar-register"
                              style="margin-bottom: 0px"
                            ></div>
                            <div class="clearfix"></div>
                            <div class="row margin-top-0">
                              <div class="col-md-2 col-xs-2 col-sm-2"></div>
                              <div
                                class="col-md-10 col-xs-10 col-sm-10 text-center"
                              >
                                <div class="">
                                  <div
                                    class="md-radio"
                                    onclick="add_gender_class('male')"
                                  >
                                    <input
                                      id="1"
                                      type="radio"
                                      name="g"
                                      checked=""
                                    />
                                    <label
                                      for="1"
                                      class="Poppins-Medium default-color color-d"
                                      id="male_id"
                                      >Male</label
                                    >
                                  </div>
                                  <div
                                    class="md-radio"
                                    onclick="add_gender_class('female')"
                                  >
                                    <input id="2" type="radio" name="g" />
                                    <label
                                      for="2"
                                      class="default-color"
                                      id="female_id"
                                      >Female</label
                                    >
                                  </div>
                                  <input
                                    type="hidden"
                                    name="gender"
                                    id="gender"
                                    value="Male"
                                  />
                                </div>
                              </div>
                            </div>
                            <div class="row margin-top-30">
                              <div
                                class="col-md-6 col-xs-12 col-sm-6 reg-pad-r-10"
                              >
                                <div class="register-input-palce">
                                  <input
                                    type="text"
                                    id="firstname"
                                    name="firstname"
                                    pattern="[A-Za-z]+"
                                    required=""
                                    placeholder="First Name"
                                    class="cstm-form"
                                  />
                                </div>
                              </div>
                              <div
                                class="col-md-6 col-xs-12 col-sm-6 reg-pad-l-10"
                              >
                                <div class="register-input-palce">
                                  <input
                                    type="text"
                                    id="lastname"
                                    name="lastname"
                                    required=""
                                    placeholder="Last Name"
                                    pattern="[A-Za-z]+"
                                    class="cstm-form"
                                  />
                                </div>
                              </div>
                            </div>
                            <div class="row margin-top-0">
                              <div class="col-md-12 col-xs-12 col-sm-9">
                                <div class="register-input-palce">
                                  <input
                                    type="tel"
                                    required=""
                                    name="phone_input"
                                    id="phone_input"
                                    minlength="7"
                                    maxlength="13"
                                    class="cstm-form"
                                  />
                                  <input
                                    type="hidden"
                                    name="mobile_number"
                                    id="mobile_number"
                                  />
                                  <input
                                    type="hidden"
                                    name="country_code"
                                    id="country_code"
                                  />
                                  <input type="hidden" name="_token" value="" />
                                  <input
                                    type="hidden"
                                    name="number_status"
                                    id="number_status"
                                    value="invalid"
                                  />
                                </div>
                              </div>
                            </div>
                            <div class="row margin-top-0">
                              <div class="col-md-12 col-xs-12 col-sm-12">
                                <div class="register-input-palce">
                                  <input
                                    type="email"
                                    required=""
                                    name="email"
                                    placeholder="E-Mail ID"
                                    class="cstm-form"
                                  />
                                  <input
                                    type="hidden"
                                    name="email_varifired"
                                    id="email_varifired"
                                    value="0"
                                  />
                                  <input
                                    type="hidden"
                                    name="is_post"
                                    id="is_post"
                                    value="1"
                                  />
                                </div>
                              </div>
                            </div>
                            <div class="row margin-top-0">
                              <div class="col-md-12 col-xs-12 col-sm-12">
                                <div class="register-input-palce">
                                  <input
                                    id="password-field"
                                    type="password"
                                    required=""
                                    name="password"
                                    placeholder="Password"
                                    class="myPsw cstm-form"
                                  />
                                  <span
                                    toggle="#password-field"
                                    class="fa fa-fw fa-eye field-icon toggle-password"
                                  ></span>
                                </div>
                              </div>
                            </div>
                            <div class="row margin-top-0">
                              <div class="col-md-12 col-xs-12 col-sm-12">
                                <div class="">
                                  <div
                                    class="col-md-4 col-sm-4 col-xs-4"
                                    style="padding-left: 0px"
                                  >
                                    <select
                                      style="width: 100%"
                                      class="form-control select-cust w-75 select2"
                                      name="birth_date"
                                      id="birth_date"
                                      required=""
                                    >
                                      <option value="">Date</option>
                                      <option selected="" value="1">1</option>
                                      <option value="2">2</option>
                                      <option value="3">3</option>
                                      <option value="4">4</option>
                                      <option value="5">5</option>
                                      <option value="6">6</option>
                                      <option value="7">7</option>
                                      <option value="8">8</option>
                                      <option value="9">9</option>
                                      <option value="10">10</option>
                                      <option value="11">11</option>
                                      <option value="12">12</option>
                                      <option value="13">13</option>
                                      <option value="14">14</option>
                                      <option value="15">15</option>
                                      <option value="16">16</option>
                                      <option value="17">17</option>
                                      <option value="18">18</option>
                                      <option value="19">19</option>
                                      <option value="20">20</option>
                                      <option value="21">21</option>
                                      <option value="22">22</option>
                                      <option value="23">23</option>
                                      <option value="24">24</option>
                                      <option value="25">25</option>
                                      <option value="26">26</option>
                                      <option value="27">27</option>
                                      <option value="28">28</option>
                                      <option value="29">29</option>
                                      <option value="30">30</option>
                                      <option value="31">31</option>
                                    </select>
                                  </div>
                                  <div
                                    class="col-md-4 col-sm-4 col-xs-4"
                                    style="padding: 0px"
                                  >
                                    <select
                                      style="width: 100%"
                                      class="form-control select2"
                                      onchange="month_year_change()"
                                      name="birth_month"
                                      id="birth_month"
                                      required=""
                                    >
                                      <option value="">Month</option>
                                      <option selected="" value="01">
                                        Jan
                                      </option>
                                      <option value="02">Feb</option>
                                      <option value="03">March</option>
                                      <option value="04">April</option>
                                      <option value="05">May</option>
                                      <option value="06">Jun</option>
                                      <option value="07">July</option>
                                      <option value="08">Aug</option>
                                      <option value="09">Sept</option>
                                      <option value="10">Oct</option>
                                      <option value="11">Nov</option>
                                      <option value="12">Dec</option>
                                    </select>
                                  </div>
                                  <div
                                    class="col-md-4 col-sm-4 col-xs-4"
                                    style="padding-right: 0px"
                                  >
                                    <select
                                      style="width: 100%"
                                      class="form-control select2"
                                      onchange="month_year_change()"
                                      name="birth_year"
                                      id="birth_year"
                                      required=""
                                    >
                                      <option value="">Year</option>
                                      <option selected="" value="2007">
                                        2007
                                      </option>
                                      <option value="2006">2006</option>
                                      <option value="2005">2005</option>
                                      <option value="2004">2004</option>
                                      <option value="2003">2003</option>
                                      <option value="2002">2002</option>
                                      <option value="2001">2001</option>
                                      <option value="2000">2000</option>
                                      <option value="1999">1999</option>
                                      <option value="1998">1998</option>
                                      <option value="1997">1997</option>
                                      <option value="1996">1996</option>
                                      <option value="1995">1995</option>
                                      <option value="1994">1994</option>
                                      <option value="1993">1993</option>
                                      <option value="1992">1992</option>
                                      <option value="1991">1991</option>
                                      <option value="1990">1990</option>
                                      <option value="1989">1989</option>
                                      <option value="1988">1988</option>
                                      <option value="1987">1987</option>
                                      <option value="1986">1986</option>
                                      <option value="1985">1985</option>
                                      <option value="1984">1984</option>
                                      <option value="1983">1983</option>
                                      <option value="1982">1982</option>
                                      <option value="1981">1981</option>
                                      <option value="1980">1980</option>
                                      <option value="1979">1979</option>
                                      <option value="1978">1978</option>
                                      <option value="1977">1977</option>
                                      <option value="1976">1976</option>
                                      <option value="1975">1975</option>
                                      <option value="1974">1974</option>
                                      <option value="1973">1973</option>
                                      <option value="1972">1972</option>
                                      <option value="1971">1971</option>
                                      <option value="1970">1970</option>
                                      <option value="1969">1969</option>
                                      <option value="1968">1968</option>
                                      <option value="1967">1967</option>
                                      <option value="1966">1966</option>
                                      <option value="1965">1965</option>
                                      <option value="1964">1964</option>
                                      <option value="1963">1963</option>
                                      <option value="1962">1962</option>
                                      <option value="1961">1961</option>
                                      <option value="1960">1960</option>
                                    </select>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="row margin-top-20 margin-bottom-10">
                              <div class="col-md-12 col-xs-12 col-sm-12">
                                <div class="">
                                  <select
                                    class="cstm-form select2 select-cust"
                                    required=""
                                    name="religion"
                                    id="religion"
                                    onchange="dropdownChange('religion','sect','sect_list')"
                                    style="width: 100%"
                                  >
                                    <option value="">Select Religion</option>
                                    <option value="53">Muslim</option>
                                    <option value="54">Sikh</option>
                                    <option value="52">Hindu</option>
                                    <option value="51">Christian</option>
                                    <option value="47">Qadiyani</option>
                                  </select>
                                </div>
                              </div>
                            </div>
                            <!-- <div class="row margin-top-20">
													<div class="col-md-12 col-xs-12 col-sm-12">
														<div class="">
															<select class="cstm-form select2 select-cust" required name="sect" id="sect" style="width:100%;">
																<option value="">Select Your Religion First</option>
															</select>
														</div>
													</div>
												</div> -->
                            <div class="row">
                              <!-- captcha_11 -->
                              <div
                                class="col-md-3 col-sm-3 col-xs-6"
                                id="captcha_login"
                              >
                                <img
                                  src="<?= BASE_URL ?>/assets/images/captcha.php"
                                  style="
                                    border-radius: 6px;
                                    width: 84px;
                                    height: auto;
                                  "
                                  alt="Captcha"
                                />
                              </div>
                              <div
                                class="col-md-2 col-sm-3 col-xs-6"
                                style="text-align: center"
                              >
                                <!-- <a title="Change Captcha Code" href="javascript:;" onClick="change_captcha_code('captcha_login','captcha_code')"><i title="Change Captcha Code" class="fa fa-refresh fa-1 curser_icon"></i></a> -->
                              </div>
                              <div class="col-md-7 col-sm-7 col-xs-12">
                                <input
                                  required=""
                                  type="number"
                                  name="code_captcha"
                                  id="code_captcha"
                                  class="form-control reg_input"
                                  placeholder="Enter Captcha"
                                  value=""
                                />
                              </div>
                            </div>
                            <div class="row margin-top-20">
                              <div
                                class="col-md-12 col-xs-12 col-sm-12 register-input-palce reg-pad-r-0"
                              >
                                <input
                                  type="checkbox"
                                  id="terms"
                                  name="terms"
                                  value="Yes"
                                /><label for="terms" class="reg-cb-text"
                                  >I agree to the<a
                                    href="#myModal505"
                                    data-toggle="modal"
                                    class="color-d"
                                  >
                                    Terms And Conditions</a
                                  ></label
                                >
                              </div>
                            </div>
                            <div class="row margin-top-0">
                              <div
                                class="col-md-7 col-sm-12 col-xs-12 reg-pad-r-0 reg-cb"
                              >
                                <input
                                  type="hidden"
                                  name="status_front_page"
                                  id="status_front_page"
                                  value="Yes"
                                />
                                <input type="hidden" name="id" value="" />
                                <input type="hidden" name="mode" value="add" />
                                <input
                                  type="hidden"
                                  name="csrf_new_matrimonial"
                                  value="e22c3c57fed5004540a6acb1d53ee0e8"
                                  id="hash_tocken_id1"
                                  class="hash_tocken_id"
                                />
                                <!-- <input type="checkbox" id="term" name="term" value="term"> for="term"  -->
                                <label class="reg-cb-text"
                                  >Already a member ?
                                  <a href="./login.php" onclick="login_tab()"
                                    >Login</a
                                  ></label
                                >
                              </div>
                              <input
                                type="hidden"
                                name="is_post"
                                id="is_post1"
                                value="1"
                              />
                              <input
                                type="hidden"
                                name="is_home"
                                id="is_home"
                                value="yes"
                              />
                              <input
                                type="hidden"
                                name="check_duplicate"
                                id="check_duplicate"
                                value="No"
                              />
                              <div
                                class="col-md-5 col-xs-12 col-sm-12 text-center"
                              >
                                <button class="btn reg-btn" id="register">
                                  Register
                                </button>
                              </div>
                            </div>
                          </form>
                        </div>
                        <!-- </div> -->
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
    </div>

</div>

</section>


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>

<script>
const input = document.querySelector("#phone_input");

window.intlTelInput(input,{
initialCountry:"pk",
separateDialCode:true,
preferredCountries:["pk","us","gb"],
utilsScript:"https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
});
</script>

<style>
.register-section{
background:linear-gradient(135deg,#f6f9ff,#eef2ff);
padding:80px 20px;
}

.register-card{
background:#fff;
padding:40px;
border-radius:14px;
box-shadow:0 15px 35px rgba(0,0,0,0.1);
transition:0.3s;
}

.register-card:hover{
transform:translateY(-5px);
box-shadow:0 20px 45px rgba(0,0,0,0.15);
}

.register-title{
text-align:center;
font-weight:700;
margin-bottom:35px;
color:#333;
}

.form-group{
position:relative;
margin-bottom:25px;
}

.form-group input{
width:100%;
padding:14px 12px;
border:1px solid #ddd;
border-radius:6px;
outline:none;
font-size:14px;
transition:0.3s;
}

.form-group label{
position:absolute;
top:50%;
left:12px;
transform:translateY(-50%);
background:#fff;
padding:0 5px;
color:#888;
font-size:14px;
pointer-events:none;
transition:0.3s;
}

.form-group input:focus{
border-color:#6c63ff;
box-shadow:0 0 0 3px rgba(108,99,255,0.1);
}

.form-group input:focus + label,
.form-group input:valid + label{
top:-8px;
font-size:12px;
color:#6c63ff;
}

.custom-select{
width:100%;
padding:12px;
border:1px solid #ddd;
border-radius:6px;
margin-bottom:20px;
transition:0.3s;
}

.custom-select:focus{
border-color:#6c63ff;
box-shadow:0 0 0 3px rgba(108,99,255,0.1);
}

.register-btn{
width:100%;
padding:14px;
border:none;
border-radius:8px;
background:linear-gradient(135deg,#6c63ff,#4b47e0);
color:#fff;
font-weight:600;
font-size:16px;
cursor:pointer;
transition:0.3s;
}

.register-btn:hover{
transform:translateY(-2px);
box-shadow:0 10px 20px rgba(108,99,255,0.3);
}

.terms{
margin-bottom:20px;
font-size:14px;
}

.login-link{
text-align:center;
margin-top:15px;
font-size:14px;
}

.login-link a{
color:#6c63ff;
font-weight:600;
text-decoration:none;
}

.iti{
width:100%;
}
</style>