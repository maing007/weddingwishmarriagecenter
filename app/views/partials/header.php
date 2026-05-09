<?php
if (!isset($title)) {
    $title = 'Wedding Wish Marriage Centre';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
    
    <!-- Responsive -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />

    <!-- SEO Meta -->
    <meta name="robots" content="index, follow" />
    <meta
      name="description"
      content="Wedding Wish Marriage Centre is a Pakistani matrimonial service helping parents and individuals find the best match quickly and affordably. We follow Islamic principles to ensure meaningful and successful connections."
    />
    <meta
      name="keywords"
      content="matrimonial sites in Pakistan, Pakistan matchmaking service, Pakistan brides, Pakistan grooms, Pakistani marriage site, Islamic matchmaking"
    />

    <!-- Open Graph / Social Sharing -->
    <meta property="og:title" content="Wedding Wish Marriage Centre - Trusted Matrimonial Service in Pakistan" />
    <meta
      property="og:description"
      content="Wedding Wish Marriage Centre helps individuals and families in Pakistan find suitable matches quickly, affordably, and following Islamic principles."
    />
    <meta
      property="og:image"
      content="<?= BASE_URL ?>/assets/ogimg/a3f69b1a1d9d61938560d6afeb6d9a77.jpg"
    />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?= BASE_URL ?>" />

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="Wedding Wish Marriage Centre - Trusted Matrimonial Service in Pakistan" />
    <meta name="twitter:description" content="Helping families and individuals find suitable matches quickly and affordably according to Islamic principles." />
    <meta name="twitter:image" content="<?= BASE_URL ?>/assets/ogimg/a3f69b1a1d9d61938560d6afeb6d9a77.jpg" />

    <!-- Favicon (optional) -->
    <link rel="icon" href="<?= BASE_URL ?>/assets/images/logo.png" type="image/x-icon" />
    <!-- Font Awesome CDN -->
    <link
      rel="preload"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
      as="style"
      onload="this.rel='stylesheet'"
    />
    <noscript>
      <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
      />
    </noscript>

    <!-- Favicon -->
    <link
      type="image/x-icon"
      rel="shortcut icon"
      href="<?= BASE_URL ?>/assets/logo/e38d2d6749a51c77f33f4320b6eb15b5.png"
    />
    <!-- ====== CSS from /public/assets/css ====== -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/bootstrap.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/all.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/font-awesome.min.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/responsive.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/jquery.mCustomScrollbar.min.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/owl.carousel.min.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/owl.theme.min.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/notification_popup.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/mega2.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/chosen.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/select2.min.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/intlTelInput.css" />
        <link href="<?= BASE_URL ?>/assets/css/style_1.css" rel="stylesheet" />
    <link href="<?= BASE_URL ?>/assets/css/responsive_1.css" rel="stylesheet" />
    <link href="<?= BASE_URL ?>/assets/css/mohammad.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/fontello.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/payment-style.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/payment-responsive.css" />

    <style>
    ._logo{
        display: flex;
    }
      ._logo a img {
        width: auto;
        height: 62px;
  filter: brightness(-3);

      }
      .iti {
        position: relative;
        display: block;
        width: 100%;
        margin: 0 auto 20px auto;
      }
      /* Optional helper for bg */
      .mobile-app-rc-bg {
        background-image: url('<?= BASE_URL ?>/assets/images/mobile-app-rc-bg.png');
      }
      /* Trust logos container */
.trust-logo {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-left: 220px;
}

/* Individual logo images */
.trust-logo img {
  height: 42px;
  width: auto;
  object-fit: contain;
  filter: grayscale(0%) contrast(1.05);
  transition: all 0.3s ease;
}

/* Hover effect */
.trust-logo img:hover {
  transform: scale(1.05);
  filter: brightness(1.05);
}

/* Hide on tablet + mobile */
@media (max-width: 991px) {
  .trust-logo {
    display: none !important;
  }
}
    </style>

    <!-- =================== JS LIBRARIES =================== -->

    <!-- jQuery (local) -->
    <script src="<?= BASE_URL ?>/assets/js/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>
<!-- Custom Scrollbar JS (IMPORTANT) -->
<!-- <script src="<?= BASE_URL ?>/assets/js/jquery.mCustomScrollbar.concat.min.js"></script> -->
    <!-- Bootstrap JS (CDN – fixes $(...).modal error) -->
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/jquery.mCustomScrollbar.concat.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/owl.carousel.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/intlTelInput.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/utils.js"></script>
<script src="<?= BASE_URL ?>/assets/js/js.cookie.min.js"></script>


    <!--===========================FreiChat START=========================-->
    <input type="hidden" id="hidd_plan_status" value="" />
    <!--=======================FreiChatX END=====================-->

    <!-- =================== COMMON JS =================== -->
    <script>
      // Smooth scroll for .scroll links
      jQuery(function ($) {
        $(".scroll").on("click", function (event) {
          event.preventDefault();
          $("html,body").animate(
            { scrollTop: $(this.hash).offset().top },
            1000
          );
        });
      });

      // Scroll back to top circular progress (if .progress-wrap exists)
      (function ($) {
        "use strict";
        $(function () {
          var progressPath = document.querySelector(".progress-wrap path");
          if (!progressPath) return;

          var pathLength = progressPath.getTotalLength();
          progressPath.style.transition = progressPath.style.WebkitTransition = "none";
          progressPath.style.strokeDasharray = pathLength + " " + pathLength;
          progressPath.style.strokeDashoffset = pathLength;
          progressPath.getBoundingClientRect();
          progressPath.style.transition = progressPath.style.WebkitTransition =
            "stroke-dashoffset 10ms linear";

          var updateProgress = function () {
            var scroll = $(window).scrollTop();
            var height = $(document).height() - $(window).height();
            var progress = pathLength - (scroll * pathLength) / height;
            progressPath.style.strokeDashoffset = progress;
          };

          updateProgress();
          $(window).on("scroll", updateProgress);

          var offset = 50;
          var duration = 550;

          $(window).on("scroll", function () {
            if ($(this).scrollTop() > offset) {
              $(".progress-wrap").addClass("active-progress");
            } else {
              $(".progress-wrap").removeClass("active-progress");
            }
          });

          $(".progress-wrap").on("click", function (event) {
            event.preventDefault();
            $("html, body").animate({ scrollTop: 0 }, duration);
            return false;
          });
        });
      })(jQuery);

      // Sticky header
      window.addEventListener("load", function () {
        var header = document.getElementById("StickyHeader");
        if (!header) return;

        var sticky = header.offsetTop;

        function handleStickyHeader() {
          if (window.pageYOffset > sticky) {
            header.classList.add("sticky");
          } else {
            header.classList.remove("sticky");
          }
        }

        window.addEventListener("scroll", handleStickyHeader);
      });

      // All jQuery plugin initialisation in ONE safe block
      jQuery(function ($) {
        // Bootstrap tooltips
        // if ($.fn.tooltip) {
        //   $('[data-toggle="tooltip"]').tooltip();
        // } else {
        //   console.warn("Bootstrap tooltip plugin not loaded.");
        // }

        // Select2
        if ($.fn.select2) {
          $("#religion,#caste,#birth_date,#birth_month,#birth_year").select2();
        } else {
          console.warn("Select2 plugin not loaded.");
        }

        // OwlCarousel sliders (only if plugin is loaded)
        if ($.fn.owlCarousel) {
          if ($("#testimonial-slider").length) {
            $("#testimonial-slider").owlCarousel({
              items: 1,
              itemsDesktop: [1000, 1],
              itemsDesktopSmall: [979, 1],
              itemsTablet: [768, 1],
              itemsMobile: [650, 1],
              pagination: false,
              navigation: false,
              slideSpeed: 1000,
              autoPlay: true
            });
          }

          if ($("#testimonial-slider-2").length) {
            $("#testimonial-slider-2").owlCarousel({
              items: 1,
              itemsDesktop: [1000, 1],
              itemsDesktopSmall: [979, 1],
              itemsTablet: [768, 1],
              pagination: true,
              navigation: false,
              navigationText: ["", ""],
              autoPlay: 3000,
              stopOnHover: true
            });
          }

          if ($("#testimonial-slider-3").length) {
            $("#testimonial-slider-3").owlCarousel({
              items: 2,
              dots: false,
              itemsDesktop: [1000, 1],
              itemsDesktopSmall: [979, 1],
              itemsTablet: [768, 1],
              pagination: true,
              navigation: false,
              nav: true,
              navigationText: false,
              autoPlay: true,
              autoplayTimeout: 1520,
              smartSpeed: 1500,
              animateIn: "linear",
              animateOut: "linear"
            });
          }
        } else {
          console.warn("OwlCarousel plugin not loaded.");
        }
      });

      // Gender toggle helper
      function add_gender_class(id) {
        if (id === "male") {
          jQuery("#male_id").addClass("color-d Poppins-Medium");
          jQuery("#female_id").removeClass("color-d Poppins-Medium");
          jQuery("#gender").val("Male");
        } else {
          jQuery("#male_id").removeClass("color-d Poppins-Medium");
          jQuery("#female_id").addClass("color-d Poppins-Medium");
          jQuery("#gender").val("Female");
        }
      }

      // STUBS for old inline handlers so errors na aaye
      function dropdownChange(sourceId, targetId, listName) {
        // TODO: agar real logic chahiye ho to yahan add kar lena
        return true;
      }

      function month_year_change() {
        // TODO: yahan DOB validate kar sakte ho agar zarurat ho
        return true;
      }

      // Home search (agar use ho raha hai)
      function find_match() {
        var $ = jQuery;
        var hash_tocken_id = $("#hash_tocken_id").val();
        var base_url = $("#base_url").val();
        if (!base_url) return false;

        var url = base_url + "search/home_search";
        var form_data = $("#search_form").serialize();
        form_data = form_data + "&csrf_new_matrimonial=" + hash_tocken_id;

        if (typeof show_comm_mask === "function") {
          show_comm_mask();
        }

        $.ajax({
          url: url,
          type: "POST",
          data: form_data,
          dataType: "json",
          success: function (data) {
            window.location.href = base_url + "search/result";
            if (typeof update_tocken === "function") {
              update_tocken(data.tocken);
            }
            if (typeof hide_comm_mask === "function") {
              hide_comm_mask();
            }
          }
        });
        return false;
      }

      // Custom select UI (home search bar)
      jQuery(function ($) {
        $(".custom-select").each(function () {
          var $this = $(this),
              classes = $this.attr("class"),
              id = $this.attr("id"),
              placeholder = $this.attr("placeholder");

          if ($this.find(":selected").attr("title")) {
            placeholder = $this.find(":selected").attr("title");
          }
          if (placeholder === "Bride") {
            placeholder = "Looking For a " + placeholder;
          }

          var template = '<div class="' + classes + '">';
          template += '<span class="custom-select-trigger" id="' + id + '_change">' + placeholder + '</span>';
          template += '<div class="custom-options">';
          $this.find("option").each(function () {
            template += '<span class="custom-option" data-value="' + $(this).attr("value") + '">' + $(this).html() + "</span>";
          });
          template += "</div></div>";

          $this.wrap('<div class="custom-select-wrapper"></div>');
          $this.hide();
          $this.after(template);
        });

        $(".custom-option:first-of-type").hover(
          function () {
            $(this).parents(".custom-options").addClass("option-hover");
          },
          function () {
            $(this).parents(".custom-options").removeClass("option-hover");
          }
        );

        $(".custom-select-trigger").on("click", function (event) {
          $("html").one("click", function () {
            $(".custom-select").removeClass("opened");
            $(".custom-select-trigger").removeClass("open");
          });
          if ($(".open").length) {
            $(".custom-select").removeClass("opened");
            $(".custom-select-trigger").removeClass("open");
          } else {
            $(this).parents(".custom-select").toggleClass("opened");
            $(".custom-select-trigger").addClass("open");
          }
          event.stopPropagation();
        });

        $(".custom-option").on("click", function () {
          var $option = $(this);
          $option.parents(".custom-select-wrapper").find("select").val($option.data("value"));
          $option.parents(".custom-options").find(".custom-option").removeClass("selection");
          $option.addClass("selection");
          $option.parents(".custom-select").removeClass("opened");
          $option.parents(".custom-select").find(".custom-select-trigger").text($option.text());

          if ($option.data("value") == "Male") {
            $("#agefrom").val("24");
            $("#ageto").val("35");
            $("#agefrom_change").text("24 Year");
            $("#ageto_change").text("35 Year");
            $("#Looking_change").text("Looking For a Groom");
          } else if ($option.data("value") == "Female") {
            $("#agefrom").val("20");
            $("#ageto").val("30");
            $("#agefrom_change").text("20 Year");
            $("#ageto_change").text("30 Year");
            $("#Looking_change").text("Looking For a Bride");
          }
        });
      });

      // Captcha change helper
      function change_captcha_code(captcha_div_id, captcha_session) {
        var $ = jQuery;
        var base_url = $("#base_url").val();
        if (!base_url) return;

        var action = base_url + "login/change_captcha";
        var hash_tocken_id = $("#hash_tocken_id").val();

        if (typeof show_comm_mask === "function") {
          show_comm_mask();
        }

        $.ajax({
          url: action,
          type: "post",
          data: {
            csrf_new_matrimonial: hash_tocken_id,
            captcha_session: captcha_session
          },
          success: function (data) {
            $("#" + captcha_div_id).html(data);
            $("#code_captcha").val("");
            if (typeof hide_comm_mask === "function") {
              hide_comm_mask();
            }
          }
        });
      }

      // Cookie policy popup
      function cookiesPolicyPrompt() {
        if (typeof Cookies === "undefined") return;

        if (Cookies.get("acceptedCookiesPolicy") == "yes") {
          jQuery("#alertCookiePolicy").hide();
        }
        jQuery("#btnAcceptCookiePolicy").on("click", function () {
          Cookies.set("acceptedCookiesPolicy", "yes", { expires: 30 });
          jQuery("#alertCookiePolicy").hide();
        });
        jQuery("#btnDeclineCookiePolicy").on("click", function () {
          jQuery("#alertCookiePolicy").fadeOut("slow");
        });
      }

      jQuery(function () {
        cookiesPolicyPrompt();
        // For testing reset cookie (if element exists)
        jQuery("#btnResetCookiePolicy").on("click", function () {
          if (typeof Cookies !== "undefined") {
            Cookies.remove("acceptedCookiesPolicy");
            jQuery("#alertCookiePolicy").show();
          }
        });
      });
    </script>
  </head>

  <body
    class="overflow-x-h"
    style="
      background: url('<?= BASE_URL ?>/assets/images/site-back-img.png');
      background-color: whitesmoke;
    "
  >
    <div class="_banner">
      <!-- ====== Nav Bar ====== -->
      <div class="menu-position">
        <!-- ======- Desktop Menu bar ====== -->
        <div class="_navBarMani hidden-sm hidden-xs" id="StickyHeader">
          <div class="container-fluid cust_padding">
            <nav class="clearfix">
              <div class="_logo">
                <a href="<?= BASE_URL ?>/">
                  <img src="<?= BASE_URL ?>/assets/images/logo.png" alt="Logo" width="100" />
                </a>
                <div class="trust-logo">
<img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQwjFdctQ2pf1uXyr8p9jW17pY4g3_s7CNgEQ&s"
alt="Trusted Badge" class="img-fluid">

<img src="https://upload.wikimedia.org/wikipedia/en/0/0a/SECP_logo_new.png"
alt="SECP Registered" class="img-fluid">

<img src="https://crystalpng.com/wp-content/uploads/2025/04/fbr-logo-1.png"
alt="FBR Registered" class="img-fluid">
<h5>
    Trusted By Government Of Pakistan
</h5>
</div>
                </div>
                


              
              <div class="_hamburge">
                <ul class="clearfix">
                  <li class="menu-active cool-link">
                    <a href="<?= BASE_URL ?>/">Home</a>
                  </li>
                      <!-- <li class="clearfix">
                  <a href="#homeSubmenu" data-toggle="collapse" aria-expanded="false">
                    Search<i class="fas fa-angle-down"></i>
                  </a>
                  <ul class="collapse list-unstyled ul-li-bg" id="homeSubmenu">
                    <li><a href="<?= BASE_URL ?>/search">Quick Search</a></li>
                    <li><a href="<?= BASE_URL ?>/search">Advance Search</a></li>
                    <li><a href="<?= BASE_URL ?>/search">Keyword Search</a></li>
                    <li><a href="<?= BASE_URL ?>/search">Id Search</a></li>
                  </ul>
                </li> -->
                
                <!-- aria-haspopup="true"
                      aria-expanded="false" -->
                  <li class="nav-item dropdown">
                    <a
                      class="nav-link dropdown-toggle"
                      href="#"
                      id="navbarDropdown6"
                      role="button"
                      data-toggle="dropdown"
                     aria-haspopup="true"
                      aria-expanded="false"
                    >
                      Search
                      <span class="caret"></span>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown6">
                      <a class="dropdown-item" href="<?= BASE_URL ?>/search">Quick Search</a>
                      <a class="dropdown-item" href="<?= BASE_URL ?>/search">Advance Search</a>
                      <a class="dropdown-item" href="<?= BASE_URL ?>/search">Keyword Search</a>
                      <a class="dropdown-item" href="<?= BASE_URL ?>/search">Id Search</a>
                    </div>
                  </li>
                  <li><a href="<?= BASE_URL ?>/member">Membership</a></li>
                  <!-- <li><a href="#">Success Stories</a></li> -->
                  <li><a href="<?= BASE_URL ?>/contact">Contact Us</a></li>
                  <li><a href="<?= BASE_URL ?>/carees">Careers</a></li>
                  <li>
                    <a href="<?= BASE_URL ?>/register" class="last-child-menu">Register</a>
                  </li>
                  <li>
                    <a href="<?= BASE_URL ?>/login" class="last-child-menu">
                      <i class="fas fa-sign-in-alt"></i> Log In
                    </a>
                  </li>
                </ul>
              </div>
            </nav>
          </div>
        </div>

        <!-- ====== Mobile Menu bar Start ====== -->
        <div class="MobileMenu hidden-lg hidden-md clearfix ps-Sticky">
          <div class="_menuOpenMobile">
            <nav id="sidebar">
              <div id="dismiss">
                <i class="fas fa-times"></i>
              </div>

              <ul class="list-unstyled components">
                <li><a href="<?= BASE_URL ?>/">Home</a></li>
                <li><a href="<?= BASE_URL ?>/">Register Now</a></li>
                <li class="clearfix">
                  <a href="#homeSubmenu" data-toggle="collapse" aria-expanded="false">
                    Search<i class="fas fa-angle-down"></i>
                  </a>
                  <ul class="collapse list-unstyled ul-li-bg" id="homeSubmenu">
                    <li><a href="<?= BASE_URL ?>/search">Quick Search</a></li>
                    <li><a href="<?= BASE_URL ?>/search">Advance Search</a></li>
                    <li><a href="<?= BASE_URL ?>/search">Keyword Search</a></li>
                    <li><a href="<?= BASE_URL ?>/search">Id Search</a></li>
                  </ul>
                </li>
                <li><a href="<?= BASE_URL ?>/member">Membership</a></li>
                  <li><a href="<?= BASE_URL ?>/carees">Careers</a></li>
                <li><a href="<?= BASE_URL ?>/contact">Contact Us</a></li>
                <li><a href="<?= BASE_URL ?>/login">Log In</a></li>
              </ul>
            </nav>
            <button type="button" id="sidebarCollapse" class="newHamburge">
              <i class="fas fa-bars"></i>
            </button>
          </div>
        </div>
        <!-- ====== Mobile Menu bar End ====== -->
      </div>
    </div>
    <!-- From here your page-specific content (Home, Register, Login, etc.) start hota hai -->
    <script>
      // mobile menu start
      $(document).ready(function () {
        $("#sidebar").mCustomScrollbar({
          theme: "minimal",
        });

        $("#dismiss, .overlay").on("click", function () {
          $("#sidebar").removeClass("active");
          $(".overlay").removeClass("active");
        });

        $("#sidebarCollapse").on("click", function () {
          $("#sidebar").addClass("active");
          $(".overlay").addClass("active");
          $(".collapse.in").toggleClass("in");
          $("a[aria-expanded=true]").attr("aria-expanded", "false");
        });
      });
      

document.addEventListener('DOMContentLoaded', function () {

  var toggles = document.querySelectorAll('.dropdown-toggle');

  toggles.forEach(function (toggle) {
    toggle.addEventListener('click', function (e) {
      e.preventDefault();

      var dropdown = this.closest('.dropdown');
      if (!dropdown) return;

      var menu = dropdown.querySelector('.dropdown-menu');
      if (!menu) return;

      // Toggle display manually
      if (menu.style.display === 'block') {
        menu.style.display = 'none';
      } else {
        menu.style.display = 'block';
      }
    });
  });

});
    </script>
 <!-- Tooltips -->
    <script>
      $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
      });
    </script>