 <!-- <div class="contact-tab"> -->
    <div class="container-fluid new-width">
      <div class="row">
        <div class="col-md-12">
          <div class="tab contact-tab-m" role="tabpanel">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs contact-tab-nav" role="tablist">
              <li role="presentation" class="active contact-tab-margin f-17">
                <a
                  href="#conatct-section"
                  aria-controls="conatct-section"
                  role="tab"
                  data-toggle="tab"
                >
                  <i class="fas fa-phone"></i>
                  Contact Us
                </a>
              </li>
              <li role="presentation" class="f-17">
                <a
                  href="#enquery-section"
                  aria-controls="enquery-section"
                  role="tab"
                  data-toggle="tab"
                >
                  <i class="fas fa-comment-dots icon-dot"></i>
                  Enquiry / Feedback</a
                >
              </li>
            </ul>
            <!-- Tab panes -->
          </div>
        </div>
      </div>
    </div>

    <div class="container new-width">
      <div class="mega-conatct-box-new mt-5 pb-5">
        <div class="tab-content tabs">
          <div
            role="tabpanel"
            class="tab-pane fade in active"
            id="conatct-section"
          >
            <p
              class="calibri-Bold-font f-22 color-31 c-tab-t2 hidden-sm hidden-xs"
            >
              Address
            </p>
            <p
              class="Poppins-Regular f-12 color-65 c-tab-t1 hidden-sm hidden-xs"
            >
              At Wedding Wish Marriage Center, we are always striving to better
              your experience.
            </p>

            <div class="address-map-box">
              <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="c1-add">
                     <p>Model Town Link Road Zainab Tower Office No. M75 Near Amana Mall</p>

                  <p>Call us:
                  <br>
                   
                  +92 322-6817540<br>
                    +92 309-7688394 <br>
                    +92 309-5996132

                  </p>


                  <p>Email: info@weddingwishmarriagecentre.com</p>
                </div>
              </div>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="n-map">
                  <!-- <div id="googleMap" class="map"></div> -->
                  <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d27219.505179071937!2d74.32392324999999!3d31.48463845!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x391904106691c4c7%3A0xfb24ddaf1e7bc6c2!2sModel%20Town%2C%20Lahore!5e0!3m2!1sen!2s!4v1765044906790!5m2!1sen!2s"
                    width="600"
                    height="450"
                    style="border: 0"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                  ></iframe>
                </div>
              </div>
            </div>
          </div>
          <div role="tabpanel" class="tab-pane fade" id="enquery-section">
            <h1 class="calibri-Bold-font f-22 color-31 ab-t1">
              All fields are <span class="color-d">mandatory</span>
            </h1>
            <p
              class="Poppins-Regular f-12 color-65 c-tab-t1 hidden-sm hidden-xs"
            >
              Please feel free to post your questions, comments and suggestions.
              We are eager to assist you and serve you better.
            </p>
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="mega-box-new add-box-cstm b-shadow-none padding-0">
                  <div class="m-add-box">
                    <div class="add-box-2">
                      <div
                        class="alert alert-success"
                        id="email_success_message"
                        style="display: none"
                      ></div>
                      <div
                        class="alert alert-danger"
                        id="email_error_message"
                        style="display: none"
                      ></div>
 <?php if (!empty($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <?= $_SESSION['success']; unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger">
        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

                     <form  method="post" action="<?= BASE_URL ?>/contact-submit"
  id="contact_form"
  name="contact_form"
>

                        <div class="row add-b-cstm">
                          <div class="col-md-4 col-sm-4 col-xs-12">
                            <p class="Poppins-Medium f-16 color-31 ad-name">
                              Name
                              <span class="f-16 select2-lbl-span">* </span>:
                            </p>
                          </div>
                          <div class="col-md-8 col-sm-8 col-xs-12">
                            <div class="add-input">
                              <input
                                type="text"
                                class="form-control ni-input"
                                placeholder="Enter Your Name"
                                id="name"
                                name="name"
                                required=""
                              />
                            </div>
                          </div>
                        </div>
                        <div class="row add-b-cstm mt-4">
                          <div class="col-md-4 col-sm-4 col-xs-12">
                            <p class="Poppins-Medium f-16 color-31 ad-name">
                              Email
                              <span class="f-16 select2-lbl-span">* </span>:
                            </p>
                          </div>
                          <div class="col-md-8 col-sm-8 col-xs-12">
                            <div class="add-input">
                              <input
                                type="email"
                                class="form-control ni-input"
                                placeholder="Enter Your Email"
                                name="email"
                                id="email"
                                required=""
                              />
                            </div>
                          </div>
                        </div>
                        <div class="row add-b-cstm mt-4">
                          <div class="col-md-3 col-sm-3 col-xs-12">
                            <p class="Poppins-Medium f-16 color-31 ad-name">
                              Contact No
                              <span class="f-16 select2-lbl-span">* </span>:
                            </p>
                          </div>
                          <div class="col-md-3 col-sm-3 col-xs-12">
                            <select
                              class="mdb-select md-form md-outline colorful-select dropdown-primary ni-input2 tst_box mn_12"
                              name="country_code"
                              id="country_code"
                            >
                              <option value="+1-242">+1-242</option>
                              <option value="+1-246">+1-246</option>
                              <option value="+1-264">+1-264</option>
                              <option value="+1-268">+1-268</option>
                              <option value="+1-284">+1-284</option>
                              <option value="+1-340">+1-340</option>
                              <option value="+1-345">+1-345</option>
                              <option value="+1-441">+1-441</option>
                              <option value="+1-473">+1-473</option>
                              <option value="+1-649">+1-649</option>
                              <option value="+1-664">+1-664</option>
                              <option value="+1-670">+1-670</option>
                              <option value="+1-671">+1-671</option>
                              <option value="+1-684">+1-684</option>
                              <option value="+1-758">+1-758</option>
                              <option value="+1-767">+1-767</option>
                              <option value="+1-784">+1-784</option>
                              <option value="+1-787" and="" 1-939="">
                                +1-787 and 1-939
                              </option>
                              <option value="+1-809" and="" 1-829="">
                                +1-809 and 1-829
                              </option>
                              <option value="+1-868">+1-868</option>
                              <option value="+1-869">+1-869</option>
                              <option value="+1-876">+1-876</option>
                              <option value="+358-18">+358-18</option>
                              <option value="+44-1481">+44-1481</option>
                              <option value="+44-1534">+44-1534</option>
                              <option value="+44-1624">+44-1624</option>
                              <option selected="" value="+92">+92</option>
                              <option value="0055">0055</option>
                              <option value="1">1</option>
                              <option value="1721">1721</option>
                              <option value="20">20</option>
                              <option value="211">211</option>
                              <option value="212">212</option>
                              <option value="213">213</option>
                              <option value="216">216</option>
                              <option value="218">218</option>
                              <option value="220">220</option>
                              <option value="221">221</option>
                              <option value="222">222</option>
                              <option value="223">223</option>
                              <option value="224">224</option>
                              <option value="225">225</option>
                              <option value="226">226</option>
                              <option value="227">227</option>
                              <option value="228">228</option>
                              <option value="229">229</option>
                              <option value="230">230</option>
                              <option value="231">231</option>
                              <option value="232">232</option>
                              <option value="233">233</option>
                              <option value="234">234</option>
                              <option value="235">235</option>
                              <option value="236">236</option>
                              <option value="237">237</option>
                              <option value="238">238</option>
                              <option value="239">239</option>
                              <option value="240">240</option>
                              <option value="241">241</option>
                              <option value="242">242</option>
                              <option value="243">243</option>
                              <option value="244">244</option>
                              <option value="245">245</option>
                              <option value="246">246</option>
                              <option value="248">248</option>
                              <option value="249">249</option>
                              <option value="250">250</option>
                              <option value="251">251</option>
                              <option value="252">252</option>
                              <option value="253">253</option>
                              <option value="254">254</option>
                              <option value="255">255</option>
                              <option value="256">256</option>
                              <option value="257">257</option>
                              <option value="258">258</option>
                              <option value="260">260</option>
                              <option value="261">261</option>
                              <option value="262">262</option>
                              <option value="263">263</option>
                              <option value="264">264</option>
                              <option value="265">265</option>
                              <option value="266">266</option>
                              <option value="267">267</option>
                              <option value="268">268</option>
                              <option value="269">269</option>
                              <option value="27">27</option>
                              <option value="290">290</option>
                              <option value="291">291</option>
                              <option value="297">297</option>
                              <option value="298">298</option>
                              <option value="299">299</option>
                              <option value="30">30</option>
                              <option value="31">31</option>
                              <option value="32">32</option>
                              <option value="33">33</option>
                              <option value="34">34</option>
                              <option value="350">350</option>
                              <option value="351">351</option>
                              <option value="352">352</option>
                              <option value="353">353</option>
                              <option value="354">354</option>
                              <option value="355">355</option>
                              <option value="356">356</option>
                              <option value="357">357</option>
                              <option value="358">358</option>
                              <option value="359">359</option>
                              <option value="36">36</option>
                              <option value="370">370</option>
                              <option value="371">371</option>
                              <option value="372">372</option>
                              <option value="373">373</option>
                              <option value="374">374</option>
                              <option value="375">375</option>
                              <option value="376">376</option>
                              <option value="377">377</option>
                              <option value="378">378</option>
                              <option value="379">379</option>
                              <option value="380">380</option>
                              <option value="381">381</option>
                              <option value="382">382</option>
                              <option value="383">383</option>
                              <option value="385">385</option>
                              <option value="386">386</option>
                              <option value="387">387</option>
                              <option value="389">389</option>
                              <option value="39">39</option>
                              <option value="40">40</option>
                              <option value="41">41</option>
                              <option value="420">420</option>
                              <option value="421">421</option>
                              <option value="423">423</option>
                              <option value="43">43</option>
                              <option value="44">44</option>
                              <option value="45">45</option>
                              <option value="46">46</option>
                              <option value="47">47</option>
                              <option value="48">48</option>
                              <option value="49">49</option>
                              <option value="500">500</option>
                              <option value="501">501</option>
                              <option value="502">502</option>
                              <option value="503">503</option>
                              <option value="504">504</option>
                              <option value="505">505</option>
                              <option value="506">506</option>
                              <option value="507">507</option>
                              <option value="508">508</option>
                              <option value="509">509</option>
                              <option value="51">51</option>
                              <option value="52">52</option>
                              <option value="53">53</option>
                              <option value="54">54</option>
                              <option value="55">55</option>
                              <option value="56">56</option>
                              <option value="57">57</option>
                              <option value="58">58</option>
                              <option value="590">590</option>
                              <option value="591">591</option>
                              <option value="592">592</option>
                              <option value="593">593</option>
                              <option value="594">594</option>
                              <option value="595">595</option>
                              <option value="596">596</option>
                              <option value="597">597</option>
                              <option value="598">598</option>
                              <option value="599">599</option>
                              <option value="60">60</option>
                              <option value="61">61</option>
                              <option value="62">62</option>
                              <option value="63">63</option>
                              <option value="64">64</option>
                              <option value="65">65</option>
                              <option value="66">66</option>
                              <option value="670">670</option>
                              <option value="672">672</option>
                              <option value="673">673</option>
                              <option value="674">674</option>
                              <option value="675">675</option>
                              <option value="676">676</option>
                              <option value="677">677</option>
                              <option value="678">678</option>
                              <option value="679">679</option>
                              <option value="680">680</option>
                              <option value="681">681</option>
                              <option value="682">682</option>
                              <option value="683">683</option>
                              <option value="685">685</option>
                              <option value="686">686</option>
                              <option value="687">687</option>
                              <option value="688">688</option>
                              <option value="689">689</option>
                              <option value="690">690</option>
                              <option value="691">691</option>
                              <option value="692">692</option>
                              <option value="7">7</option>
                              <option value="81">81</option>
                              <option value="82">82</option>
                              <option value="84">84</option>
                              <option value="850">850</option>
                              <option value="852">852</option>
                              <option value="853">853</option>
                              <option value="855">855</option>
                              <option value="856">856</option>
                              <option value="86">86</option>
                              <option value="870">870</option>
                              <option value="880">880</option>
                              <option value="886">886</option>
                              <option value="90">90</option>
                              <option value="91">91</option>
                              <option value="93">93</option>
                              <option value="94">94</option>
                              <option value="95">95</option>
                              <option value="960">960</option>
                              <option value="961">961</option>
                              <option value="962">962</option>
                              <option value="963">963</option>
                              <option value="964">964</option>
                              <option value="965">965</option>
                              <option value="966">966</option>
                              <option value="967">967</option>
                              <option value="968">968</option>
                              <option value="970">970</option>
                              <option value="971">971</option>
                              <option value="972">972</option>
                              <option value="973">973</option>
                              <option value="974">974</option>
                              <option value="975">975</option>
                              <option value="976">976</option>
                              <option value="977">977</option>
                              <option value="98">98</option>
                              <option value="992">992</option>
                              <option value="993">993</option>
                              <option value="994">994</option>
                              <option value="995">995</option>
                              <option value="996">996</option>
                              <option value="998">998</option>
                            </select>
                          </div>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="add-input">
                              <input
                                type="text"
                                class="form-control ni-input"
                                placeholder="Enter Your Contact Number"
                                id="phone"
                                name="phone"
                                required=""
                                minlength="7"
                                maxlength="13"
                              />
                            </div>
                          </div>
                        </div>
                        <div class="row add-b-cstm mt-4">
                          <div class="col-md-4 col-sm-4 col-xs-12">
                            <p class="Poppins-Medium f-16 color-31 ad-name">
                              Subject
                              <span class="f-16 select2-lbl-span">* </span>:
                            </p>
                          </div>
                          <div class="col-md-8 col-sm-8 col-xs-12">
                            <div class="add-input">
                              <input
                                type="text"
                                class="form-control ni-input"
                                name="subject"
                                id="subject"
                                required=""
                                placeholder="Enter Your Subject Related To"
                              />
                            </div>
                          </div>
                        </div>

                        <div class="row add-b-cstm mt-4">
                          <div class="col-md-4 col-sm-4 col-xs-12">
                            <p class="Poppins-Medium f-16 color-31 ad-name">
                              Feedback
                              <span class="f-16 select2-lbl-span">* </span>:
                            </p>
                          </div>
                          <div class="col-md-8 col-sm-8 col-xs-12">
                            <div class="add-input">
                              <textarea
                                class="form-control ni-input"
                                placeholder="Post your questions, comments and suggestions"
                                rows="8"
                                name="description"
                                id="description"
                                required=""
                              ></textarea>
                            </div>
                          </div>
                        </div>
                        <div class="row add-b-cstm mt-4">
                          <div class="col-md-3 col-sm-3 col-xs-12"></div>
                          <!-- <div
                            class="col-md-3 col-sm-3 col-xs-12 enquery_captcha"
                            style="right: 10px"
                          >
                                  <img src="<?= BASE_URL ?>/assets/images/captcha.php" alt="captcha code" />
                          </div>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="add-input">
                              <input
                                type="number"
                                name="code_captcha"
                                id="code_captcha"
                                class="form-control ni-input"
                                placeholder="Enter Captcha Code"
                                required=""
                              />
                            </div>
                          </div> -->
                        </div>
                        <div class="row add-b-cstm mt-5">
                          <div class="col-md-3 col-sm-3 col-xs-12"></div>
                          <div class="col-md-3 col-sm-3 col-xs-12">
                            <div class="add-ad-btn">
                              <h2>
                                <div class="Poppins-Medium color-f f-18">
                                  <button type="submit" class="btn reg-btn">
                                    Submit
                                  </button>
                                </div>
                              </h2>
                              <h2></h2>
                            </div>
                          </div>
                        </div>
                        <input
                          type="hidden"
                          name="is_post"
                          id="is_post"
                          value="1"
                        />
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    var tabLinks = document.querySelectorAll('.contact-tab-nav a[role="tab"]');
    var tabPanes = document.querySelectorAll('.tab-content .tab-pane');

    function activateTab(link) {
      var targetSelector = link.getAttribute('href');
      if (!targetSelector || !targetSelector.startsWith('#')) return;

      // Deactivate all tabs
      tabLinks.forEach(function (l) {
        if (l.parentElement) l.parentElement.classList.remove('active');
      });
      tabPanes.forEach(function (pane) {
        pane.classList.remove('active');
        pane.classList.remove('in');
      });

      // Activate current tab + pane
      if (link.parentElement) link.parentElement.classList.add('active');
      var pane = document.querySelector(targetSelector);
      if (pane) {
        pane.classList.add('active');
        // For fade effect
        setTimeout(function () {
          pane.classList.add('in');
        }, 10);
      }
    }

    // Attach click handlers
    tabLinks.forEach(function (link) {
      link.addEventListener('click', function (e) {
        e.preventDefault();
        activateTab(this);
      });
    });

    // Ensure initially active tab pane matches .active li
    var activeLi = document.querySelector('.contact-tab-nav li.active a[role="tab"]');
    if (activeLi) {
      activateTab(activeLi);
    }
  });
</script>
