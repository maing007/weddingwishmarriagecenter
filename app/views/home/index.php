    <!-- ====== Banner Section Start ====== -->
    <style>
      .urdu {
        font-family: 'nastliq', sans-serif;
        color: white;
      }

      #hero-custom-slider {
        width: 100%;
        position: relative;
        overflow: hidden;
      }

      #hero-custom-slider .custom-slider-wrapper {
        position: relative;
        width: 100%;
        height: 680px;
      }

      #hero-custom-slider .custom-slide {
        position: absolute;
        width: 100%;
        height: 100%;
        opacity: 0;
        transition: opacity 1s ease-in-out;
      }

      #hero-custom-slider .custom-slide.active {
        opacity: 1;
        z-index: 2;
      }

      #hero-custom-slider .custom-slide img {
        width: 100%;
        height: 100%;
        object-fit: cover;
      }


      @media(max-width:768px) {
        #hero-custom-slider .custom-slider-wrapper {
          height: 250px;
        }

        .urdu {
          color: black;
        }
      }
    </style>
    <script>
      document.addEventListener("DOMContentLoaded", function() {
        const slides = document.querySelectorAll("#hero-custom-slider .custom-slide");
        let current = 0;

        function showSlide(index) {
          slides.forEach(slide => slide.classList.remove("active"));
          slides[index].classList.add("active");
        }

        setInterval(() => {
          current++;
          if (current >= slides.length) {
            current = 0;
          }
          showSlide(current);
        }, 3000);
      });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <div class="banner-new">
      <div id="testimonial-slider" class="owl-carousel">
        <div class="testimonial_xx">
          <!--<img src="<?= BASE_URL ?>/assets/images/acd8713bfbd4b4c2ee59e2a3dc811596.png"-->
          <!--  title=""-->
          <!--  alt=""-->
          <!--/>-->
          <section id="hero-custom-slider">
            <div class="custom-slider-wrapper">

              <div class="custom-slide active">
                <img src="<?= BASE_URL ?>/assets/images/banner3.jpg" alt="Slide 1">
              </div>

              <div class="custom-slide">
                <img src="<?= BASE_URL ?>/assets/images/banner2.JPG" alt="Slide 2">
              </div>

              <div class="custom-slide">

                <img src="<?= BASE_URL ?>/assets/images/acd8713bfbd4b4c2ee59e2a3dc811596.png" alt="Slide 3">
              </div>

            </div>
          </section>
        </div>
      </div>
      <div class="container-fluid cust_padding">
        <div class="row">
          <div class="col-md-1 col-sm-12 col-xs-12"></div>
          <div class="col-md-5 col-sm-12 col-xs-12">
            <div class="indian-matri">
              <h2 class="urdu">ہم رشتے بانٹتے نہیں ، بناتے ہیں</h2>
              <p style="font-size: 20px">
                We bring people together.<br />
                Love unites them...
              </p>
            </div>
          </div>
        </div>
      </div>
      <div class="container-fluid cust_padding">
        <div class="row">
          <div class="col-md-8 col-sm-12 col-xs-12">
            <div class="search_new_ind">
              <div class="row">
                <div class="col-md-7 col-sm-12 col-xs-12">
                  <div class="register_box">
                    <h2>Free Register</h2>
                    <hr />
                    <div class="tabbable-panel">
                      <div class="tabbable-line">
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
                            action="<?= BASE_URL ?>/register-user">
                            <div
                              id="phone_error"
                              class="text-danger"
                              style="
                                display: none;
                                margin-top: 5px;
                                margin-bottom: 5px;
                              "></div>
                            <div
                              id="reponse_message_step1"
                              class="snackbar-register"
                              style="margin-bottom: 0px"></div>
                            <div class="clearfix"></div>
                            <div class="row margin-top-0">
                              <div class="col-md-2 col-xs-2 col-sm-2"></div>
                              <div
                                class="col-md-10 col-xs-10 col-sm-10 text-center">
                                <div class="">
                                  <div
                                    class="md-radio"
                                    onclick="add_gender_class('male')">
                                    <input
                                      id="1"
                                      type="radio"
                                      name="g"
                                      checked="" />
                                    <label
                                      for="1"
                                      class="Poppins-Medium default-color color-d"
                                      id="male_id">Male</label>
                                  </div>
                                  <div
                                    class="md-radio"
                                    onclick="add_gender_class('female')">
                                    <input id="2" type="radio" name="g" />
                                    <label
                                      for="2"
                                      class="default-color"
                                      id="female_id">Female</label>
                                  </div>
                                  <input
                                    type="hidden"
                                    name="gender"
                                    id="gender"
                                    value="Male" />
                                </div>
                              </div>
                            </div>
                            <div class="row margin-top-30">
                              <div
                                class="col-md-6 col-xs-12 col-sm-6 reg-pad-r-10">
                                <div class="register-input-palce">
                                  <input
                                    type="text"
                                    id="firstname"
                                    name="firstname"
                                    pattern="[A-Za-z]+"
                                    required=""
                                    placeholder="First Name"
                                    class="cstm-form" />
                                </div>
                              </div>
                              <div
                                class="col-md-6 col-xs-12 col-sm-6 reg-pad-l-10">
                                <div class="register-input-palce">
                                  <input
                                    type="text"
                                    id="lastname"
                                    name="lastname"
                                    required=""
                                    placeholder="Last Name"
                                    pattern="[A-Za-z]+"
                                    class="cstm-form" />
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
                                    class="cstm-form" />
                                  <input
                                    type="hidden"
                                    name="mobile_number"
                                    id="mobile_number" />
                                  <input
                                    type="hidden"
                                    name="country_code"
                                    id="country_code" />
                                  <input type="hidden" name="_token" value="" />
                                  <input
                                    type="hidden"
                                    name="number_status"
                                    id="number_status"
                                    value="invalid" />
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
                                    class="cstm-form" />
                                  <input
                                    type="hidden"
                                    name="email_varifired"
                                    id="email_varifired"
                                    value="0" />
                                  <input
                                    type="hidden"
                                    name="is_post"
                                    id="is_post"
                                    value="1" />
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
                                    class="myPsw cstm-form" />
                                  <span
                                    toggle="#password-field"
                                    class="fa fa-fw fa-eye field-icon toggle-password"></span>
                                </div>
                              </div>
                            </div>
                            <div class="row margin-top-0">
                              <div class="col-md-12 col-xs-12 col-sm-12">
                                <div class="">
                                  <div
                                    class="col-md-4 col-sm-4 col-xs-4"
                                    style="padding-left: 0px">
                                    <select
                                      style="width: 100%"
                                      class="form-control select-cust w-75 select2"
                                      name="birth_date"
                                      id="birth_date"
                                      required="">
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
                                    style="padding: 0px">
                                    <select
                                      style="width: 100%"
                                      class="form-control select2"
                                      onchange="month_year_change()"
                                      name="birth_month"
                                      id="birth_month"
                                      required="">
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
                                    style="padding-right: 0px">
                                    <select
                                      style="width: 100%"
                                      class="form-control select2"
                                      onchange="month_year_change()"
                                      name="birth_year"
                                      id="birth_year"
                                      required="">
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
                                    style="width: 100%">
                                    <option value="">Select Religion</option>
                                    <option value="Islam">Islam</option>
                                    <option value="Sikh">Sikh</option>
                                    <option value="Hindu">Hindu</option>
                                    <option value="Christian">Christian</option>
                                    <option value="Qadiyani">Qadiyani</option>
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
                                id="captcha_login">
                                <img
                                  src="<?= BASE_URL ?>/assets/images/captcha.php"
                                  style="
                                    border-radius: 6px;
                                    width: 84px;
                                    height: auto;
                                  "
                                  alt="Captcha" />
                              </div>
                              <div
                                class="col-md-2 col-sm-3 col-xs-6"
                                style="text-align: center">
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
                                  value="" />
                              </div>
                            </div>
                            <div class="row margin-top-20">
                              <div
                                class="col-md-12 col-xs-12 col-sm-12 register-input-palce reg-pad-r-0">
                                <input
                                  type="checkbox"
                                  id="terms"
                                  name="terms"
                                  value="Yes" /><label for="terms" class="reg-cb-text">I agree to the<a
                                    href="#myModal505"
                                    data-toggle="modal"
                                    class="color-d">
                                    Terms And Conditions</a></label>
                              </div>
                            </div>
                            <div class="row margin-top-0">
                              <div
                                class="col-md-7 col-sm-12 col-xs-12 reg-pad-r-0 reg-cb">
                                <input
                                  type="hidden"
                                  name="status_front_page"
                                  id="status_front_page"
                                  value="Yes" />
                                <input type="hidden" name="id" value="" />
                                <input type="hidden" name="mode" value="add" />
                                <input
                                  type="hidden"
                                  name="csrf_new_matrimonial"
                                  value="e22c3c57fed5004540a6acb1d53ee0e8"
                                  id="hash_tocken_id1"
                                  class="hash_tocken_id" />
                                <!-- <input type="checkbox" id="term" name="term" value="term"> for="term"  -->
                                <label class="reg-cb-text">Already a member ?
                                  <a href="<?= BASE_URL ?>/login" onclick="login_tab()">Login</a></label>
                              </div>
                              <input
                                type="hidden"
                                name="is_post"
                                id="is_post1"
                                value="1" />
                              <input
                                type="hidden"
                                name="is_home"
                                id="is_home"
                                value="yes" />
                              <input
                                type="hidden"
                                name="check_duplicate"
                                id="check_duplicate"
                                value="No" />
                              <div
                                class="col-md-5 col-xs-12 col-sm-12 text-center">
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
                <div class="col-md-5 col-sm-12 col-xs-12">
                  <div class="reg-fect-box text-center">
                    <div id="testimonial-slider-2" class="owl-carousel">
                      <div class="testimonial testimonial-reg-fect">
                        <img
                          src="<?= BASE_URL ?>/assets/images/425c03d6583e45ef07e460f29033cfa6.png"
                          alt="Secure Shield" />
                        <p>
                          Top-rated consultants | 100% verified profiles | 100%
                          Privacy |
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- banner contact -->
      <div class="container-fluid cust_padding hidden-sm hidden-xs">
        <div class="banner-contact">
          <div class="banner-contact-1">
            <h4>
              <img src="<?= BASE_URL ?>/assets/images/banner-contact-1.png" alt="Contact Number" /><a href="tel:++92322-6817540" style="color:white !important;" class="link-info">
                +92 322-6817540
              </a>
            </h4>
          </div>
          <br>
          <div class="banner-contact-1">
            <h4>
              <img src="<?= BASE_URL ?>/assets/images/banner-contact-1.png" alt="Contact Number" /><a href="tel:+92309-7688394" style="color:white !important;" class="link-info">
                +92 309-7688394
              </a>
            </h4>
          </div>
          <br>
          <div class="banner-contact-1">
            <h4>
              <img src="<?= BASE_URL ?>/assets/images/banner-contact-1.png" alt="Contact Number" /><a href="tel:+92309-5996132" style="color:white !important;" class="link-info">
                +92 309-5996132</a>
            </h4>
          </div>
          <!--<div class="banner-contact-2">-->
          <!--  <h4>-->
          <!--    <img src="<?= BASE_URL ?>/assets/images/banner-contact-2.png" alt="Skype Id" />Mehmood-->
          <!--  </h4>-->
          <!--</div>-->
        </div>
      </div>
      <!-- banner contact -->
    </div>
    <!-- ====== Banner Section Ends ====== -->

    <!-- ======== Search for your right partner Starts ========== -->
    <div class="search_for">
      <div class="container">
        <div class="row margin-top-10">
          <div class="col-md-12 col-xs-12 col-sm-12">
            <div class="search-box">
              <div class="row">
                <div class="col-md-12 col-sm-10 col-xs-12">
                  <form method="post" name="search_form" id="search_form" action="<?= BASE_URL ?>/search">
                    <div class="search-section">
                      <div
                        class="form-group col-md-3 col-sm-4 col-xs-12 no-padding land-lookingfor">
                        <div class="left">
                          <select
                            name="gender"
                            id="Looking"
                            class="custom-select sources"
                            style="display: none">
                            <option value="Female" title="Bride" selected="">
                              Bride
                            </option>
                            <option value="Male" title="Groom">Groom</option>
                          </select>
                        </div>
                      </div>
                      <div
                        class="form-group col-md-2 col-sm-2 col-xs-6 no-padding land-agefrom agefromto_mob-w">
                        <div class="left">
                          <select
                            name="from_age"
                            id="agefrom"
                            class="custom-select sources"
                            style="display: none">
                            <option selected="" value="18" title="18 Year">
                              18 Year
                            </option>
                            <option value="19" title="19 Year">19 Year</option>
                            <option value="20" title="20 Year">20 Year</option>
                            <option value="21" title="21 Year">21 Year</option>
                            <option value="22" title="22 Year">22 Year</option>
                            <option value="23" title="23 Year">23 Year</option>
                            <option value="24" title="24 Year">24 Year</option>
                            <option value="25" title="25 Year">25 Year</option>
                            <option value="26" title="26 Year">26 Year</option>
                            <option value="27" title="27 Year">27 Year</option>
                            <option value="28" title="28 Year">28 Year</option>
                            <option value="29" title="29 Year">29 Year</option>
                            <option value="30" title="30 Year">30 Year</option>
                            <option value="31" title="31 Year">31 Year</option>
                            <option value="32" title="32 Year">32 Year</option>
                            <option value="33" title="33 Year">33 Year</option>
                            <option value="34" title="34 Year">34 Year</option>
                            <option value="35" title="35 Year">35 Year</option>
                            <option value="36" title="36 Year">36 Year</option>
                            <option value="37" title="37 Year">37 Year</option>
                            <option value="38" title="38 Year">38 Year</option>
                            <option value="39" title="39 Year">39 Year</option>
                            <option value="40" title="40 Year">40 Year</option>
                            <option value="41" title="41 Year">41 Year</option>
                            <option value="42" title="42 Year">42 Year</option>
                            <option value="43" title="43 Year">43 Year</option>
                            <option value="44" title="44 Year">44 Year</option>
                            <option value="45" title="45 Year">45 Year</option>
                            <option value="46" title="46 Year">46 Year</option>
                            <option value="47" title="47 Year">47 Year</option>
                            <option value="48" title="48 Year">48 Year</option>
                            <option value="49" title="49 Year">49 Year</option>
                            <option value="50" title="50 Year">50 Year</option>
                            <option value="51" title="51 Year">51 Year</option>
                            <option value="52" title="52 Year">52 Year</option>
                            <option value="53" title="53 Year">53 Year</option>
                            <option value="54" title="54 Year">54 Year</option>
                            <option value="55" title="55 Year">55 Year</option>
                            <option value="56" title="56 Year">56 Year</option>
                            <option value="57" title="57 Year">57 Year</option>
                            <option value="58" title="58 Year">58 Year</option>
                            <option value="59" title="59 Year">59 Year</option>
                            <option value="60" title="60 Year">60 Year</option>
                            <option value="61" title="61 Year">61 Year</option>
                            <option value="62" title="62 Year">62 Year</option>
                            <option value="63" title="63 Year">63 Year</option>
                            <option value="64" title="64 Year">64 Year</option>
                            <option value="65" title="65 Year">65 Year</option>
                          </select>
                        </div>
                      </div>

                      <div class="col-xs-2 agetolabel">
                        <p class="left">to</p>
                      </div>

                      <div
                        class="form-group col-md-2 col-sm-2 col-xs-6 no-padding land-ageto agefromto_mob-w">
                        <div class="left">
                          <select
                            name="to_age"
                            id="ageto"
                            class="custom-select sources"
                            style="display: none">
                            <option value="18" title="18 Year">18 Year</option>
                            <option value="19" title="19 Year">19 Year</option>
                            <option value="20" title="20 Year">20 Year</option>
                            <option value="21" title="21 Year">21 Year</option>
                            <option value="22" title="22 Year">22 Year</option>
                            <option value="23" title="23 Year">23 Year</option>
                            <option value="24" title="24 Year">24 Year</option>
                            <option value="25" title="25 Year">25 Year</option>
                            <option value="26" title="26 Year">26 Year</option>
                            <option value="27" title="27 Year">27 Year</option>
                            <option value="28" title="28 Year">28 Year</option>
                            <option value="29" title="29 Year">29 Year</option>
                            <option selected="" value="30" title="30 Year">
                              30 Year
                            </option>
                            <option value="31" title="31 Year">31 Year</option>
                            <option value="32" title="32 Year">32 Year</option>
                            <option value="33" title="33 Year">33 Year</option>
                            <option value="34" title="34 Year">34 Year</option>
                            <option value="35" title="35 Year">35 Year</option>
                            <option value="36" title="36 Year">36 Year</option>
                            <option value="37" title="37 Year">37 Year</option>
                            <option value="38" title="38 Year">38 Year</option>
                            <option value="39" title="39 Year">39 Year</option>
                            <option value="40" title="40 Year">40 Year</option>
                            <option value="41" title="41 Year">41 Year</option>
                            <option value="42" title="42 Year">42 Year</option>
                            <option value="43" title="43 Year">43 Year</option>
                            <option value="44" title="44 Year">44 Year</option>
                            <option value="45" title="45 Year">45 Year</option>
                            <option value="46" title="46 Year">46 Year</option>
                            <option value="47" title="47 Year">47 Year</option>
                            <option value="48" title="48 Year">48 Year</option>
                            <option value="49" title="49 Year">49 Year</option>
                            <option value="50" title="50 Year">50 Year</option>
                            <option value="51" title="51 Year">51 Year</option>
                            <option value="52" title="52 Year">52 Year</option>
                            <option value="53" title="53 Year">53 Year</option>
                            <option value="54" title="54 Year">54 Year</option>
                            <option value="55" title="55 Year">55 Year</option>
                            <option value="56" title="56 Year">56 Year</option>
                            <option value="57" title="57 Year">57 Year</option>
                            <option value="58" title="58 Year">58 Year</option>
                            <option value="59" title="59 Year">59 Year</option>
                            <option value="60" title="60 Year">60 Year</option>
                            <option value="61" title="61 Year">61 Year</option>
                            <option value="62" title="62 Year">62 Year</option>
                            <option value="63" title="63 Year">63 Year</option>
                            <option value="64" title="64 Year">64 Year</option>
                            <option value="65" title="65 Year">65 Year</option>
                          </select>
                        </div>
                      </div>
                      <div
                        class="form-group col-md-3 col-sm-6 col-xs-12 no-padding land-religion">
                        <div class="left">
                          <select
                            name="religion[]"
                            id="Religion"
                            class="custom-select sources"
                            style="display: none">
                            <option
                              class="list"
                              value=""
                              selected=""
                              title="Select Religion">
                              Doesn't matter
                            </option>
                            <option value="Islam" title="Muslim">Muslim</option>
                            <option value="Sikh" title="Sikh">Sikh</option>
                            <option value="Hindu" title="Hindu">Hindu</option>
                            <option value="Christian" title="Christian">
                              Christian
                            </option>
                            <option value="Qadiyani" title="Qadiyani">
                              Qadiyani
                            </option>
                          </select>
                        </div>
                      </div>
                      <div
                        class="form-group col-md-2 col-sm-12 col-xs-12 no-padding land-search pad-r-0"
                        style="box-shadow: none">
                        <div class="left">
                          <!--onclick="find_match()"-->
                          <button
                            type="submit"
                            class="searchnow"
                            id="submit-btn">
                            Search
                          </button>
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- =========== Search for your right partner End ============ -->

    <!-- how does it work -->
    <div class="container">
      <div class="how-does-it-work">
        <div class="row">
          <div class="col-md-12 col-xs-12 col-sm-12 text-center">
            <div class="indian-matri-title">
              <p>How <span>Does</span> It Work ?</p>
            </div>
          </div>
        </div>

        <div class="row how-does-it-work-main">
          <div class="col-md-3 col-sm-12 col-xs-12 text-center">
            <div class="how-does-it-work-data">
              <div class="how-does-it-work-new">
                <img src="<?= BASE_URL ?>/assets/images/hw-1.png" alt="Create Account" />
              </div>

              <h3>Create Account</h3>

              <p>Sign up with us, it is free.</p>
            </div>
          </div>
          <div class="col-md-3 col-sm-12 col-xs-12 text-center">
            <div class="how-does-it-work-data">
              <div class="how-does-it-work-new">
                <img src="<?= BASE_URL ?>/assets/images/hw-2.png" alt="Browse Profiles" />
              </div>
              <h3>Browse Profiles</h3>
              <p>Browse profiles of your choice and shortlist them.</p>
            </div>
          </div>
          <div class="col-md-3 col-sm-12 col-xs-12 text-center">
            <div class="how-does-it-work-data">
              <div class="how-does-it-work-new">
                <img src="<?= BASE_URL ?>/assets/images/hw-3.png" alt="Connect" />
              </div>
              <h3>Connect</h3>

              <p>
                Our team will contact another person and arrange your meeting.
              </p>
            </div>
          </div>

          <div class="col-md-3 col-sm-12 col-xs-12 text-center">
            <div class="how-does-it-work-data">
              <div class="how-does-it-work-new">
                <img src="<?= BASE_URL ?>/assets/images/hw-4.png" alt="Interact" />
              </div>
              <h3>Interact</h3>

              <p>Interact with your potential spouse and make your decision.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- how does it work end -->

    <!-- Last Added Profiles -->

    <div class="last-added-profiles">
      <div class="container">
        <div class="row">
          <div class="col-md-12 col-xs-12 col-sm-12 text-center">
            <div class="indian-matri-title">
              <p>Last <span>Added</span> Profiles</p>
            </div>
          </div>
        </div>
        <?php
        $recentProfiles = $recentProfiles ?? [];
        $lastImgStyle = 'border: 3px solid #0d010e; box-shadow: 0px 1px 10px 0px rgb(4 4 4 / 29%);';
        $demoCards = [
            ['name' => 'Shaheer Ahmad Test', 'line' => '27 Years, Muslim', 'img' => BASE_URL . '/assets/images/male.png'],
            ['name' => 'Dr Binte Farooq', 'line' => '31 Years, Muslim, Lahore, Pakistan', 'img' => BASE_URL . '/assets/images/female.png'],
            ['name' => 'Muhsin latif', 'line' => '26 Years, Muslim, Pakistan', 'img' => BASE_URL . '/assets/images/male.png'],
            ['name' => 'Mr Haris', 'line' => '30 Years, Muslim, Lahore, Pakistan', 'img' => BASE_URL . '/assets/images/male.png'],
        ];
        ?>
        <div class="row margin-top-20">
          <?php if (!empty($recentProfiles)): ?>
            <?php foreach (array_slice($recentProfiles, 0, 4) as $rp): ?>
              <?php
              $rid = (int)($rp['id'] ?? 0);
              $rname = trim(($rp['first_name'] ?? '') . ' ' . ($rp['second_name'] ?? ''));
              $rage = '-';
              if (!empty($rp['dob']) && $rp['dob'] !== '0000-00-00') {
                try {
                  $rage = (new DateTime())->diff(new DateTime($rp['dob']))->y;
                } catch (Exception $e) {
                  $rage = '-';
                }
              }
              $rimg = !empty($rp['photo_path'])
                ? BASE_URL . '/' . ltrim((string)$rp['photo_path'], '/')
                : BASE_URL . '/assets/images/male.png';
              $rline = $rage !== '-' ? $rage . ' Years' : '';
              if (!empty($rp['religion'])) {
                $rline .= ($rline !== '' ? ', ' : '') . $rp['religion'];
              }
              if (!empty($rp['city'])) {
                $rline .= ($rline !== '' ? ', ' : '') . $rp['city'];
              }
              if ($rline === '') {
                $rline = 'Member';
              }
              $rhref = BASE_URL . '/profile/' . $rid;
              ?>
              <div class="col-md-3 col-sm-12 col-xs-12">
                <div class="option-3-data margin-top-10">
                  <a href="<?= htmlspecialchars($rhref, ENT_QUOTES, 'UTF-8') ?>">
                    <img src="<?= htmlspecialchars($rimg, ENT_QUOTES, 'UTF-8') ?>"
                      title="<?= htmlspecialchars($rname, ENT_QUOTES, 'UTF-8') ?>"
                      alt=""
                      class="last-img"
                      style="<?= $lastImgStyle ?>" /></a>
                  <div class="option-3-data-pad margin-top-10">
                    <h4><?= htmlspecialchars($rname ?: 'Member', ENT_QUOTES, 'UTF-8') ?></h4>
                    <p><?= htmlspecialchars($rline, ENT_QUOTES, 'UTF-8') ?></p>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <?php foreach ($demoCards as $d): ?>
              <div class="col-md-3 col-sm-12 col-xs-12">
                <div class="option-3-data margin-top-10">
                  <a href="<?= BASE_URL ?>/login">
                    <img src="<?= htmlspecialchars($d['img'], ENT_QUOTES, 'UTF-8') ?>"
                      title="<?= htmlspecialchars($d['name'], ENT_QUOTES, 'UTF-8') ?>"
                      alt=""
                      class="last-img"
                      style="<?= $lastImgStyle ?>" /></a>
                  <div class="option-3-data-pad margin-top-10">
                    <h4><?= htmlspecialchars($d['name'], ENT_QUOTES, 'UTF-8') ?></h4>
                    <p><?= htmlspecialchars($d['line'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p class="small text-muted">Sample profile — <a href="<?= BASE_URL ?>/login">Register</a> to see real matches</p>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <!-- Last Added Profiles -->

    <!-- Are you trying our planning tools -->
    <div class="planning-tools">
      <div class="container">
        <div class="row m-center">
          <div class="col-md-7 col-sm-12 col-xs-12">
            <h3></h3>

            <p></p>

            <a href="/login" class="btn get-started-btn">Get Started</a>
          </div>
          <div class="col-md-1 col-sm-12 col-xs-12"></div>
          <div class="col-md-4 col-sm-12 col-xs-12">
            <img src="<?= BASE_URL ?>/assets/images/planning-tools.png" alt="Planning Tools" />
          </div>
        </div>
      </div>
    </div>
    <!-- Are you trying our planning tools end -->

    <!-- features -->
    <div class="features">
      <div class="container">
        <div class="row text-center">
          <div class="col-md-3 col-sm-12 col-xs-12">
            <h4>57+ Languages</h4>

            <h5>Offering Multilingual Choices</h5>
          </div>

          <div class="col-md-3 col-sm-12 col-xs-12">
            <h4>586+ Castes</h4>

            <h5>Within Pakistan & Abroad</h5>
          </div>

          <div class="col-md-3 col-sm-12 col-xs-12">
            <h4>3200+ Cities</h4>
            <h5>Across 4 countries of operation</h5>
          </div>
          <div class="col-md-3 col-sm-12 col-xs-12">
            <h4>250 Countries</h4>
            <h5>Connecting beyond borders</h5>
          </div>
        </div>
      </div>
    </div>
    <!-- features end -->

    <!-- ========= Stay connected with our app Start ========== -->
    <!--<div class="stay-connected">-->
    <!--  <svg-->
    <!--    xmlns="http://www.w3.org/2000/svg"-->
    <!--    width="671.887"-->
    <!--    height="508.029"-->
    <!--    viewBox="0 0 671.887 508.029"-->
    <!--    style="position: absolute; right: 0"-->
    <!--  >-->
    <!--    <defs></defs>-->
    <!--    <path-->
    <!--      class="a"-->
    <!--      d="M-207.887,508.029,464,0V508H0Z"-->
    <!--      transform="translate(207.887)"-->
    <!--    ></path>-->
    <!--  </svg>-->
    <!--  <div class="container">-->
    <!--    <div class="row margin-top-0">-->
    <!--      <div class="col-md-6 col-xs-12 col-sm-6">-->
    <!--        <div class="stay-connected-title">-->
    <!--          <p><span>Mobile</span> App</p>-->
    <!--        </div>-->

    <!--        <div class="stay-connected-title-dec">-->
    <!--          <p class=""></p>-->
    <!--        </div>-->

    <!--        <ul class="mobile-app-feact margin-top-25">-->
    <!--          <li>-->
    <!--            <div class="mobile-app-feact-new">-->
    <!--              <img-->
    <!--                src="<?= BASE_URL ?>/assets/images/hw-1.png"-->
    <!--                alt="Create Account"-->
    <!--                data-toggle="tooltip"-->
    <!--                data-placement="top"-->
    <!--                title="Create Account"-->
    <!--              />-->
    <!--            </div>-->
    <!--          </li>-->
    <!--          <li>-->
    <!--            <div class="mobile-app-feact-new">-->
    <!--              <img src="<?= BASE_URL ?>/assets/images/hw-2.png"-->
    <!--                alt="Browse Profiles"-->
    <!--                data-toggle="tooltip"-->
    <!--                data-placement="top"-->
    <!--                title="Browse Profiles"-->
    <!--              />-->
    <!--            </div>-->
    <!--          </li>-->
    <!--          <li>-->
    <!--            <div class="mobile-app-feact-new">-->
    <!--              <img src="<?= BASE_URL ?>/assets/images/hw-3.png"-->
    <!--                alt="Connect"-->
    <!--                data-toggle="tooltip"-->
    <!--                data-placement="top"-->
    <!--                title="Connect"-->
    <!--              />-->
    <!--            </div>-->
    <!--          </li>-->
    <!--          <li>-->
    <!--            <div class="mobile-app-feact-new">-->
    <!--              <img src="<?= BASE_URL ?>/assets/images/hw-4.png"-->
    <!--                alt="Interact"-->
    <!--                data-toggle="tooltip"-->
    <!--                data-placement="top"-->
    <!--                title="Interact"-->
    <!--              />-->
    <!--            </div>-->
    <!--          </li>-->
    <!--        </ul>-->

    <!--        <ul class="mobile-app-feact-1 margin-top-20">-->
    <!--          <li>-->
    <!--            <img src="<?= BASE_URL ?>/assets/images/check.png" alt="Check Icon" /> Search by-->
    <!--            location, community, profession & more.-->
    <!--          </li>-->
    <!--          <li>-->
    <!--            <img src="<?= BASE_URL ?>/assets/images/check.png" alt="Check Icon" /> Verified stamp-->
    <!--            added to profile.-->
    <!--          </li>-->
    <!--          <li>-->
    <!--            <img src="<?= BASE_URL ?>/assets/images/check.png" alt="Check Icon" /> Profile and-->
    <!--            pictures with advanced privacy settings.-->
    <!--          </li>-->
    <!--        </ul>-->
    <!--       <img src="<?= BASE_URL ?>/assets/images/mobile-app.png"-->
    <!--          class="img-responsive hidden-lg hidden-md"-->
    <!--          alt="Mobile App"-->
    <!--        />-->

    <!-- <hr class="hidden-lg hidden-md hr-mobile-new"> -->

    <!-- mobile app icon for desktop -->

    <!--        <div class="row margin-top-20 hidden-sm hidden-xs">-->
    <!--          <div class="col-md-4 col-sm-12 col-xs-12 text-center">-->
    <!--            <div class="mobile-app-box">-->
    <!--              <div class="row">-->
    <!--                <div-->
    <!--                  class="col-md-6 col-sm-6 col-xs-6 mobile-app-right-border"-->
    <!--                >-->
    <!--                  <a target="_blank" href="https://www.google.co.pk/ios"-->
    <!--                    ><img-->
    <!--                      src="<?= BASE_URL ?>/assets/images/app-store.png"-->
    <!--                      alt="App Store"-->
    <!--                      data-toggle="tooltip"-->
    <!--                      data-placement="bottom"-->
    <!--                      title="App Store"-->
    <!--                  /></a>-->
    <!--                </div>-->
    <!--                <div class="col-md-6 col-sm-6 col-xs-6">-->
    <!--                  <a-->
    <!--                    target="_blank"-->
    <!--                    href="https://play.google.com/store/apps/"-->
    <!--                    > <img src="<?= BASE_URL ?>/assets/images/google-play-store.png"-->
    <!--                      alt="Google Play Store"-->
    <!--                      data-toggle="tooltip"-->
    <!--                      data-placement="bottom"-->
    <!--                      title="Google Play Store"-->
    <!--                  /></a>-->
    <!--                </div>-->
    <!--              </div>-->
    <!--            </div>-->
    <!--          </div>-->
    <!--        </div>-->

    <!-- mobile app icon for desktop -->

    <!-- mobile app icon for mobile -->

    <!--        <div class="row margin-top-20 hidden-lg hidden-md">-->
    <!--          <div class="col-md-3 col-sm-3 col-xs-3"></div>-->

    <!--          <div class="col-md-6 col-sm-6 col-xs-6">-->
    <!--            <div class="mobile-app-box">-->
    <!--              <div class="row">-->
    <!--                <div-->
    <!--                  class="col-md-6 col-sm-6 col-xs-6 mobile-app-right-border"-->
    <!--                >-->
    <!--                  <a target="_blank" href="https://www.google.co.pk/ios"-->
    <!--                    > <img src="<?= BASE_URL ?>/assets/images/app-store.png" alt="App Store"-->
    <!--                  /></a>-->
    <!--                </div>-->
    <!--                <div class="col-md-6 col-sm-6 col-xs-6">-->
    <!--                  <a-->
    <!--                    target="_blank"-->
    <!--                    href="https://play.google.com/store/apps/"-->
    <!--                    ><img-->
    <!--                      src="<?= BASE_URL ?>/assets/images/google-play-store.png"-->
    <!--                      alt="Google Play Store"-->
    <!--                  /></a>-->
    <!--                </div>-->
    <!--              </div>-->
    <!--            </div>-->
    <!--          </div>-->
    <!--          <div class="col-md-3 col-sm-3 col-xs-3"></div>-->
    <!--        </div>-->
    <!-- mobile app icon for mobile -->
    <!--      </div>-->

    <!--      <div class="col-md-1 col-xs-12 col-sm-12"></div>-->
    <!--      <div class="col-md-5 col-xs-12 col-sm-6">-->
    <!--        <div class="mobile-app-img">-->
    <!--         <img src="<?= BASE_URL ?>/assets/images/mobile-app.png"-->
    <!--            class="img-responsive hidden-sm hidden-xs"-->
    <!--            alt=""-->
    <!--          />-->
    <!--        </div>-->
    <!--      </div>-->
    <!--    </div>-->
    <!--  </div>-->
    <!--</div>-->
    <!-- ========== Stay connected with our app End =========== -->

    <style>
      #video-frame-section {
        width: 90%;
        /* reduced overall width */
        margin: 30px auto;
      }

      #video-frame-section .video-wrapper {
        position: relative;
        width: 100%;
        padding-bottom: 50%;
        /* reduced height (less than 56.25%) */
        height: 0;
        overflow: hidden;
        border-radius: 20px;
      }

      #video-frame-section .video-wrapper iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: none;
        border-radius: 20px;
      }
    </style>

    <section class="py-5">
      <div class="container">

        <div class="row justify-content-center mb-5">
          <div class="col-lg-12 col-md-10">
            <section id="video-frame-section">
              <div class="video-wrapper">
                <iframe
                  src="https://www.youtube.com/embed/LH1h1xmQk1A?controls=0&modestbranding=1&rel=0&showinfo=0"
                  title="Wedding Wish Marriage Center"
                  allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                  allowfullscreen>
                </iframe>
              </div>
            </section>
          </div>
        </div>



        <!-- Facebook Groups -->

        <h3 class="text-center mb-4">
          <i class="fa-solid fa-users"></i> Join Our Facebook Groups
        </h3>

        <div class="row text-center g-3">

          <div class="col-lg-3 col-md-6 col-6">
            <a href="https://www.facebook.com/share/g/1DoNmANW1k/" target="_blank" class="btn btn-primary w-100">
              <i class="fa-brands fa-facebook"></i> Group 1
            </a>
          </div>

          <div class="col-lg-3 col-md-6 col-6">
            <a href="https://www.facebook.com/share/g/1DyzyeyTRi/" target="_blank" class="btn btn-primary w-100">
              <i class="fa-brands fa-facebook"></i> Group 2
            </a>
          </div>

          <div class="col-lg-3 col-md-6 col-6">
            <a href="https://www.facebook.com/share/g/1DgJhSx6TH/" target="_blank" class="btn btn-primary w-100">
              <i class="fa-brands fa-facebook"></i> Group 3
            </a>
          </div>

          <div class="col-lg-3 col-md-6 col-6">
            <a href="https://www.facebook.com/share/g/1AxUfnzgRk/" target="_blank" class="btn btn-primary w-100">
              <i class="fa-brands fa-facebook"></i> Group 4
            </a>
          </div>

        </div>
        <h3 class="text-center mb-4">
          <i class="fa-solid fa-users"></i> Join Our Facebook Page
        </h3>
        <div class="col-lg-3 col-md-6 col-6">
          <a href="https://www.facebook.com/share/1CkAPN4uR5/" target="_blank" class="btn btn-primary w-100">
            <i class="fa-brands fa-facebook"></i> Follow us facebook
          </a>
        </div>
      </div>

    </section>



    <style>
      .stories-section {
        padding: 70px 0;
        background: #efd8a8;
      }

      .stories-title {
        text-align: center;
        font-size: 42px;
        font-weight: 700;
        color: #7b001c;
        /* royal red */
        margin-bottom: 50px;
      }

      .story-card {
        background: #7b001c;
        /* royal red */
        border: 5px solid #7b001c;
        overflow: hidden;
        height: 100%;
      }

      .story-card img {
        width: 100%;
        height: 250px;
        object-fit: cover;
      }

      .story-content {
        padding: 25px;
        color: #fff;
      }

      .story-content h4 {
        font-size: 30px;
        font-weight: 700;
        margin-bottom: 20px;
      }

      .story-content p {
        font-size: 20px;
        line-height: 1.8;
        margin-bottom: 30px;
      }

      .read-btn {
        background: #fff;
        color: #4a004f;
        padding: 12px 24px;
        text-decoration: none;
        display: inline-block;
        font-size: 18px;
        border-radius: 2px;
        font-weight: 500;
      }

      .read-btn:hover {
        background: #f5f5f5;
        color: #4a004f;
      }

      .read-btn i {
        margin-right: 8px;
      }

      @media (max-width: 991px) {
        .stories-title {
          font-size: 32px;
        }

        .story-content h4 {
          font-size: 24px;
        }

        .story-content p {
          font-size: 17px;
        }
      }
    </style>


    <section class="stories-section">
      <div class="container">
        <h2 class="stories-title">Sweet Stories From Our Lovers</h2>

        <div class="row g-4">

          <!-- Card 1 -->
          <div class="col-lg-4 col-md-6">
            <div class="story-card">
              <img src="https://www.shadihub.pk/redesign_frontend/assets/images/story/01.jpg" alt="Bilal and Sana Iram">

              <div class="story-content">
                <h4>Bilal and Sana Iram</h4>

                <p>
                  Marriage ends with the life of sharing,
                  caring, and endless love for each other.
                  Thanks to the ShadiHub team. It was a
                  great experience and given a good
                  opportunity in finding my life partner
                </p>

                <a href="<?= BASE_URL ?>/sucess1" class="read-btn">
                  ➤ Read More
                </a>
              </div>
            </div>
          </div>

          <!-- Card 2 -->
          <div class="col-lg-4 col-md-6">
            <div class="story-card">
              <img src="https://www.shadihub.pk/redesign_frontend/assets/images/story/02.jpg" alt="Ahmad and Aimal">

              <div class="story-content">
                <h4>Ahmad and Aimal</h4>

                <p>
                  Marriage ends with the life of sharing,
                  caring, and endless love for each other.
                  Thanks to the ShadiHub team. It was a
                  great experience and given a good
                  opportunity in finding my life partner
                </p>

                <a href="<?= BASE_URL ?>/sucess2" class="read-btn">
                  ➤ Read More
                </a>
              </div>
            </div>
          </div>

          <!-- Card 3 -->
          <div class="col-lg-4 col-md-6">
            <div class="story-card">
              <img src="https://www.shadihub.pk/redesign_frontend/assets/images/story/03.jpg" alt="Mohsin and Misbah">

              <div class="story-content">
                <h4>Mohsin and Misbah</h4>

                <p>
                  Marriage ends with the life of sharing,
                  caring, and endless love for each other.
                  Thanks to the ShadiHub team. It was a
                  great experience and given a good
                  opportunity in finding my life partner
                </p>

                <a href="<?= BASE_URL ?>/sucess3" class="read-btn">
                  ➤ Read More
                </a>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>


    <!--<section class="py-5">-->

    <!--<div class="container">-->

    <!--<h3 class="text-center mb-5">-->
    <!--<i class="fa-solid fa-shield-halved"></i> Trusted & Registered-->
    <!--</h3>-->

    <!--<div class="row justify-content-center align-items-center text-center g-2 trust-badges">-->

    <!--<div class="col-lg-1 col-md-4 col-6">-->
    <!--<img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQwjFdctQ2pf1uXyr8p9jW17pY4g3_s7CNgEQ&s"-->
    <!--alt="Trusted Badge" class="img-fluid trust-logo">-->
    <!--</div>-->

    <!--<div class="col-lg-1 col-md-4 col-6">-->
    <!--<img src="https://upload.wikimedia.org/wikipedia/en/0/0a/SECP_logo_new.png"-->
    <!--alt="SECP Registered" class="img-fluid trust-logo">-->
    <!--</div>-->

    <!--<div class="col-lg-1 col-md-4 col-6">-->
    <!--<img src="https://crystalpng.com/wp-content/uploads/2025/04/fbr-logo-1.png"-->
    <!--alt="FBR Registered" class="img-fluid trust-logo">-->
    <!--</div>-->

    <!--</div>-->

    <!--</div>-->

    <!--</section>-->


    <style>
      /* TRUST LOGO STYLE */

      .trust-logo {
        max-height: 80px;
        width: auto;
        filter: grayscale(100%) brightness(0.9);
        transition: 0.3s;
      }

      /* HOVER EFFECT */

      .trust-logo:hover {
        filter: none;
        transform: scale(1.05);
      }

      /* MOBILE SIZE */

      @media (max-width:768px) {

        .trust-logo {
          max-height: 60px;
        }

      }

      @media (max-width:480px) {

        .trust-logo {
          max-height: 50px;
        }

      }
    </style>



    <!-- Why Choose us -->
    <div class="why-choose-us">
      <div class="container">
        <div class="row">
          <div class="col-md-12 col-xs-12 col-sm-12 m-center">
            <div class="indian-matri-title1">
              <h2>Why <span>Choose</span> us</h2>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="why-choose-us-data">
              <p>
                One of Pakistan's best-known brands which operates globally was
                founded with a simple objective - to help people to complete
                their half Deen. The company pioneered online matrimonial
                service in 2016 and continues to lead the exciting matrimony
                category. Our dedicated teams help you to achieve your desired
                partner.
              </p>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="why-choose-us-data">
            <div
              class="col-md-6 col-sm-12 col-xs-12 why-choose-us-right-border">
              <h3>Our Matchmaking mechanism</h3>
              <p>
                After logging into our website, and short-list matches of your
                choice, our assigned consultants will communicate with other
                parties and will arrange your meeting. However, it depends upon
                your selection criteria. the wise you choose, the earlier will
                be t
              </p>
              <div class="margin-top-20">
                <h3>How to choose marriage Bureaus</h3>
                <p>
                  marriage Bureaus having the following criteria should be
                  chosen for service: 1. Working website and updated database 2.
                  Registered 3. 5 years + service history 4. Reasonable profiles
                  of your city, caste, and religion .
                </p>
              </div>
            </div>
            <div class="col-md-6 col-sm-12 col-xs-12">
              <h3>Selection criteria of spouse in Islam</h3>
              <p>
                A man who marries a woman for wealth, Allah leaves him in his
                condition, and if marries her (only) for her beauty, he will
                find in her (things) which he dislikes. If marries her for her
                faith (religiousness), Allah will gather up all these things for
                him.
              </p>
              <div class="margin-top-20">
                <h3>One of the main cause of divorce in Pakistan</h3>
                <p>
                  The main battle between spouses is to decide, "who is the king
                  of the house" Whose decision will be accepted when husband and
                  wife argue about something? Nobody wants to withdraw from
                  her/his point of view
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Why Choose us end-->

    <!-- =========== Browse Matrimony Start =========== -->
    <?php
    $homeBrowseQ = static function (string $term): string {
        return BASE_URL . '/search?q=' . rawurlencode($term);
    };
    ?>
    <div class="browse-matri">
      <div class="container">
        <div class="row">
          <div class="col-md-6 col-sm-12 col-xs-12 margin-top-20">
            <div class="browse-matri-data">
              <div class="col-md-2 col-sm-3 col-xs-3 bm-pad-l">
                <div class="browse-matri-data-new">
                  <img src="<?= BASE_URL ?>/assets/images/bm-1.png" alt="Religion" />
                </div>
              </div>
              <div class="col-md-10 col-sm-9 col-xs-9 bm-pad-l">
                <h3>Religion</h3>
                <ul class="browse-matri-list">
                  <li>
                    <?php foreach (['Hindu', 'Muslim', 'Christian', 'Qadiyani', 'Sikh'] as $i => $t): ?>
                      <?php if ($i > 0): ?><div class="bm-vl"></div><?php endif; ?>
                      <a href="<?= htmlspecialchars($homeBrowseQ($t), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($t, ENT_QUOTES, 'UTF-8') ?></a>
                    <?php endforeach; ?>
                    <div class="bm-vl"></div>
                    <a href="<?= BASE_URL ?>/search">More Details</a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-sm-12 col-xs-12 margin-top-20">
            <div class="browse-matri-data">
              <div class="col-md-2 col-sm-3 col-xs-3 bm-pad-l">
                <div class="browse-matri-data-new">
                  <img src="<?= BASE_URL ?>/assets/images/bm-1.png" alt="Sect" />
                </div>
              </div>
              <div class="col-md-10 col-sm-9 col-xs-9 bm-pad-l">
                <h3>Sect</h3>
                <ul class="browse-matri-list">
                  <li>
                    <a href="<?= htmlspecialchars($homeBrowseQ('Sunni'), ENT_QUOTES, 'UTF-8') ?>">Sunni</a>
                    <div class="bm-vl"></div>
                    <a href="<?= htmlspecialchars($homeBrowseQ('Shia'), ENT_QUOTES, 'UTF-8') ?>">Shia</a>
                    <div class="bm-vl"></div>
                    <a href="<?= BASE_URL ?>/search">More Details</a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-sm-12 col-xs-12 margin-top-20">
            <div class="browse-matri-data">
              <div class="col-md-2 col-sm-3 col-xs-3 bm-pad-l">
                <div class="browse-matri-data-new">
                  <img src="<?= BASE_URL ?>/assets/images/bm-4.png" alt="Caste" />
                </div>
              </div>
              <div class="col-md-10 col-sm-9 col-xs-9 bm-pad-l">
                <h3>Caste</h3>
                <ul class="browse-matri-list">
                  <li>
                    <?php
                    $castes = ['Sheikh', 'Arain', 'Rajput', 'Jutt', 'Syed', 'Mughal', 'Kashmiri'];
                    foreach ($castes as $i => $t):
                    ?>
                      <?php if ($i > 0): ?><div class="bm-vl"></div><?php endif; ?>
                      <a href="<?= htmlspecialchars($homeBrowseQ($t), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($t, ENT_QUOTES, 'UTF-8') ?></a>
                    <?php endforeach; ?>
                    <div class="bm-vl"></div>
                    <a href="<?= BASE_URL ?>/search">More Details</a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-sm-12 col-xs-12 margin-top-20">
            <div class="browse-matri-data">
              <div class="col-md-2 col-sm-3 col-xs-3 bm-pad-l">
                <div class="browse-matri-data-new">
                  <img src="<?= BASE_URL ?>/assets/images/bm-2.png" alt="Mother Tongue" />
                </div>
              </div>
              <div class="col-md-10 col-sm-9 col-xs-9 bm-pad-l">
                <h3>Mother Tongue</h3>
                <ul class="browse-matri-list">
                  <li>
                    <?php foreach (['Urdu', 'Punjabi', 'English'] as $i => $t): ?>
                      <?php if ($i > 0): ?><div class="bm-vl"></div><?php endif; ?>
                      <a href="<?= htmlspecialchars($homeBrowseQ($t), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($t, ENT_QUOTES, 'UTF-8') ?></a>
                    <?php endforeach; ?>
                    <div class="bm-vl"></div>
                    <a href="<?= BASE_URL ?>/search">More Details</a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-sm-12 col-xs-12 margin-top-20">
            <div class="browse-matri-data">
              <div class="col-md-2 col-sm-3 col-xs-3 bm-pad-l">
                <div class="browse-matri-data-new">
                  <img src="<?= BASE_URL ?>/assets/images/bm-5.png" alt="Country" />
                </div>
              </div>
              <div class="col-md-10 col-sm-9 col-xs-9 bm-pad-l">
                <h3>Country</h3>
                <ul class="browse-matri-list">
                  <li>
                    <?php
                    $countries = ['Pakistan', 'United Kingdom', 'Australia', 'Canada', 'Saudi Arabia', 'United Arab Emirates', 'Oman', 'Bahrain', 'India'];
                    foreach ($countries as $i => $t):
                    ?>
                      <?php if ($i > 0): ?><div class="bm-vl"></div><?php endif; ?>
                      <a href="<?= htmlspecialchars($homeBrowseQ($t), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($t, ENT_QUOTES, 'UTF-8') ?></a>
                    <?php endforeach; ?>
                    <div class="bm-vl"></div>
                    <a href="<?= BASE_URL ?>/search">More Details</a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-sm-12 col-xs-12 margin-top-20">
            <div class="browse-matri-data">
              <div class="col-md-2 col-sm-3 col-xs-3 bm-pad-l">
                <div class="browse-matri-data-new">
                  <img src="<?= BASE_URL ?>/assets/images/bm-3.png" alt="State" />
                </div>
              </div>
              <div class="col-md-10 col-sm-9 col-xs-9 bm-pad-l">
                <h3>State</h3>
                <ul class="browse-matri-list">
                  <li>
                    <?php
                    $states = ['Sindh', 'Islamabad Capital Territory', 'Azad Kashmir', 'Punjab', 'Khyber Pakhtunkhwa'];
                    foreach ($states as $i => $t):
                    ?>
                      <?php if ($i > 0): ?><div class="bm-vl"></div><?php endif; ?>
                      <a href="<?= htmlspecialchars($homeBrowseQ($t), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($t, ENT_QUOTES, 'UTF-8') ?></a>
                    <?php endforeach; ?>
                    <div class="bm-vl"></div>
                    <a href="<?= BASE_URL ?>/search">More Details</a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-sm-12 col-xs-12 margin-top-20">
            <div class="browse-matri-data">
              <div class="col-md-2 col-sm-3 col-xs-3 bm-pad-l">
                <div class="browse-matri-data-new">
                  <img src="<?= BASE_URL ?>/assets/images/bm-6.png" alt="Cities" />
                </div>
              </div>
              <div class="col-md-10 col-sm-9 col-xs-9 bm-pad-l">
                <h3>Cities</h3>
                <ul class="browse-matri-list">
                  <li>
                    <?php
                    $cities = ['Lahore', 'Karachi', 'Islamabad', 'Rawalpindi', 'Gujranwala', 'Sialkot', 'Multan', 'Sahiwal'];
                    foreach ($cities as $i => $t):
                    ?>
                      <?php if ($i > 0): ?><div class="bm-vl"></div><?php endif; ?>
                      <a class="font-12" href="<?= htmlspecialchars($homeBrowseQ($t), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($t, ENT_QUOTES, 'UTF-8') ?></a>
                    <?php endforeach; ?>
                    <div class="bm-vl"></div>
                    <a href="<?= BASE_URL ?>/search">More Details</a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- =========== Browse Matrimony End =========== -->

    <!--write something modal start-->
    <div
      id="myModal505"
      class="modal fade"
      tabindex="-1"
      role="dialog"
      aria-labelledby="myModalLabel"
      aria-hidden="true">
      <div class="modal-dialog modal-dialog-vendor">
        <div class="modal-content">
          <div class="modal-header new-header-modal">
            <p class="Poppins-Bold mega-n3 new-event text-center">
              Terms and <span class="mega-n4 f-s">Conditions </span>
            </p>
            <button
              type="button"
              class="close close-vendor"
              data-dismiss="modal"
              aria-hidden="true"
              style="margin-top: -37px !important">
              ×
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                <div class="no-data-f">
                  <img
                    src="<?= BASE_URL ?>/assets/images/no-data.png"
                    class="img-responsive no-data"
                    style="margin: auto"
                    alt="No data" />
                  <h1 class="color-no">
                    <span class="Poppins-Bold color-no">NO</span> DATA
                    <span class="Poppins-Bold color-no"> FOUND </span>
                  </h1>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--write something modal End-->

    <script>
      document.addEventListener("DOMContentLoaded", function () {
        var phoneEl = document.querySelector("#phone_input");
        if (!phoneEl || typeof window.intlTelInput !== "function") {
          return;
        }

        var iti = window.intlTelInput(phoneEl, {
          initialCountry: "auto",
          separateDialCode: true,
          autoPlaceholder: "polite",
          nationalMode: false,
          preferredCountries: ["pk", "us", "gb"],
          utilsScript: "<?= BASE_URL ?>/assets/js/utils.js",
          geoIpLookup: function (cb) {
            cb("PK");
          },
        });

        function syncPhoneFromIti() {
          var num = iti.getNumber();
          var mob = document.getElementById("mobile_number");
          var cc = document.getElementById("country_code");
          if (mob) {
            mob.value = num;
          }
          var iso = "";
          try {
            var d = iti.getSelectedCountryData();
            iso = d && d.iso2 ? String(d.iso2).toUpperCase() : "";
          } catch (e) {}
          if (cc) {
            cc.value = iso;
          }
          var ph = document.querySelector('input[name="phone"]');
          if (!ph) {
            ph = document.createElement("input");
            ph.type = "hidden";
            ph.name = "phone";
            var rf = document.getElementById("register_step1");
            if (rf) {
              rf.appendChild(ph);
            }
          }
          if (ph) {
            ph.value = num;
          }
        }

        var regForm = document.getElementById("register_step1");
        if (regForm) {
          regForm.addEventListener("submit", function () {
            syncPhoneFromIti();
          });
        }

        var loginForm = document.querySelector("#login_form");
        if (loginForm) {
          loginForm.addEventListener("submit", syncPhoneFromIti);
        }
      });
    </script>
    <!-- <script src="<?= BASE_URL ?>/assets/js/jquery.min.js"></script> -->
    <div
      id="myModal_disclaimer"
      class="modal fade"
      tabindex="-1"
      data-keyboard="false"
      data-backdrop="static"
      role="dialog"
      aria-labelledby="myModal_disclaimer"
      aria-hidden="true">
      <div class="modal-dialog modal-dialog-vendor">
        <div class="modal-content">
          <div
            class="modal-header new-header-modal"
            style="border-bottom: 1px solid #e5e5e5">
            <p class="Poppins-Bold mega-n3 new-event text-center">
              Before You <span class="mega-n4 f-s">Sign Up</span>
            </p>
            <button
              type="button"
              class="close close-vendor"
              data-dismiss="modal"
              aria-hidden="true"
              style="margin-top: -37px !important">
              ×
            </button>
          </div>
          <div class="modal-body">
            <div class="">
              <h3 class="Poppins-Medium">Fraud & Fake Profiles:</h3>
              <p>
                Creating a fake profile to scam people is a serious crime under
                the new cyber law of Pakistan and can land you in jail. Your IP
                address will be captured by this website and provided to the
                Federal Investigation Authority (FIA) in case of any fraud or
                scam. Please do not create fake profiles to pursue legal action
                in cases of fraud or scams.
              </p>
              <h3 class="Poppins-Medium">Do Not Use Proxy:</h3>
              <p>
                We will not activate your account if you are using the internet
                behind a proxy or any kind of hidden IP software.
              </p>
              <h3 class="Poppins-Medium">Be Careful:</h3>
              <p>
                You cannot change your email and gender after signing up.
                However, you can choose to hide or disclose your picture.
              </p>
            </div>
            <div class="row mt-3 text-right">
              <div class="disclaimer_btns">
                <button
                  class="btn btn-danger mr-2"
                  type="button"
                  id="dis_false">
                  <b>I din't want to Register</b>
                </button>
                <button
                  class="btn btn-success mr-2"
                  type="button"
                  id="dis_true">
                  <b>I Understand and agree With Terms</b>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script>
      // $(document).ready(function () {
      // $("#register").click(function (event) {
      //   // prevent form submit
      //   event.preventDefault();
      //   $("#myModal_disclaimer").modal("show");
      // });

      // if (window.location.href.endsWith("/register")) {
      //   $("#myModal_disclaimer").modal("show");
      // }

      // $("#dis_true").click(function (event) {
      //   // prevent form submit
      //   event.preventDefault();
      //   $("#myModal_disclaimer").modal("hide");
      //   if (window.location.href.endsWith("/register")) {
      //   } else {
      //     // submit the form
      //     $("#register_step1").submit();
      //   }
      // });

      //   $("#dis_false").click(function (event) {
      //     // prevent form submit
      //     event.preventDefault();
      //     $("#myModal_disclaimer").modal("hide");

      //     // redirect to the home page
      //     window.location.href = "./";
      //   });
      // });
    </script>