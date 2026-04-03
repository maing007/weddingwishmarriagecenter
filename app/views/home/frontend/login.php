<?php if (!empty($error)): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
    </div>
    
<?php session_start();

endif; ?>

<div class="login-reg-main">
  <div class="container">
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12 margin-top-40">
        <div class="row">
          <div class="col-md-3 col-sm-12 col-xs-12"></div>

          <div class="col-md-6 col-sm-12 col-xs-12">
            <div class="reg-login-box">
              <h1>
                <p class="Poppins-Semi-Bold f-22 color-31 text-center">
                  LOG<span class="color-d">IN</span>
                </p>
              </h1>

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

              <form
                action="<?= BASE_URL ?>/login"
                method="post"
                id="login_form"
                name="login_form"
              >
                <div class="reg-box pb-3">
                  <div class="row-cstm">
                    <div class="reg-input">
                      <input
                        type="text"
                        class="form-control reg_input"
                        required
                        name="username"
                        id="username"
                        placeholder="Enter your Email ID or Matri ID"
                        value="<?= isset($old['username']) ? htmlspecialchars($old['username'], ENT_QUOTES, 'UTF-8') : '' ?>"
                      />
                    </div>
                  </div>

                  <div class="row-cstm">
                    <div class="reg-input">
                      <input
                        type="password"
                        class="form-control reg_input"
                        required
                        name="password"
                        id="password"
                        placeholder="Enter Password"
                      />
                    </div>
                  </div>

                  <!-- Captcha -->
                  <!-- <div class="row">
                    <div class="reg-input">
                      <div class="col-md-3 col-sm-3 col-xs-6" id="captcha_login">
                        <img
                          src="<?= BASE_URL ?>/assets/images/captcha.php"
                          style="border-radius: 6px"
                          alt="Captcha"
                        />
                      </div>
                      <div class="col-md-2 col-sm-2 col-xs-6">
                        <a
                          title="Change Captcha Code"
                          href="javascript:;"
                          onclick="location.reload();"
                        >
                          <i
                            title="Change Captcha Code"
                            class="fa fa-refresh fa-1 curser_icon"
                          ></i>
                        </a>
                      </div>
                      <div class="col-md-7 col-sm-7 col-xs-12">
                       <input
  required
  type="text"
  name="code_captcha"
  id="code_captcha"
  class="form-control reg_input"
  placeholder="Enter Captcha"
  value=""
/>

                      </div>
                    </div>
                  </div> -->

                  <div class="row pull-right">
                    <div class="reg-input">
                      <div class="col-md-12 col-sm-12 col-xs-12">
                        <p class="Poppins-Regular color-83 f-13">
                          <!-- Optional: forgot password route later -->
                          <a href="#">
                            <span class="color-d Poppins-Medium">
                              Forgot Password ?
                            </span>
                          </a>
                        </p>
                      </div>
                    </div>
                  </div>

                  <div class="row-cstm pt-4">
                    <div class="e-t2 text-center">
                      <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>"
                      />
                      <input
                        type="submit"
                        class="Poppins-Medium btn reg-btn f-17 color-f e-3_m"
                        value="Login"
                      />
                    </div>
                  </div>
                </div>
              </form>

              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="row-cstm text-center">
                  <div class="col-md-12 col-sm-12 col-xs-12">
                    <p class="Poppins-Regular color-83 f-13 reg-footer_r">
                      New Member?
                      <a href="<?= BASE_URL ?>/">
                        <span class="color-80 Poppins-Medium">
                          Register Free
                        </span>
                      </a>
                    </p>
                  </div>
                </div>
              </div>

            </div> <!-- /.reg-login-box -->
          </div>   <!-- /.col-md-6 -->

        </div>
      </div>
    </div>
  </div>
</div>
