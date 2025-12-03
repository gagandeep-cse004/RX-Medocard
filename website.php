<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sibling Insurance Facility – RxMedoCard</title>
  <link rel="icon" href="images/logo.ico" type="image/x-icon">
  <link rel="stylesheet" href="lib/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/site224e.css">
  <link rel="stylesheet" href="css/navbar-footer1287.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f9fafb;
    }
    .hero-section {
      background: linear-gradient(to right, #001eb6, #018dbbff);
      color: white;
      padding: 90px 30px;
      text-align: center;
    }
    .form-section {
      background: white;
      border-radius: 20px;
      padding: 40px;
      box-shadow: 0 0 50px rgba(0,0,0,0.1);
      max-width: 1090px;
      margin: 70px auto;
    }
    .form-section h2 {
      color: #000fb6;
      margin-bottom: 50px;
    }
    .btn-submit {
      background-color: #1d3a83ff;
      color: white;
      border: none;
      padding: 60px 60px;
      border-radius: 40px;
      font-weight: 600;
      transition: all 0.3s ease;
    }
    .btn-submit:hover {
      background-color: #152c77ff;
    }
    .benefits-section {
      padding: 50px 50px;
      text-align: center;
      background-color: #154285ff;
    }
    .benefits-section h3 {
      color: #0379b9ff;
      margin-bottom: 40px;
    }
    .benefit-card {
      background: white;
      border-radius: 15px;
      padding: 25px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      transition: transform 0.3s ease;
    }
    .benefit-card:hover {
      transform: translateY(-5px);
    }
    footer {
      background-color: #0077b6;
      color: white;
      text-align: left;
      padding: 40px 0;
      margin-top: 50px;
    }
    .footer-heading {
      margin-bottom: 12px;
      font-weight: 600;
      position: relative;
      display: inline-block;
      padding-bottom: 5px;
    }
    .footer-heading::after {
      content: "";
      position: absolute;
      left: 0;
      bottom: 0;
      width: 40px;
      height: 2px;
      background-color: #ffffff;
      border-radius: 2px;
    }
    .footer-links,
    .footer-contact {
      padding-left: 0;
      margin: 0;
      list-style: none;
    }
    .footer-links li,
    .footer-contact li {
      margin-bottom: 8px;
      line-height: 1.6;
    }
    .footer-links a,
    .footer-contact a {
      color: #e0e0e0;
      text-decoration: none;
      display: inline-block;
      transition: color 0.3s ease;
    }
    .footer-links a:hover,
    .footer-contact a:hover {
      color: #ffffff;
    }
    .footer-social {
      margin-top: 15px;
    }
    .footer-social a {
      color: #ffffff;
      margin-right: 12px;
      font-size: 18px;
      display: inline-block;
      transition: transform 0.3s ease;
    }
    .footer-social a:hover {
      transform: scale(1.2);
    }
    .footer-divider {
      border-color: rgba(255, 255, 255, 0.2);
      margin: 30px 0;
    }
    .footer-bottom {
      font-size: 14px;
      color: #ddd;
    }
  </style>
</head>

<body>

<header class="header-modern">
  <nav class="navbar navbar-expand-custom navbar-light navbar-modern">
    <div class="container-fluid px-4 d-flex justify-content-between align-items-center position-relative">
      <a class="navbar-brand me-4" href="index.html">
        <div class="logo-with-icon">
          <div class="logo-icon">
            <img src="images/logo.ico" alt="RX MEDO CARD Logo" class="logo-image">
          </div>
          <div class="logo-text-wrapper">
            <div class="logo-text">RX MEDO CARD</div>
            <div class="logo-subtitle">AN INITIATIVE OF RX MEDICAL TRUST</div>
          </div>
        </div>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <i class="fas fa-bars-staggered"></i>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <div class="mobile-menu-header d-md-none">
          <h5 class="mobile-menu-title">Menu</h5>
          <button class="mobile-menu-close" type="button" aria-label="Close menu">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <ul class="navbar-nav d-flex flex-row align-items-center">
          <li class="nav-item"><a class="nav-link" href="index.html">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="Offers.html">Card Details</a></li>
          <li class="nav-item"><a class="nav-link" href="Hospitals.html">Hospitals</a></li>
          <li class="nav-item"><a class="nav-link" href="OPD.html">OPD</a></li>
          <li class="nav-item"><a class="nav-link" href="Pharmacy.html">Pharmacy</a></li>
          <li class="nav-item"><a class="nav-link" href="Diagnostics.html">Diagnostics</a></li>
        </ul>
        <div class="ms-auto">
          <a class="nav-link get-card-btn" href="website.php" id="showLogin">
            <i class="fas fa-user d-md-none me-2"></i>Sign up
          </a>
        </div>
        <div class="ms-auto">
          <a class="nav-link get-card-btn" href="website.php">
            <i class="fas fa-id-card d-md-none me-2"></i> Get Your Card
          </a>
        </div>
      </div>
    </div>
  </nav>
</header>

<main class="container my-5">
  <!-- Hero Section -->
  <section class="hero-section text-center py-5">
    <h1>Welcome to RX MEDO CARD</h1>
    <p>Create your account or login to access exclusive healthcare benefits.</p>
  </section>

  <!-- ✅ Signup Form -->
  <section class="form-section" id="signupSection">
    <h2 class="mb-4 text-center">Signup Form</h2>
    <form method="POST" action="">
      Name: <input type="text" name="name" required><br><br>
      Phone: <input type="text" name="phone" placeholder="+91XXXXXXXXXX" required><br><br>
      Email: <input type="text" name="mail" placeholder="example@gmail.com" required><br><br>
      Password: <input type="text" name="pass" required><br><br>
      Aadhaar Card No: <input type="text" name="aadhaar_card_no" placeholder="12-digit Aadhaar" required><br><br>
      PAN Card No: <input type="text" name="pan_card_no" placeholder="PAN Card Number" required><br><br>
      <input type="submit" name="sb" value="Signup" class="btn-submit">
    </form>
    <p class="text-center mt-3">
      Already have an account?
      <a href="#" id="showLoginHere">Login here</a>
    </p>

    <?php 
      $con = mysqli_connect('localhost','root','','users');

      // ✅ Signup Logic
      if(isset($_POST['sb']))
      {
          $name = $_POST['name'];
          $phone = $_POST['phone'];
          $email = $_POST['mail'];
          $password = $_POST['pass'];
          $aadhaar = $_POST['aadhaar_card_no'];
          $pan = $_POST['pan_card_no'];

          $query = "INSERT INTO mydata(name, phone, email, password, aadhaar_card_no, pan_card_no) 
                    VALUES ('$name','$phone','$email','$password','$aadhaar','$pan')";
          
          $execute = mysqli_query($con, $query);

          if($execute){
              echo "<script>alert('🎉 You have signed up successfully. Welcome to RX MEDO CARD!');</script>";
          } else {
              echo "<script>alert('❌ Signup failed. Please try again.');</script>";
          }
      }
    ?>
  </section>

  <!-- ✅ Login Form (Initially Hidden) -->
  <section class="form-section" id="loginSection" style="display: none;">
    <h2 class="mb-4 text-center">Login</h2>
    <form method="POST" action="">
      Email: <input type="text" name="mail" required><br><br>
      Password: <input type="text" name="pass" required><br><br>
      Aadhaar Card No: <input type="text" name="aadhaar_card_no" placeholder="12-digit Aadhaar" required><br><br>
      PAN Card No: <input type="text" name="pan_card_no" placeholder="PAN Card Number" required><br><br>
      <input type="submit" name="login" value="Login" class="btn-submit">
    </form>
    <p class="text-center mt-3">
      <a href="forgot_password.html">Forgot Password?</a>
    </p>
    <p class="text-center mt-3">
      Don't have an account?
      <a href="#" id="showSignupHere">Signup here</a>
    </p>

    <?php 
      // ✅ Login Logic
      if(isset($_POST['login']))
      {
          $email = $_POST['mail'];
          $password = $_POST['pass'];

          $query = "SELECT * FROM mydata WHERE email='$email' AND password='$password'";
          $result = mysqli_query($con, $query);

          if(mysqli_num_rows($result) == 1){
              echo "<script>alert('✅ Login successful. Access granted to your RX MEDO CARD dashboard.');</script>";
              // Optional: redirect to dashboard or card form
              // echo "<script>window.location.href='dashboard.html';</script>";
          } else {
              echo "<script>alert('❌ Invalid credentials. Please try again.');</script>";
          }
      }
    ?>
  </section>
</main>


  <!-- Benefits Section -->
  <section class="benefits-section">
    <h3>Why Choose RxMedoCard?</h3>
    <div class="benefit-card">✔ Affordable sibling insurance</div>
    <div class="benefit-card">✔ Trusted hospital network</div>
    <div class="benefit-card">✔ Easy online access</div>
  </section>

  <!-- FAQ Section -->
<section class="faq-section py-5 bg-light">
  <div class="container">
    <h3 class="text-center mb-4">Frequently Asked Questions</h3>
    <div class="accordion" id="faqAccordion">
      <div class="accordion-item">
        <h2 class="accordion-header" id="faqOne">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
            How do I reset my password?
          </button>
        </h2>
        <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            You can reset your password by clicking on the “Forgot Password?” link on the login page. Enter your registered email or phone number, and set a new password instantly.
          </div>
        </div>
      </div>
      <div class="accordion-item">
        <h2 class="accordion-header" id="faqTwo">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
            Can I sign up without a phone number?
          </button>
        </h2>
        <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            No, a valid phone number is required during sign-up to ensure secure account recovery and verification.
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Footer Section -->
<footer class="footer-modern py-5">
  <div class="container">
    <div class="row">
      <div class="col-lg-4 mb-4 mb-lg-0">
        <div class="footer-logo">
          <div class="footer-logo-with-icon">
            <div class="footer-logo-icon">
              <img src="images/logo.ico" alt="RX MEDO CARD Logo" class="footer-logo-image">
            </div>
            <div class="footer-logo-text-wrapper">
              <div class="footer-logo-text">RX MEDO CARD</div>
              <div class="footer-logo-subtitle">AN INITIATIVE OF RX MEDICAL TRUST</div>
            </div>
          </div>
        </div>
        <p class="mt-3 footer-description">
          RX MEDO CARD is a healthcare discount program designed to make quality healthcare accessible and affordable for everyone.
        </p>
        <div class="tagline-footer">Your Health, Our Priority</div>
        <div class="mt-4">
          <a class="footer-cta-button" href="Membership.html">Get Your Card</a>
        </div>
      </div>
      <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
        <h5 class="footer-heading">Quick Links</h5>
        <ul class="footer-links">
          <li><a href="index.html">Home</a></li>
          <li><a href="Offers.html">Card Details</a></li>
          <li><a href="Hospitals.html">Hospitals</a></li>
          <li><a href="OPD.html">OPD</a></li>
          <li><a href="Pharmacy.html">Pharmacy</a></li>
          <li><a href="Diagnostics.html">Diagnostics</a></li>
        </ul>
      </div>
      <div class="col-lg-3 col-md-4 mb-4 mb-md-0">
        <h5 class="footer-heading">Our Services</h5>
        <ul class="footer-links">
          <li><a href="#">Family Physician</a></li>
          <li><a href="#">Specialist Consultations</a></li>
          <li><a href="Diagnostics.html">Diagnostic Tests</a></li>
          <li><a href="#">Hospital Packages</a></li>
          <li><a href="#">Senior Care</a></li>
        </ul>
      </div>
      <div class="col-lg-3 col-md-4">
        <h5 class="footer-heading">Contact Us</h5>
        <ul class="footer-contact">
          <li><a href="tel:+919999307517"><i class="bi bi-telephone me-2"></i> +91 9999307517</a></li>
          <li><a href="mailto:info@rxmedocard.com"><i class="bi bi-envelope me-2"></i> info@rxmedocard.com</a></li>
          <li><a href="#"><i class="bi bi-geo-alt me-2"></i> 123 Healthcare Avenue, Delhi, India</a></li>
        </ul>
        <div class="footer-social">
          <a href="#"><i class="bi bi-facebook"></i></a>
          <a href="#"><i class="bi bi-twitter"></i></a>
          <a href="#"><i class="bi bi-instagram"></i></a>
          <a href="#"><i class="bi bi-linkedin"></i></a>
        </div>
      </div>
    </div>
    <hr class="footer-divider">
    <div class="row footer-bottom">
      <div class="col-md-6 text-center text-md-start">
        <p>&copy; 2025 - <b>RX MEDO CARD</b>. All rights reserved.</p>
      </div>
      <div class="col-md-6 text-center text-md-end">
        <a href="/policies.html" class="me-3">Privacy Policy</a>
        <a href="/terms.html" class="me-3">Terms of Service</a>
      </div>
    </div>
  </div>
</footer>

<!-- Toggle Script -->
<script>
  document.getElementById("showLoginHere").addEventListener("click", function (e) {
    e.preventDefault();
    document.getElementById("signupSection").style.display = "none";
    document.getElementById("loginSection").style.display = "block";
  });

  document.getElementById("showSignupHere").addEventListener("click", function (e) {
    e.preventDefault();
    document.getElementById("loginSection").style.display = "none";
    document.getElementById("signupSection").style.display = "block";
  });
</script>

<!-- Styling -->
<style>
  .form-section {
    background-color: #f9f9f9;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
  }

  .form-section h2 {
    font-family: 'Poppins', sans-serif;
    font-weight: 600;
    color: #0077b6;
  }

  .form-section label {
    font-weight: 500;
  }

  .form-section .btn-submit {
    font-weight: 600;
    font-size: 1rem;
    padding: 20px 90px;
    border-radius: 8px;
    transition: all 0.3s ease;
    text-align: center;
    display: block;
    margin: 0 auto;
    background-color: #14126bff;
    color: white;
    border: none;
  }

  .form-section .btn-submit:hover {
    background-color: #005f87;
  }

  .faq-section {
    background-color: #f8f9fa;
    padding: 60px 20px;
  }

  .faq-section h3 {
    font-family: 'Poppins', sans-serif;
    font-weight: 600;
    color: #0077b6;
    margin-bottom: 40px;
    text-align: center;
  }

  .faq-section .accordion .accordion-item {
    background-color: #ffffff;
    border: 1px solid #dee2e6;
    border-radius: 12px;
    margin-bottom: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
  }

  .faq-section .accordion-button {
    font-family: 'Poppins', sans-serif;
    font-weight: 500;
    color: #0077b6;
    background-color: #e0f7fa;
    border: none;
    padding: 18px 20px;
    border-radius: 12px;
    text-align: left;
    transition: background-color 0.3s ease;
  }

  .faq-section .accordion-button:focus {
    box-shadow: none;
  }

  .faq-section .accordion-button:not(.collapsed) {
    background-color: #0077b6;
    color: #fff;
  }

  .footer-social {
    margin-top: 15px;
    display: flex;
    gap: 10px;
  }

  .footer-social a {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #0077b6;
    color: #fff;
    text-decoration: none;
    transition: all 0.3s ease;
  }

  .footer-social a:hover {
    background-color: #005f87;
    transform: scale(1.1);
  }
</style>

<!-- Scripts -->
<script src="lib/jquery/dist/jquery.min.js"></script>
<script src="lib/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/site3d49.js?v=3mhbb32mML9fvJL9BlFT134NRMU0vqwPRnZEDt880_E"></script>
<script src="js/navbar-active8e7c.js?v=dGPriXiIAjlcajsO9YP960In1CeOdTqoMQj1hIvVDHA"></script>
<script src="js/navbar-toggle-fix.html"></script>
<script src="js/mobile-menub273.js?v=Gh3nMh0WrzIl_Vl8_zqvsQ4blj2vRct2WTrrzuvIwVU"></script>
<script src="js/disable-swipe2953.js?v=PwgUEYjVAVjGPihdqv4XRIAwAykwOU08FzZSoeTBu08"></script>
<script src="../unpkg.com/aos%402.3.1/dist/aos.js"></script>
<script>
  AOS.init({
    duration: 800,
    easing: 'ease-in-out',
    once: true,
    mirror: false
  });
</script>

<script>
  // Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      e.preventDefault();
      document.querySelector(this.getAttribute('href')).scrollIntoView({
        behavior: 'smooth'
      });
    });
  });

  document.addEventListener('DOMContentLoaded', function () {
    const stepCards = document.querySelectorAll('.step-card');
    const howToAvailSection = document.querySelector('.how-to-avail h2');

    function isInViewport(element) {
      const rect = element.getBoundingClientRect();
      return (
        rect.top <= (window.innerHeight || document.documentElement.clientHeight) * 0.85 &&
        rect.bottom >= 0
      );
    }

    function handleScrollAnimation() {
      if (howToAvailSection && isInViewport(howToAvailSection)) {
        howToAvailSection.style.opacity = '1';
        howToAvailSection.style.transform = 'translateY(0)';
      }

      stepCards.forEach((card, index) => {
        if (isInViewport(card)) {
          setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
          }, 150 * index);
        }
      });
    }

    if (howToAvailSection) {
      howToAvailSection.style.opacity = '0';
      howToAvailSection.style.transform = 'translateY(30px)';
      howToAvailSection.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
    }

    stepCards.forEach((card, index) => {
      card.style.opacity = '0';
      card.style.transform = 'translateY(40px)';
      card.style.transition = 'opacity 0.7s ease, transform 0.7s ease';
    });

    setTimeout(handleScrollAnimation, 300);
    window.addEventListener('scroll', handleScrollAnimation);
  });

  // Equalize card header heights
  function equalizeCardHeaders() {
    const headers = document.querySelectorAll('.membership-card .card-header');
    let maxHeight = 0;

    headers.forEach(header => {
      header.style.height = 'auto';
      if (header.offsetHeight > maxHeight) {
        maxHeight = header.offsetHeight;
      }
    });

    headers.forEach(header => {
      header.style.height = maxHeight + 'px';
    });
  }

  window.addEventListener('load', equalizeCardHeaders);
  window.addEventListener('resize', equalizeCardHeaders);
</script>

<script>
  document.getElementById('relation').addEventListener('change', function () {
    const otherRelationContainer = document.getElementById('otherRelationContainer');
    if (this.value === 'Other') {
      otherRelationContainer.style.display = 'block';
      document.getElementById('otherRelation').required = true;
    } else {
      otherRelationContainer.style.display = 'none';
      document.getElementById('otherRelation').required = false;
      document.getElementById('otherRelation').value = '';
    }
  });
</script>

</body>
</html>





