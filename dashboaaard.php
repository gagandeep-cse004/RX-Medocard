<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  echo "<script>alert('Please login first.'); window.location.href='login.html';</script>";
  exit;
}

// ✅ Connect to database
$con = mysqli_connect('localhost', 'root', '', 'users');
$user_id = $_SESSION['user_id'];
$orderId = ''; // default empty

$orderStmt = $con->prepare("SELECT order_id FROM razorpay_orders WHERE user_id=? ORDER BY id DESC LIMIT 1");
$orderStmt->bind_param("i", $user_id);
$orderStmt->execute();
$orderRes = $orderStmt->get_result();
$orderRow = $orderRes->fetch_assoc();
$orderId = $orderRow['order_id'] ?? '';
$orderStmt->close();


$stmt = $con->prepare("SELECT name, card_type, card_validity, purchase_date FROM mydata WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $card_type, $card_validity, $purchase_date);

$stmt->fetch();

// ✅ Validity calculation block (added here)
if (!empty($purchase_date)) {
  $startDate = date('d-m-Y', strtotime($purchase_date));
  $endDate = date('d-m-Y', strtotime($purchase_date . ' +1 year -1 day'));
  $validityText = " 1 Year ($startDate to $endDate)";
} else {
  $validityText = "No validity date available.";
}

$stmt->close();
$con->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>RX Medocard | Dashboard</title>
  <link rel="stylesheet" href="dashboard.css">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    
  .form-container {
    background: #ffffff;
    padding: 35px;
    border-radius: 14px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    width: 100%;
    max-width: 500px;
    transition: box-shadow 0.3s ease;
  }

  .form-container:hover {
    box-shadow: 0 12px 32px rgba(0,0,0,0.18);
  }

  h2 {
    text-align: center;
    margin-bottom: 28px;
    color: #003366;
    font-size: 24px;
    font-weight: 600;
    letter-spacing: 0.5px;
  }

  label {
    font-weight: 600;
    margin: 14px 0 6px;
    display: block;
    color: #003366;
  }

  input[type="text"],
  input[type="date"],
  input[type="file"],
  select {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ccc;
    border-radius: 8px;
    margin-bottom: 18px;
    font-size: 15px;
    transition: border 0.3s ease;
  }

  input:focus,
  select:focus {
    border-color: #0055aa;
    outline: none;
  }

  button {
    width: 100%;
    padding: 12px;
    background: #0055aa;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 16px;
    cursor: pointer;
    transition: background 0.3s ease;
  }

  button:hover {
    background: #003366;
  }

  .dropdown {
    position: relative;
    width: 100%;
    margin-bottom: 18px;
  }

  .dropdown-btn {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ccc;
    background: #fff;
    cursor: pointer;
    text-align: left;
    border-radius: 8px;
    transition: border 0.3s ease;
  }

  .dropdown-btn:hover {
    border-color: #0055aa;
  }

  .dropdown-content {
    display: none;
    position: absolute;
    width: 100%;
    max-height: 300px;
    border: 1px solid #ccc;
    background: #fff;
    z-index: 1000;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    border-radius: 8px;
    overflow: hidden;
  }

  .dropdown-header {
    padding: 8px;
    border-bottom: 1px solid #eee;
    background: #f0f4ff;
    position: sticky;
    top: 0;
    z-index: 10;
  }

  .dropdown-header input {
    width: 95%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 6px;
  }

  .dropdown-list {
    max-height: 260px;
    overflow-y: auto;
  }

  .dropdown-list div {
    padding: 10px;
    cursor: pointer;
    font-size: 15px;
    transition: background 0.2s ease;
  }

  .dropdown-list div:hover {
    background: #e6f0ff;
  }
    body {
      background: #f5f7fb;
      /* I removed overflow: hidden so scrolling works when sections grow */
      font-family: "Poppins", sans-serif;
    }

    /* Sidebar */
    .sidebar {
      width: 260px;
      height: 100vh;
      background: #0d6efd;
      color: white;
      position: fixed;
      left: 0;
      top: 0;
      padding: 25px 0;
      overflow-y: auto;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.15);
      z-index: 20;
    }

    .sidebar .logo-box {
      text-align: center;
      margin-bottom: 20px;
    }

    .sidebar .logo-box img {
      width: 120px;
      border-radius: 10px;
    }

    .sidebar a,
    .sidebar button.menu-btn {
      color: white;
      font-size: 15px;
      padding: 12px 20px;
      display: block;
      text-decoration: none;
      border-radius: 6px;
      margin: 6px 15px;
      transition: 0.2s;
      background: transparent;
      border: none;
      text-align: left;
      width: calc(100% - 30px);
      cursor: pointer;
    }

    .sidebar a:hover,
    .sidebar button.menu-btn:hover,
    .sidebar a.active,
    .sidebar button.menu-btn.active {
      background: rgba(255, 255, 255, 0.18);
    }

    /* Main Content */
    .main-content {
      margin-left: 260px;
      padding: 30px;
      min-height: 100vh;
      overflow-y: auto;
    }

    .content-section {
      display: none;
      animation: fade 0.3s ease-in-out;
    }

    .content-section.active {
      display: block;
    }

    @keyframes fade {
      from {
        opacity: 0;
        transform: translateY(10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Fade-in Animations (for the card display) */
    .fade-in {
      animation: fadeIn 0.6s ease forwards;
      opacity: 0;
    }

    .fade-in-delay-1 {
      animation: fadeIn 0.7s ease forwards;
      opacity: 0;
    }

    .fade-in-delay-2 {
      animation: fadeIn 0.9s ease forwards;
      opacity: 0;
    }

    .fade-in-delay-3 {
      animation: fadeIn 1.1s ease forwards;
      opacity: 0;
    }

    .fade-in-delay-4 {
      animation: fadeIn 1.3s ease forwards;
      opacity: 0;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Modern card section styling */
    .modern-card-section {
      background: white;
      padding: 25px;
      border-radius: 15px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.07);
      animation: fadeIn 0.3s ease-in-out;
      max-width: 1100px;
      margin-bottom: 20px;
    }

    .info-box {
      background: #eef4ff;
      padding: 15px;
      border-radius: 10px;
      margin-bottom: 15px;
    }

    .services-box {
      background: #f8f9fc;
      padding: 18px;
      border-radius: 12px;
      margin-top: 15px;
      border-left: 5px solid #0d6efd;
    }

    .services-box ul {
      list-style: none;
      padding-left: 0;
    }

    .services-box ul li {
      padding: 6px 0;
      font-size: 15px;
    }

    .modern-btn {
      display: inline-block;
      padding: 12px 20px;
      border-radius: 8px;
      font-weight: 600;
      text-decoration: none;
      transition: 0.3s ease;
    }

    .primary-btn {
      background: #0d6efd;
      color: white;
    }

    .primary-btn:hover {
      background: #084dbf;
    }

    .secondary-btn {
      background: #e8ecff;
      color: #0d6efd;
      margin-left: 10px;
    }

    .secondary-btn:hover {
      background: #cdd6ff;
    }

    .rotating-card-container {
      perspective: 1200px;
      margin-top: 20px;
      display: flex;
      justify-content: center;
    }

    .rotating-card {
      width: 320px;
      height: 200px;
      position: relative;
      transform-style: preserve-3d;
      animation: rotateCard 8s infinite linear;
    }

    .rotating-face {
      position: absolute;
      inset: 0;
      backface-visibility: hidden;
    }

    .rotating-back {
      transform: rotateY(180deg);
    }

    @keyframes rotateCard {
      from {
        transform: rotateY(0deg);
      }

      to {
        transform: rotateY(360deg);
      }
    }

    .card-image {
      width: 100%;
      height: 100%;
      border-radius: 12px;
      object-fit: cover;
    }

    /* small responsive tweaks */
    @media (max-width: 900px) {
      .sidebar {
        width: 220px;
      }

      .main-content {
        margin-left: 220px;
        padding: 20px;
      }

      .rotating-card {
        width: 260px;
        height: 160px;
      }
    }

    @media (max-width: 700px) {
      .sidebar {
        position: relative;
        width: 100%;
        height: auto;
      }

      .main-content {
        margin-left: 0;
        padding: 15px;
      }
    }

    .services-card-wrapper {
      display: flex;
      justify-content: space-between;
      gap: 40px;
      align-items: center;
      margin-top: 20px;
    }

    .modal-content {
      background: #fff;
      width: 90%;
      max-width: 450px;
      padding: 20px;
      border-radius: 10px;
      animation: popup 0.3s ease-out;

      /* FIXES */
      box-sizing: border-box;
      max-height: 85vh;
      overflow-y: auto;
      margin: 0 10px;
      /* Prevent side overflow */
    }

    /* ---------- Form Section ---------- */


    .form-section h3 {
      text-align: center;
      color: #00bfff;
      margin-bottom: 20px;
      font-size: 1.6em;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    input,
    textarea {
      padding: 10px;
      border: none;
      border-radius: 5px;
      outline: none;
      font-size: 0.95em;
    }

    input[type="file"] {
      color: #fff;
    }

    button[type="submit"] {
      background-color: #00bfff;
      color: #fff;
      border: none;
      padding: 12px;
      font-size: 1em;
      border-radius: 8px;
      cursor: pointer;
      transition: 0.3s;
    }

    button[type="submit"]:hover {
      background-color: #0077b6;
      box-shadow: 0 0 15px #00ffff;
    }
    /* ---------------- MOBILE HEADER ---------------- */
.mobile-header {
  display: none;
  justify-content: space-between;
  align-items: center;
  padding: 12px 15px;
  background: white;
  border-bottom: 1px solid #ddd;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  z-index: 2000;
}

.mobile-logo {
  height: 60px;
}

.hamburger {
  background: #1a65e6;
  color: #fff;
  font-size: 26px;
  border: none;
  padding: 8px 12px;
  border-radius: 6px;
  cursor: pointer;
}

/* ---------------- SIDEBAR ---------------- */
.sidebar {
  width: 250px;
  background: rgb(70, 141, 255);
  height: 100vh;
  position: fixed;
  left: 0;
  top: 0;
  padding-top: 20px;
  border-right: 1px solid #ddd;
  transition: left 0.3s ease;
  z-index: 1500;
}

/* ---------------- DESKTOP ONLY ---------------- */
@media (min-width: 769px) {
  .sidebar {
    left: 0 !important;   /* Always visible on desktop */
  }
  .mobile-header {
    display: none;        /* hidden on desktop */
  }
  
}
/* Prevent content from hiding under mobile header */
@media (max-width: 768px) {
  .main-content {
    margin-top: 70px !important; /* adjust as needed */
  }
}


/* ---------------- MOBILE ONLY ---------------- */
@media (max-width: 768px) {
  .mobile-header {
    display: flex;
  }
  

  .sidebar {
    left: -260px;         /* hide sidebar by default */
    top: 60px;            /* push below mobile header */
  }

  .sidebar.open {
    left: 0;              /* slide in */
  }
}
@media (max-width: 768px) {
  
  .services-card-wrapper {
    flex-direction: column;
    align-items: center;
   
  }
  .logo-box{
    display: none;
  }

  .services-list {
    width: 100%;
    padding-left: 0;
    font-size: 14px;
    margin-bottom: 15px;
  }

  .services-list li {
    margin-bottom: 8px;
    list-style: none;
  }

  .image-slider {
    width: 100%;
  }

  .image-slider img {
    max-width: 260px; /* Bigger for mobile */
    width: 90%;
  }

  .services-box h5 {
    text-align: center;
  }
}

@media (max-width: 768px) {
  .image-slider img {
    width: 100% !important;
    max-width: 300px !important; /* bigger for phone */
    height: auto !important;
    object-fit: contain !important; /* ensures image is NOT cropped */
    margin-left: 30px;
  }

  .image-slider {
    width: 100%;
    display: flex;
    justify-content: center;
  }
}
  </style>
</head>

<body>

  <!-- ==================== SIDEBAR ==================== -->
   
  <!-- MOBILE HEADER (only visible on phone) -->
  <div class="mobile-header">
    <img src="treelogo.jpg" class="mobile-logo" alt="Logo">
    <button class="hamburger" onclick="toggleSidebar()" id="hamIcon">☰</button>
  </div>
  
  <!-- SIDEBAR -->
  <div class="sidebar" id="sidebar">
  
    <div class="logo-box">
      <img src="treelogo.jpg" alt="RX Medocard Logo">
    </div>
  
    <button class="menu-btn" onclick="showSection('dashboard', this)">Profile</button>
    <button class="menu-btn" onclick="showSection('cards', this)">Card Details</button>
    <button class="menu-btn" onclick="showSection('membership', this)">Family Plans</button>
    <button class="menu-btn" onclick="showSection('hospitals', this)">Hospitals</button>
    <button class="menu-btn" onclick="showSection('pharmacy', this)">Pharmacy</button>
    <button class="menu-btn" onclick="showSection('diagnostic', this)">Diagnostic</button>
    <button class="menu-btn" onclick="showSection('pathology', this)">Pathology</button>
  
    <button class="menu-btn" onclick="showSection('rediology', this)">Rediology</button>
  
    <button class="menu-btn" onclick="showSection('OPD', this)">OPD</button>
    <button class="menu-btn" onclick="showSection('payment', this)">Payment History</button>
    <button class="menu-btn" onclick="showSection('documents', this)">Upload Documents</button>
  
    <button class="menu-btn btn btn-danger mt-3" style="margin: 0 15px;" onclick="return confirmLogout()">Logout</button>
  </div>

  <!-- ==================== MAIN CONTENT ==================== -->
  <div class="main-content">

    <!-- DASHBOARD (profile) -->
    <div id="dashboard" class="content-section active">
      <div class="modern-card-section">

        <h3 class="fade-in">Welcome,
          <?php echo htmlspecialchars($name); ?>
        </h3>

        <div class="info-box fade-in-delay-1">
          <p><strong>Selected Card:</strong>
            <?php echo htmlspecialchars($card_type); ?>
          </p>
          <p><strong>Type:</strong> Health Insurance</p>
          <p><strong>Validity:</strong>
            <?php echo $validityText; ?>
          </p>
        </div>

        <div class="button-group fade-in-delay-3">
          <a href="generate_card.php" class="modern-btn primary-btn">
            📥 Download RX Medo Card
          </a>

          <a href="download_receipt.php?order_id=<?php echo $orderId; ?>" class="modern-btn secondary-btn">
            📄 Download Receipt
          </a>
        </div>

        <div class="services-box fade-in-delay-2">

          <h5>Included Services</h5>

          <div class="services-card-wrapper">

            <!-- LEFT SIDE : Services -->
            <ul class="services-list">
              <li>✔ Waiver of First Year Exclusion</li>
              <li>✔ Waiver of 30 Days Waiting Period</li>
              <li>✔ Waiver of First Two Year Exclusion</li>
              <li>✔ Cover for Pre-Existing Diseases</li>
              <li>✔ ₹5 Lakh Insurance + ₹50 Lakh Accidental Cover</li>
              <li>✔ Free Online Consultation</li>
              <li>✔ Up to <b>20%</b> long-term renewal discount</li>
            </ul>

            <!-- RIGHT SIDE : Rotating Card -->
            <div class="image-slider">
              <img src="images/yellowfront.jpg" class="slide">
              <img src="images/yellowback.jpg" class="slide">
              <img src="images/bluefront.jpg" class="slide">
              <img src="images/blueback.jpg" class="slide">
            </div>

          </div>
        </div>
        <style>
          .image-slider {
            width: 350px;
            height: 220px;
            position: relative;
            overflow: hidden;
          }

          .slide {
            width: 100%;
            height: 100%;
            object-fit: cover;

            position: absolute;
            top: 0;
            left: 0;

            opacity: 0;
            animation: fadeSlide 12s infinite;
          }

          /* Image Timings */
          .slide:nth-child(1) {
            animation-delay: 0s;
          }

          .slide:nth-child(2) {
            animation-delay: 3s;
          }

          .slide:nth-child(3) {
            animation-delay: 6s;
          }

          .slide:nth-child(4) {
            animation-delay: 9s;
          }

          /* Fade Animation */
          @keyframes fadeSlide {
            0% {
              opacity: 0;
            }

            10% {
              opacity: 1;
            }

            25% {
              opacity: 1;
            }

            35% {
              opacity: 0;
            }

            100% {
              opacity: 0;
            }
          }
        </style>



      </div>
    </div>


    <!-- CARDS -->
    <div id="cards" class="content-section">
      <div id="cards" class="card-box dynamic-section">
        <h3>Available RX Medo Cards</h3>
        <p>You can select the other card that suits your needs:</p>



        <div class="card-grid">
          <!-- ✅ First Card with View Services Button -->
          <div class="card-item">
            <h4>RX Medo Card</h4>
            <p>Offer: ₹2500 (Original ₹3500)</p>
            <button onclick="showServices()" class="view-btn">View Services</button>
          </div>

          <!-- Other Cards -->
          <div class="card-item">
            <h4>RX Medo Insure Card</h4>
            <p>Coverage up to ₹5 Lakh</p>
            <button onclick="showInsureServices()" class="view-btn">View Services</button>
          </div>

          <div class="card-item">
            <h4>RX Insure Top Up Card</h4>
            <p>Complete protection</p>
          </div>
          <div class="card-item">
            <h4>RX Medo Insure Top-Up Card</h4>
            <p>Combo Plan</p>
          </div>
        </div>
        <div class="button-group">
          <!--<a href="process_payment.php" class="btn">Make Payment</a>-->
          <a href="yellowcardfront.jpg" class="modern-btn secondary-btn">Download Your Card</a>
          <a href="receipt.html" class="modern-btn secondary-btn">Download Your Receipt</a>
        </div>
      </div>

    </div>

    <!-- MEMBERSHIP -->
    <div id="membership" class="content-section">
      <div class="modern-card-section">
        <h2 class="fw-bold mb-3">Family Plans</h2>
        <ul>
          <li>Basic – ₹5,000/year (2 members)</li>
          <li>Standard – ₹10,000/year (4 members)</li>
          <li>Premium – ₹15,000/year (6 members + benefits)</li>
        </ul>
      </div>
    </div>

    <!-- HOSPITALS -->
    <div id="hospitals" class="content-section">
      <h2 style="text-align: center;">Our Hospital Network</h2>
      <div style="display: flex;gap: 10px;margin-top: 40px;" class="hsp">
        <!-- Hospital Cards -->
        <div class="hospital-cards-container row g-4">
          <!-- MD City Hospital -->
          <div class="col-lg-4 col-md-6">
            <div class="hospital-card">
              <div class="hospital-card-img-container">
                <img src="./images/MD.jpg" alt="MD City Hospital" class="hospital-card-img">
                <div class="hospital-card-overlay"></div>
                <div class="hospital-card-rating">
                  <i class="bi bi-star-fill"></i> 4.5
                </div>
              </div>
              <div class="hospital-card-content">
                <h3 class="hospital-card-title">MD City Hospital</h3>
                <div class="hospital-card-location">
                  <i class="bi bi-geo-alt-fill"></i> Sector 21, Noida, India
                </div>
                <p class="hospital-card-description">
                  Multi-specialty services and emergency care with priority access for Rx Medo Card holders.
                </p>
                <ul class="hospital-card-features">
                  <li>Multi-Specialty Care</li>
                  <li>Advanced Diagnostics</li>
                  <li>International Patients</li>
                </ul>
                <a href="https://www.google.com/search?q=MD+City+Hospital" target="_blank" class="hospital-card-btn">
                  View Location <i class="bi bi-arrow-right"></i>
                </a>
              </div>
            </div>
          </div>

          <!-- Sarvodaya Hospitals -->
          <div class="col-lg-4 col-md-6">
            <div class="hospital-card">
              <div class="hospital-card-img-container">
                <img src="./images/sarvodya(greater).jpg" alt="Sarvodaya Hospital" class="hospital-card-img">
                <div class="hospital-card-overlay"></div>
                <div class="hospital-card-rating">
                  <i class="bi bi-star-fill"></i> 4.4
                </div>
              </div>
              <div class="hospital-card-content">
                <h3 class="hospital-card-title">Sarvodaya Hospital</h3>
                <div class="hospital-card-location">
                  <i class="bi bi-geo-alt-fill"></i> Greater Noida, India
                </div>
                <p class="hospital-card-description">
                  Advanced medical facilities and experienced staff offering seamless care.
                </p>
                <ul class="hospital-card-features">
                  <li>Multi-Specialty Care</li>
                  <li>Emergency Services</li>
                  <li>International Patients</li>
                </ul>
                <a href="https://www.google.com/search?q=sarvodaya+hospital" target="_blank" class="hospital-card-btn">
                  View Location <i class="bi bi-arrow-right"></i>
                </a>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6">
            <div class="hospital-card">
              <div class="hospital-card-img-container">
                <img src="./images/sorvodya-hospital.jpg" alt="Sarvodaya Hospital Sector 8" class="hospital-card-img">
                <div class="hospital-card-overlay"></div>
                <div class="hospital-card-rating">
                  <i class="bi bi-star-fill"></i> 4.4
                </div>
              </div>
              <div class="hospital-card-content">
                <h3 class="hospital-card-title">Sarvodaya Hospital</h3>
                <div class="hospital-card-location">
                  <i class="bi bi-geo-alt-fill"></i> Sector 8, Faridabad
                </div>
                <p class="hospital-card-description">
                  A leading healthcare center in Faridabad, known for quality patient care and advanced medical
                  facilities.
                </p>
                <ul class="hospital-card-features">
                  <li>Multi-Specialty Care</li>
                  <li>Emergency Services</li>
                  <li>International Patients</li>
                </ul>
                <a href="https://www.google.com/search?q=sarvodaya+hospital" target="_blank" class="hospital-card-btn">
                  View Location <i class="bi bi-arrow-right"></i>
                </a>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6">
            <div class="hospital-card">
              <div class="hospital-card-img-container">
                <img src="./images/faridabad.jpg" alt="Sarvodaya Hospital Sector 19" class="hospital-card-img">
                <div class="hospital-card-overlay"></div>
                <div class="hospital-card-rating">
                  <i class="bi bi-star-fill"></i> 4.4
                </div>
              </div>
              <div class="hospital-card-content">
                <h3 class="hospital-card-title">Sarvodaya Hospital</h3>
                <div class="hospital-card-location">
                  <i class="bi bi-geo-alt-fill"></i> Sector 19, Faridabad
                </div>
                <p class="hospital-card-description">
                  A leading healthcare center in Faridabad, known for quality patient care and advanced medical
                  facilities.
                </p>
                <ul class="hospital-card-features">
                  <li>Multi-Specialty Care</li>
                  <li>Emergency Services</li>
                  <li>International Patients</li>
                </ul>
                <a href="https://www.google.com/search?q=sarvodaya+hospital" target="_blank" class="hospital-card-btn">
                  View Location <i class="bi bi-arrow-right"></i>
                </a>
              </div>
            </div>
          </div>

          <!-- Felix Hospitals -->
          <div class="col-lg-4 col-md-6">
            <div class="hospital-card">
              <div class="hospital-card-img-container">
                <img src="./images/felix.jpg" alt="Felix Hospital Noida" class="hospital-card-img">
                <div class="hospital-card-overlay"></div>
                <div class="hospital-card-rating">
                  <i class="bi bi-star-fill"></i> 4.5
                </div>
              </div>
              <div class="hospital-card-content">
                <h3 class="hospital-card-title">Felix Hospital</h3>
                <div class="hospital-card-location">
                  <i class="bi bi-geo-alt-fill"></i> Sector-137, Noida
                </div>
                <p class="hospital-card-description">
                  Comprehensive healthcare services with modern infrastructure and support.
                </p>
                <ul class="hospital-card-features">
                  <li>Multi-Specialty Care</li>
                  <li>Advanced Diagnostics</li>
                  <li>International Patients</li>
                </ul>
                <a href="https://www.google.com/search?q=Felix+Hospital+Noida" target="_blank"
                  class="hospital-card-btn">
                  View Location <i class="bi bi-arrow-right"></i>
                </a>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6">
            <div class="hospital-card">
              <div class="hospital-card-img-container">
                <img src="./images/felix(greater).jpg" alt="Felix Hospital Greater Noida" class="hospital-card-img">
                <div class="hospital-card-overlay"></div>
                <div class="hospital-card-rating">
                  <i class="bi bi-star-fill"></i> 4.5
                </div>
              </div>
              <div class="hospital-card-content">
                <h3 class="hospital-card-title">Felix Hospital</h3>
                <div class="hospital-card-location">
                  <i class="bi bi-geo-alt-fill"></i> Greater Noida West
                </div>
                <p class="hospital-card-description">
                  Comprehensive healthcare services with modern infrastructure and support.
                </p>
                <ul class="hospital-card-features">
                  <li>Multi-Specialty Care</li>
                  <li>Advanced Diagnostics</li>
                  <li>International Patients</li>
                </ul>
                <a href="https://www.google.com/search?q=Felix+Hospital+Noida" target="_blank"
                  class="hospital-card-btn">
                  View Location <i class="bi bi-arrow-right"></i>
                </a>
              </div>
            </div>
          </div>

          <!-- Max Hospital -->
          <div class="col-lg-4 col-md-6">
            <div class="hospital-card">
              <div class="hospital-card-img-container">
                <img src="./images/max.webp" alt="Max Hospital" class="hospital-card-img">
                <div class="hospital-card-overlay"></div>
                <div class="hospital-card-rating">
                  <i class="bi bi-star-fill"></i> 4.2
                </div>
              </div>
              <div class="hospital-card-content">
                <h3 class="hospital-card-title">Max Hospital</h3>
                <div class="hospital-card-location">
                  <i class="bi bi-geo-alt-fill"></i> Saket, New Delhi
                </div>
                <p class="hospital-card-description">
                  Specialized treatments and priority appointments for Rx Medo Card members.
                </p>
                <ul class="hospital-card-features">
                  <li>Multi-Specialty Care</li>
                  <li>Advanced Diagnostics</li>
                  <li>International Patients</li>
                </ul>
                <a href="https://www.google.com/search?q=Max+Hospital+Saket+Delhi" target="_blank"
                  class="hospital-card-btn">
                  View Location <i class="bi bi-arrow-right"></i>
                </a>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

    <style>
      .hospital-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: transform 0.3s, box-shadow 0.3s;
      }

      .hospital-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
      }

      .hospital-card-img-container {
        position: relative;
        width: 100%;
        height: 200px;
        overflow: hidden;
      }

      .hospital-card-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
      }

      .hospital-card-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0));
      }

      .hospital-card-rating {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #ffd700;
        padding: 5px 10px;
        border-radius: 8px;
        font-weight: bold;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 5px;
      }

      .hospital-card-content {
        padding: 20px;
      }

      .hospital-card-title {
        font-size: 1.3rem;
        margin-bottom: 8px;
        color: #333;
      }

      .hospital-card-location {
        font-size: 0.9rem;
        margin-bottom: 10px;
        color: #777;
        display: flex;
        align-items: center;
        gap: 5px;
      }

      .hospital-card-description {
        font-size: 0.95rem;
        color: #555;
        margin-bottom: 12px;
      }

      .hospital-card-features {
        list-style: none;
        padding: 0;
        margin-bottom: 15px;
      }

      .hospital-card-features li {
        font-size: 0.85rem;
        color: #555;
        margin-bottom: 5px;
        position: relative;
        padding-left: 15px;
      }

      .hospital-card-features li::before {
        content: "✔";
        position: absolute;
        left: 0;
        color: #28a745;
      }

      .hospital-card-btn {
        display: inline-block;
        padding: 8px 16px;
        background: #007bff;
        color: #fff;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 500;
        transition: background 0.3s;
      }

      .hospital-card-btn:hover {
        background: #0056b3;
      }

      .card-item {
        border: 2px solid blue;
      }

      .btn {
        border: 2px solid blue;
      }

      @media (max-width:800px) {
        .hsp {
          display: flex;
          flex-direction: column;
        }

      }
    </style>



    <div id="pharmacy" class="content-section">
      <section class="hospital-info">
        <img
          src="https://media.istockphoto.com/photos/modern-hospital-building-picture-id1312706413?b=1&k=20&m=1312706413&s=170667a&w=0&h=VRi3w2E1UqvCCcK-nDV6mH7FhDZoTU9MM2QKSom96X4="
          alt="Hospital Image" />
        <div class="info-text">
          <h2>About Our Pharmacy Services</h2>
          <p>We connect you with top hospitals in the city offering the best outpatient (Pharmacy) care with exclusive
            discounts and free checkups for a limited time.</p>
          <div class="discount">
            🎉 <strong>Special Offer:</strong> Get <b>20% OFF</b> on select hospital Pharmacy Consultations this week!
          </div> <br>
          <button class="appointment-btn">
            <a href="#pharmacy-form">Book Appointment</a>
          </button>

        </div>
      </section>

      <!-- Recommended Hospitals -->
      <section class="recommended">
        <h2>🏥 Our Best Recommendations for You</h2>
        <div class="hospital-grid">

          <!-- MD City Hospital -->
          <div class="hospital-box">
            <img style="height: 250px;" src="./images/MD.jpg" alt="MD City Hospital" />
            <h3>MD City Hospital</h3>
            <p><b>Address:</b> Sector 21,Noida,India</p>
            <p>Top-rated multi-speciality hospital providing Pharmacy consultation this month.</p>
            <div class="offer">20% OFF on pharmacy</div>
          </div>

          <!-- Sarvodaya Hospital -->
          <div class="hospital-box">
            <img style="height: 250px;" src="./images/sorvodya-hospital.jpg" alt="Sarvodaya Hospital" />
            <h3>Sarvodaya Hospital</h3>
            <p><b>Address:</b> Greater Noida,India</p>
            <p>Providing expert care and modern facilities. Enjoy a special limited-time Pharmacy discount.</p>
            <div class="offer">🔥 20% OFF on Pharmacy</div>
          </div>

          <!-- Felix Hospital -->
          <div class="hospital-box">
            <img style="height: 250px;" src="./images/felix.jpg" alt="Felix Hospital" />
            <h3>Felix Hospital</h3>
            <p><b>Address:</b> Sector 137, Noida, Uttar Pradesh 201305</p>
            <p>Comprehensive healthcare services with modern Pharmacy facilities and experienced doctors.</p>
            <div class="offer">💰 20% OFF on Pharmacy</div>
          </div>
        </div>
      </section>

      <section id="opd" class="contact">
        <h2>📞 Contact Us</h2>
        <a href="tel:+919999307517"><button class="call-btn">Call Us Now</button></a>
      </section>

      <section class="form-section" id="pharmacy-form">
        <h3 style="text-align: center;">🩺 Pharmacy Appointment Form</h3>
        <form id="opdForm">
          <input type="text" placeholder="Full Name" required />
          <input type="text" placeholder="Hospital Name" required />
          <input type="text" placeholder="Patient ID / Email" required />
          <input type="text" placeholder="Contact Number (for easy reach-out)" required />
          <input type="date" required />
          <input type="text" placeholder="Card Number" />
          <input type="file" accept="image/*,application/pdf" />
          <textarea rows="3" placeholder="Description of receipt or upload directly above"></textarea>
          <button type="submit">Submit Form</button>
        </form>
      </section>




    </div>

    <!-- DIAGNOSTIC -->
    <div id="diagnostic" class="content-section">
      <section class="hospital-info">
        <img
          src="https://media.istockphoto.com/photos/modern-hospital-building-picture-id1312706413?b=1&k=20&m=1312706413&s=170667a&w=0&h=VRi3w2E1UqvCCcK-nDV6mH7FhDZoTU9MM2QKSom96X4="
          alt="Hospital Image" class="hospital-img" />

        <div class="info-text">
          <h2>About Our Diagnostic Services</h2>
          <p>
            We connect you with top hospitals in the city offering the best outpatient diagnostic care with exclusive
            discounts and free checkups for a limited time.
          </p>
          <div class="discount">
            🎉 <strong>Special Offer:</strong> Get <b>20% OFF</b> on select hospital Diagnostic Consultations this week!
          </div> <br>
          <button class="appointment-btn">
            <a href="#diagnostic-form">Book Appointment</a>
          </button>


        </div>

        <div class="radiology-box">
          <h1>About Radiology</h1>
          <p>
            Radiology is the medical specialty that uses imaging techniques such as X-rays, CT scans, and MRIs to
            diagnose and
            treat diseases.
          </p>
          <button class="menu-btn" onclick="showSection('rediology', this)">Rediology</button>
        </div>
      </section>
      <script>
        const btn = document.getElementById("radiology-btn");

        btn.addEventListener("click", () => {
          btn.classList.add("clicked");

          setTimeout(() => {
            window.location.href = "rediologydash.html"; // Redirects to the page
          }, 1000);
        });
      </script>



      <style>
        .hospital-info {
          display: flex;
          flex-wrap: wrap;
          justify-content: center;
          align-items: center;
          padding: 40px 20px;
          gap: 40px;

          border-radius: 20px;
          box-shadow: 0 0 40px rgba(0, 191, 255, 0.2);
          transition: transform 0.3s ease;
        }

        .hospital-info:hover {
          transform: scale(1.01);
        }

        .hospital-info img {
          max-width: 400px;
          width: 100%;
          border-radius: 15px;
          box-shadow: 0 0 30px #00bfff;
          transition: transform 0.5s ease, box-shadow 0.3s ease;
        }

        .hospital-info img:hover {
          transform: scale(1.05);
          box-shadow: 0 0 40px #00ffff;
        }

        .info-text {
          max-width: 500px;
          animation: fadeIn 1.2s ease;
        }

        .info-text h2 {
          color: #00bfff;
          font-size: 1.8rem;
          margin-bottom: 10px;
        }

        .discount {
          background-color: rgba(255, 255, 255, 0.1);
          padding: 15px;
          margin-top: 15px;
          border-left: 4px solid #00bfff;
          animation: glow 2s infinite alternate;
          border-radius: 10px;
        }

        .radiology-box {
          background: rgba(255, 255, 255, 0.05);
          padding: 25px;
          border-radius: 15px;
          box-shadow: 0 0 20px rgba(0, 191, 255, 0.2);
          max-width: 450px;
          text-align: center;
          transition: transform 0.3s ease;
          animation: fadeInUp 1.2s ease;
        }

        .radiology-box:hover {
          transform: translateY(-10px);
        }

        .radiology-box h1 {
          color: #00bfff;
          margin-bottom: 10px;
        }

        .radiology-box button {
          background: linear-gradient(90deg, #00bfff, #00ffff);
          border: none;
          color: #000;
          padding: 12px 25px;
          border-radius: 30px;
          cursor: pointer;
          font-weight: bold;
          transition: 0.3s ease;
        }

        .radiology-box button:hover {
          transform: scale(1.1);
          box-shadow: 0 0 20px #00ffff;
        }

        .radiology-box button.clicked {
          background: linear-gradient(90deg, #00ffff, #00bfff);
          opacity: 0.8;
        }

        @keyframes glow {
          from {
            box-shadow: 0 0 10px #00bfff;
          }

          to {
            box-shadow: 0 0 30px #00ffff;
          }
        }

        @keyframes fadeIn {
          from {
            opacity: 0;
            transform: translateY(20px);
          }

          to {
            opacity: 1;
            transform: translateY(0);
          }
        }

        @keyframes fadeInUp {
          from {
            opacity: 0;
            transform: translateY(50px);
          }

          to {
            opacity: 1;
            transform: translateY(0);
          }
        }

        @media (max-width: 768px) {
          .hospital-info {
            text-align: center;
            gap: 30px;
          }

          .hospital-info img {
            max-width: 100%;
          }

          .radiology-box {
            width: 100%;
          }
        }
      </style>


      <!-- Recommended Hospitals -->
      <section class="recommended">
        <h2>🏥 Our Best Recommendations for You</h2>
        <div class="hospital-grid">

          <!-- MD City Hospital -->
          <div class="hospital-box">
            <img style="height: 250px;" src="./images/MD.jpg" alt="MD City Hospital" />
            <h3>MD City Hospital</h3>
            <p><b>Address:</b> Sector 21,Noida,India</p>
            <p>Top-rated multi-speciality hospital providing Diagnostic consultation this month.</p>
            <div class="offer">💙 30% OFF on Diagnostic</div>
          </div>

          <!-- Sarvodaya Hospital -->
          <div class="hospital-box">
            <img style="height: 250px;" src="./images/sorvodya-hospital.jpg" alt="Sarvodaya Hospital" />
            <h3>Sarvodaya Hospital</h3>
            <p><b>Address:</b>Greater Noida,India</p>
            <p>Providing expert care and modern facilities. Enjoy a special limited-time Diagnostic discount.</p>
            <div class="offer">🔥 20% OFF on Diagnostic</div>
          </div>

          <!-- Felix Hospital -->
          <div class="hospital-box">
            <img style="height: 250px;" src="./images/felix.jpg" alt="Felix Hospital" />
            <h3>Felix Hospital</h3>
            <p><b>Address:</b> Sector 137, Noida, Uttar Pradesh 201305</p>
            <p>Comprehensive healthcare services with modern Diagnostic facilities and experienced doctors.</p>
            <div class="offer">💰 20% Discount on Diagnostic</div>
          </div>
        </div>
      </section>

      <section class="contact" id="diagnostic">
        <h2>📞 Contact Us</h2>
        <a href="tel:+919999307517"><button class="call-btn">Call Us Now</button></a>
        <!-- <h3 style="font-size: 25px;">🩺 Diagnostic Appointment Form</h3> -->
      </section>

      <section class="form-section" id="diagnostic-form">
        <div style="display: flex;margin-bottom: 30px;">
          <img style="width: 100px;height: 100px;justify-content: center;" src="./images/logo.webp" alt="">
          <p style="text-align: center;margin-left: 50px;font-size: 20px;">Please fill the form for book your
            appointment</p>

        </div>

        <form id="opdForm">
          <input type="text" placeholder="Full Name" required />
          <input type="text" placeholder="Hospital Name" required />
          <input type="text" placeholder="Patient ID / Email" required />
          <input type="text" placeholder="Contact Number (for easy reach-out)" required />
          <input type="date" required />
          <input type="text" placeholder="Card Number" />
          <input type="file" accept="image/*,application/pdf" />
          <textarea rows="3" placeholder="Description of receipt or upload directly above"></textarea>
          <button type="submit">Submit Form</button>
        </form>
      </section>



      <script>
        document.getElementById("opdForm").addEventListener("submit", (e) => {
          e.preventDefault();
          alert("✅ Form Submitted Successfully! Thank you for booking your OPD appointment.");
          e.target.reset();
        });
      </script>


    </div>

    <!-- pathology section -->
    <div id="pathology" class="content-section">
      <section class="hospital-info">
        <img
          src="https://media.istockphoto.com/photos/modern-hospital-building-picture-id1312706413?b=1&k=20&m=1312706413&s=170667a&w=0&h=VRi3w2E1UqvCCcK-nDV6mH7FhDZoTU9MM2QKSom96X4="
          alt="Hospital Image" />
        <div class="info-text">
          <h2>About Our Pathology Services</h2>
          <p>We connect you with top hospitals in the city offering the best outpatient Pathology care with exclusive
            discounts and free checkups for a limited time.</p>
          <div class="discount">
            🎉 <strong>Special Offer:</strong> Get <b>20% OFF</b> on select hospital Pathology Consultations this
            week!
          </div> <br>
          <button class="appointment-btn">
            <a href="#pathology-form">Book Appointment</a>
          </button>
        </div>
      </section>

      <!-- Recommended Hospitals -->
      <section class="recommended">
        <h2>🏥 Our Best Recommendations for You</h2>
        <div class="hospital-grid">

          <!-- MD City Hospital -->
          <div class="hospital-box">
            <img style="height: 250px;" src="./images/MD.jpg" alt="MD City Hospital" />
            <h3>MD City Hospital</h3>
            <p><b>Address:</b> Sector 21,Noida,India</p>
            <p>Top-rated multi-speciality hospital providing Pathology consultation this month.</p>
            <div class="offer">💙 30% OFF on Pathology</div>
          </div>

          <!-- Sarvodaya Hospital -->
          <div class="hospital-box">
            <img style="height: 250px;" src="./images/sorvodya-hospital.jpg" alt="Sarvodaya Hospital" />
            <h3>Sarvodaya Hospital</h3>
            <p><b>Address:</b>Greater Noida,India</p>
            <p>Providing expert care and modern facilities. Enjoy a special limited-time Pathology discount.</p>
            <div class="offer">🔥 20% OFF on Pathology</div>
          </div>

          <!-- Felix Hospital -->
          <div class="hospital-box">
            <img style="height: 250px;" src="./images/felix.jpg" alt="Felix Hospital" />
            <h3>Felix Hospital</h3>
            <p><b>Address:</b> Sector 137, Noida, Uttar Pradesh 201305</p>
            <p>Comprehensive healthcare services with modern Pathology facilities and experienced doctors.</p>
            <div class="offer">💰 20% Discount on Pathology</div>
          </div>

        </div>
      </section>

      <section class="contact">
        <h2>📞 Contact Us</h2>
        <a href="tel:+919999307517"><button class="call-btn">Call Us Now</button></a>
      </section>

      <section class="form-section" id="pathology-form">
        <h3>🩺 Pathology Appointment Form</h3>
        <form id="opdForm">
          <input type="text" placeholder="Full Name" required />
          <input type="text" placeholder="Hospital Name" required />
          <input type="text" placeholder="Patient ID / Email" required />
          <input type="text" placeholder="Contact Number (for easy reach-out)" required />
          <input type="date" required />
          <input type="text" placeholder="Card Number" />
          <input type="file" accept="image/*,application/pdf" />
          <textarea rows="3" placeholder="Description of receipt or upload directly above"></textarea>
          <button type="submit">Submit Form</button>
        </form>
      </section>

    </div>

    <div id="rediology" class="content-section">
      <section class="hospital-info">
        <img src="https://media.istockphoto.com/photos/modern-hospital-building-picture-id1312706413?b=1&k=20&m=1312706413&s=170667a&w=0&h=VRi3w2E1UqvCCcK-nDV6mH7FhDZoTU9MM2QKSom96X4="
            alt="Hospital Image" />
        <div class="info-text">
            <h2>About Our Rediology Services</h2>
            <p>We connect you with top hospitals in the city offering the best outpatient Rediology care with exclusive
                discounts and free checkups for a limited time.</p>
            <div class="discount">
                🎉 <strong>Special Offer:</strong> Get <b>20% OFF</b> on select hospital Rediology Consultations this
                week!
            </div>
        </div>
    </section>

    <!-- Recommended Hospitals -->
    <section class="recommended">
        <h2>🏥 Our Best Recommendations for You</h2>
        <div class="hospital-grid">

            <!-- MD City Hospital -->
            <div class="hospital-box">
                <img style="height: 250px;" src="./images/MD.jpg" alt="MD City Hospital" />
                <h3>MD City Hospital</h3>
                <p><b>Address:</b> Sector 21,Noida,India</p>
                <p>Top-rated multi-speciality hospital providing Rediology consultation this month.</p>
                <div class="offer">💙 30% OFF on Rediology</div>
            </div>

            <!-- Sarvodaya Hospital -->
            <div class="hospital-box">
                <img style="height: 250px;" src="./images/sorvodya-hospital.jpg" alt="Sarvodaya Hospital" />
                <h3>Sarvodaya Hospital</h3>
                <p><b>Address:</b>Greater Noida,India</p>
                <p>Providing expert care and modern facilities. Enjoy a special limited-time Rediology discount.</p>
                <div class="offer">🔥 20% OFF on Pathology</div>
            </div>

            <!-- Felix Hospital -->
            <div class="hospital-box">
                <img style="height: 250px;" src="./images/felix.jpg" alt="Felix Hospital" />
                <h3>Felix Hospital</h3>
                <p><b>Address:</b> Sector 137, Noida, Uttar Pradesh 201305</p>
                <p>Comprehensive healthcare services with modern Rediology facilities and experienced doctors.</p>
                <div class="offer">💰 20% Discount on Rediology</div>
            </div>

        </div>
    </section>

    <section class="contact">
        <h2>📞 Contact Us</h2>
        <a href="tel:+919999307517"><button class="call-btn">Call Us Now</button></a>
    </section>

    <section class="form-section">
        <h3>🩺 Rediology Appointment Form</h3>
        <form id="opdForm">
            <input type="text" placeholder="Full Name" required />
            <input type="text" placeholder="Hospital Name" required />
            <input type="text" placeholder="Patient ID / Email" required />
            <input type="text" placeholder="Contact Number (for easy reach-out)" required />
            <input type="date" required />
            <input type="text" placeholder="Card Number" />
            <input type="file" accept="image/*,application/pdf" />
            <textarea rows="3" placeholder="Description of receipt or upload directly above"></textarea>
            <button type="submit">Submit Form</button>
        </form>
    </section>

    </div>

    <!-- opd -->
    <div id="OPD" class="content-section">
      <section class="hospital-info">
        <img
          src="https://media.istockphoto.com/photos/modern-hospital-building-picture-id1312706413?b=1&k=20&m=1312706413&s=170667a&w=0&h=VRi3w2E1UqvCCcK-nDV6mH7FhDZoTU9MM2QKSom96X4="
          alt="Hospital Image" />
        <div class="info-text">
          <h2>About Our OPD Services</h2>
          <p>We connect you with top hospitals in the city offering the best outpatient (OPD) care with exclusive
            discounts and free checkups for a limited time.</p>
          <div class="discount">
            🎉 <strong>Special Offer:</strong> Get <b>20% OFF</b> on select hospital OPD Consultations this week!
          </div><br>
          <button class="appointment-btn">
            <a href="#opd-form">Book Appointment</a>
          </button>
        </div>
      </section>

      <!-- Recommended Hospitals -->
      <section class="recommended">
        <h2>🏥 Our Best Recommendations for You</h2>
        <div class="hospital-grid">

          <!-- MD City Hospital -->
          <div class="hospital-box">
            <img style="height: 250px;" src="./images/MD.jpg" alt="MD City Hospital" />
            <h3>MD City Hospital</h3>
            <p><b>Address:</b> Sector 21,Noida,India</p>
            <p>Top-rated multi-speciality hospital providing free OPD consultation this month.</p>
            <div class="offer">💙 Free OPD Consultation</div>
          </div>

          <!-- Sarvodaya Hospital -->
          <div class="hospital-box">
            <img style="height: 250px;" src="./images/sorvodya-hospital.jpg" alt="Sarvodaya Hospital" />
            <h3>Sarvodaya Hospital</h3>
            <p><b>Address:</b> Greater Noida,India</p>
            <p>Providing expert care and modern facilities. Enjoy a special limited-time OPD discount.</p>
            <div class="offer">🔥 20% OFF on OPD</div>
          </div>

          <!-- Felix Hospital -->
          <div class="hospital-box">
            <img style="height: 250px;" src="./images/felix.jpg" alt="Felix Hospital" />
            <h3>Felix Hospital</h3>
            <p><b>Address:</b> Sector 137, Noida, Uttar Pradesh 201305</p>
            <p>Comprehensive healthcare services with modern OPD facilities and experienced doctors.</p>
            <div class="offer">💰 20% Discount on OPD</div>
          </div>

        </div>
      </section>

      <section class="contact" id="opd-form">
        <h2>📞 Contact Us</h2>
        <a href="tel:+919999307517"><button class="call-btn">Call Us Now</button></a>
      </section>

      <section class="form-section">
        <h3>🩺 OPD Appointment Form</h3>
        <form id="opdForm">
          <input type="text" placeholder="Full Name" required />
          <input type="text" placeholder="Hospital Name" required />
          <input type="text" placeholder="Patient ID / Email" required />
          <input type="text" placeholder="Contact Number (for easy reach-out)" required />
          <input type="date" required />
          <input type="text" placeholder="Card Number" />
          <input type="file" accept="image/*,application/pdf" />
          <textarea rows="3" placeholder="Description of receipt or upload directly above"></textarea>
          <button type="submit">Submit Form</button>
        </form>
      </section>

    </div>

    <div class="content-section" id="rediology">
      <section class="form-section">
        <div style="display: flex; align-items: center; margin-bottom: 30px;">
          <img style="width: 100px; height: 100px; margin-right: 15px;" src="treelogo.jpg" alt="RX Medo Logo">
          <h3>🩺 Radiology Appointment Form</h3>
        </div>
        <h5>Please fill the form for booking an appointment</h5>

        <form id="radiologyForm" action="submit_rediology.php" method="POST" enctype="multipart/form-data">

          <input type="text" name="name" placeholder="Full Name" required />
          <input type="text" name="hospital" placeholder="Hospital Name" required />
          <input type="text" name="patient_id" placeholder="Patient ID / Email" required />
          <input type="text" name="contact" placeholder="Contact Number (for easy reach-out)" required />
          <input type="date" name="date" required />
          <input type="text" name="card_number" placeholder="Card Number" />

          <!-- ✅ Radiology Services Selection -->
          <label>Select Radiology Services:</label>
          <div class="checkbox-group" style="margin-bottom: 15px;">
            <label><input type="checkbox" name="services[]" value="CT Scan"> CT Scan</label>
            <label><input type="checkbox" name="services[]" value="3D/4D Ultrasound"> 3D/4D Ultrasound</label>
            <label><input type="checkbox" name="services[]" value="X-Ray"> X-Ray</label>
            <label><input type="checkbox" name="services[]" value="Echo"> Echo</label>
            <label><input type="checkbox" name="services[]" value="ECG"> ECG</label>
            <label><input type="checkbox" name="services[]" value="TMT"> TMT</label>
            <label><input type="checkbox" name="services[]" value="PFT"> PFT</label>
          </div>

          <label for="receipt">Please upload your prescription</label>
          <input type="file" id="receipt" name="receipt" accept="image/*,application/pdf" />

          <textarea name="description" rows="3"
            placeholder="Description of prescription or upload directly above"></textarea>

          <button type="submit">Submit Form</button>
        </form>
      </section>

    </div>


    <!-- PAYMENT -->
    <div id="payment" class="content-section">
      <div class="modern-card-section">
        <h2 class="fw-bold mb-3">Payment History</h2>
        <div id="paymentList"></div>
      </div>
    </div>

    <!-- DOCUMENTS -->
    <div id="documents" class="content-section">
      <div class="modern-card-section upload-section">
        <h2 class="fw-bold mb-3">Upload Documents</h2>
        <p>Upload and manage your Aadhaar and PAN card documents.</p>

        <form id="documentUploadForm">
          <div class="file-upload">
            <label for="aadhaarFront">Aadhaar Front:</label>
            <input type="file" id="aadhaarFront" accept="image/*,application/pdf" required>
            <img id="aadhaarFrontPreview" src="#" alt="Preview" style="display:none; max-width:100px; margin-top:5px;">
          </div>

          <div class="file-upload">
            <label for="aadhaarBack">Aadhaar Back:</label>
            <input type="file" id="aadhaarBack" accept="image/*,application/pdf" required>
            <img id="aadhaarBackPreview" src="#" alt="Preview" style="display:none; max-width:100px; margin-top:5px;">
          </div>

          <div class="file-upload">
            <label for="panCard">PAN Card:</label>
            <input type="file" id="panCard" accept="image/*,application/pdf" required>
            <img id="panCardPreview" src="#" alt="Preview" style="display:none; max-width:100px; margin-top:5px;">
          </div>

          <button type="submit" class="btn-upload">Upload Documents</button>


        </form>
      </div>

      <script>
        const previewFile = (inputId, previewId) => {
          const input = document.getElementById(inputId);
          const preview = document.getElementById(previewId);
          input.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
              const reader = new FileReader();
              reader.onload = e => {
                preview.src = e.target.result;
                preview.style.display = 'block';
              };
              reader.readAsDataURL(file);
            }
          });
        };

        previewFile('aadhaarFront', 'aadhaarFrontPreview');
        previewFile('aadhaarBack', 'aadhaarBackPreview');
        previewFile('panCard', 'panCardPreview');
      </script>

      <style>
        .upload-section {
          background: #fff;
          padding: 20px;
          border-radius: 12px;
          box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
          max-width: 700px;
          margin: 20px auto;
        }

        .upload-section .file-upload {
          margin-bottom: 15px;
        }

        .upload-section input[type="file"] {
          display: block;
          margin-top: 5px;
        }

        .btn-upload {
          background-color: #007bff;
          color: #fff;
          border: none;
          padding: 10px 18px;
          border-radius: 6px;
          cursor: pointer;
          transition: background 0.3s ease;
        }

        .btn-upload:hover {
          background-color: #0056b3;
        }
      </style>

    </div>

  </div>

  <!-- Services Popup Modal -->
  <div id="servicesModal" class="modal">
    <div class="modal-content">
      <span class="close-btn" onclick="closeServices()">&times;</span>
      <h2>RX Medo Card Services</h2>
      <ul class="services-list">
        <li>Discount upto 20–50% in Diagnostics and Laboratories</li>
        <li>Discount upto 20–50% in OPD</li>
        <li>Discount upto 20–22% in Pharmacy</li>
        <li>Discount upto 10–20% in Hospital Operations (Eye, ENT, Dental, etc.)</li>
        <li>Discount upto 10–20% in Short-Term Hospitalization</li>
        <li>Discount upto 10–20% on Domiciliary/Home Nurses</li>
        <li>Discount upto 10–20% on Purchase/Rental Medical Devices</li>
        <li>Discount upto 10–20% on Physio and Other Therapies</li>
        <li>Free OPD in Multiple Hospitals</li>
        <li>Free Delivery of Medicines & Free Ambulance</li>
        <li>Online Free E-Consultation</li>
      </ul>
    </div>
  </div>
  <style>
    /* Background Overlay */
    .modal {
      display: none;
      position: fixed;
      z-index: 9999;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.6);
      justify-content: center;
      align-items: center;
    }

    /* Modal Box */
    .modal-content {
      background: #fff;
      width: 90%;
      max-width: 450px;
      padding: 20px;
      border-radius: 10px;
      animation: popup 0.3s ease-out;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
    }

    @keyframes popup {
      from {
        transform: scale(0.8);
        opacity: 0;
      }

      to {
        transform: scale(1);
        opacity: 1;
      }
    }

    .close-btn {
      float: right;
      font-size: 26px;
      cursor: pointer;
    }

    .services-list {
      margin-top: 15px;
      padding-left: 20px;
    }

    .services-list li {
      margin: 8px 0;

      font-size: 15px;
    }
  </style>

  <!-- Insure Card Services Popup Modal -->
  <div id="insureServicesModal" class="modal">
    <div class="modal-content">
      <span class="close-btn" onclick="closeInsureServices()">&times;</span>

      <h2>🛡 Our Insure Services</h2>

      <h3>1. Young India (18–35 Years)</h3>
      <ul class="services-list">
        <li>✔ Waiver of First Year Exclusion</li>
        <li>✔ Waiver of 30 Days Waiting Period</li>
        <li>✔ Waiver of First Two Year Exclusion</li>
        <li>✔ Cover for Pre-Existing Diseases</li>
        <li>✔ Insurance Coverage of ₹5 Lakhs & ₹50 Lakhs Accidental</li>
        <li>✔ Free Online Consultation with Empanelled Doctors</li>
        <li>✔ Long-term discount upto 20% for 5 Years extension on this Insure Card</li>
      </ul>

      <h3>2. Matured Individuals (36–50 Years)</h3>
      <ul class="services-list">
        <li>✔ Add Family Members to This Card</li>
      </ul>

      <h3>3. Senior Citizens (50+ Years)</h3>
      <ul class="services-list">
        <li>✔ Free Home Medication Services</li>
      </ul>

      <p style="margin-top: 15px; font-weight: bold;">
        It also includes all the benefits of the RX Medo Card
      </p>

      <p style="font-size: 13px; opacity: 0.7;">
        * Not applicable for Corporate Insurance card holders
      </p>

    </div>
  </div>


  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- SECTION SWITCHING -->
  <script>
    function showInsureServices() {
      document.getElementById("insureServicesModal").style.display = "flex";
    }

    function closeInsureServices() {
      document.getElementById("insureServicesModal").style.display = "none";
    }

    window.onclick = function (e) {
      const modal = document.getElementById("insureServicesModal");
      if (e.target === modal) {
        closeInsureServices();
      }
    }
  </script>

  <script>
    function showServices() {
      document.getElementById("servicesModal").style.display = "flex";
    }

    function closeServices() {
      document.getElementById("servicesModal").style.display = "none";
    }

    // Close if clicked outside
    window.onclick = function (e) {
      if (e.target == document.getElementById("servicesModal")) {
        closeServices();
      }
    }
  </script>

  <script>
    function showSection(sectionId, btnEl) {
      // Hide all sections
      document.querySelectorAll('.content-section').forEach(section => {
        section.classList.remove('active');
      });

      // Show selected
      const target = document.getElementById(sectionId);
      if (target) {
        target.classList.add('active');
        // Scroll to top of main content (optional)
        document.querySelector('.main-content').scrollTop = 0;
      } else {
        console.warn('No section with id:', sectionId);
      }

      // Sidebar active highlight
      // remove active from all menu items
      document.querySelectorAll('.sidebar .menu-btn, .sidebar a').forEach(el => {
        el.classList.remove('active');
      });

      // add active to the clicked element (if provided)
      if (btnEl) btnEl.classList.add('active');
    }

    function confirmLogout() {
        if (confirm("Are you sure you want to logout?")) {
          window.location.href = "index.html"; // OK → Logout
          return false;
        }
        return false; // Cancel → Stay on same page
      }

    // Optionally show dashboard on load (it's already active in HTML, but this ensures highlight)
    document.addEventListener('DOMContentLoaded', function () {
      // highlight the first sidebar button (Profile)
      const firstBtn = document.querySelector('.sidebar .menu-btn');
      if (firstBtn) firstBtn.classList.add('active');
      // ensure dashboard visible
      showSection('dashboard', firstBtn);
    });
  </script>
  <script>
    document.querySelectorAll('.appointment-btn a').forEach(btn => {
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
          window.scrollTo({
            top: target.offsetTop - 20,
            behavior: 'smooth'
          });
        }
      });
    });
  </script>
  <script>
    function toggleSidebar() {
      let sidebar = document.getElementById("sidebar");
      let hamIcon = document.getElementById("hamIcon");

      sidebar.classList.toggle("open");

      if (sidebar.classList.contains("open")) {
        hamIcon.innerHTML = "✕"; // Close icon
      } else {
        hamIcon.innerHTML = "☰"; // Hamburger icon
      }
    }
  </script>



  <script src="script.js"></script>
</body>

</html>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>RX Medocard | Dashboard</title>
  <link rel="stylesheet" href="dashboard.css">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background: #f5f7fb;
      /* I removed overflow: hidden so scrolling works when sections grow */
      font-family: "Poppins", sans-serif;
    }

    /* Sidebar */
    .sidebar {
      width: 260px;
      height: 100vh;
      background: #0d6efd;
      color: white;
      position: fixed;
      left: 0;
      top: 0;
      padding: 25px 0;
      overflow-y: auto;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.15);
      z-index: 20;
    }

    .sidebar .logo-box {
      text-align: center;
      margin-bottom: 20px;
    }

    .sidebar .logo-box img {
      width: 120px;
      border-radius: 10px;
    }

    .sidebar a,
    .sidebar button.menu-btn {
      color: white;
      font-size: 15px;
      padding: 12px 20px;
      display: block;
      text-decoration: none;
      border-radius: 6px;
      margin: 6px 15px;
      transition: 0.2s;
      background: transparent;
      border: none;
      text-align: left;
      width: calc(100% - 30px);
      cursor: pointer;
    }

    .sidebar a:hover,
    .sidebar button.menu-btn:hover,
    .sidebar a.active,
    .sidebar button.menu-btn.active {
      background: rgba(255, 255, 255, 0.18);
    }

    /* Main Content */
    .main-content {
      margin-left: 260px;
      padding: 30px;
      min-height: 100vh;
      overflow-y: auto;
    }

    .content-section {
      display: none;
      animation: fade 0.3s ease-in-out;
    }

    .content-section.active {
      display: block;
    }

    @keyframes fade {
      from {
        opacity: 0;
        transform: translateY(10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Fade-in Animations (for the card display) */
    .fade-in {
      animation: fadeIn 0.6s ease forwards;
      opacity: 0;
    }

    .fade-in-delay-1 {
      animation: fadeIn 0.7s ease forwards;
      opacity: 0;
    }

    .fade-in-delay-2 {
      animation: fadeIn 0.9s ease forwards;
      opacity: 0;
    }

    .fade-in-delay-3 {
      animation: fadeIn 1.1s ease forwards;
      opacity: 0;
    }

    .fade-in-delay-4 {
      animation: fadeIn 1.3s ease forwards;
      opacity: 0;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Modern card section styling */
    .modern-card-section {
      background: white;
      padding: 25px;
      border-radius: 15px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.07);
      animation: fadeIn 0.3s ease-in-out;
      max-width: 1100px;
      margin-bottom: 20px;
    }

    .info-box {
      background: #eef4ff;
      padding: 15px;
      border-radius: 10px;
      margin-bottom: 15px;
    }

    .services-box {
      background: #f8f9fc;
      padding: 18px;
      border-radius: 12px;
      margin-top: 15px;
      border-left: 5px solid #0d6efd;
    }

    .services-box ul {
      list-style: none;
      padding-left: 0;
    }

    .services-box ul li {
      padding: 6px 0;
      font-size: 15px;
    }

    .modern-btn {
      display: inline-block;
      padding: 12px 20px;
      border-radius: 8px;
      font-weight: 600;
      text-decoration: none;
      transition: 0.3s ease;
    }

    .primary-btn {
      background: #0d6efd;
      color: white;
    }

    .primary-btn:hover {
      background: #084dbf;
    }

    .secondary-btn {
      background: #e8ecff;
      color: #0d6efd;
      margin-left: 10px;
    }

    .secondary-btn:hover {
      background: #cdd6ff;
    }

    .rotating-card-container {
      perspective: 1200px;
      margin-top: 20px;
      display: flex;
      justify-content: center;
    }

    .rotating-card {
      width: 320px;
      height: 200px;
      position: relative;
      transform-style: preserve-3d;
      animation: rotateCard 8s infinite linear;
    }

    .rotating-face {
      position: absolute;
      inset: 0;
      backface-visibility: hidden;
    }

    .rotating-back {
      transform: rotateY(180deg);
    }

    @keyframes rotateCard {
      from {
        transform: rotateY(0deg);
      }

      to {
        transform: rotateY(360deg);
      }
    }

    .card-image {
      width: 100%;
      height: 100%;
      border-radius: 12px;
      object-fit: cover;
    }

    /* small responsive tweaks */
    @media (max-width: 900px) {
      .sidebar {
        width: 220px;
      }

      .main-content {
        margin-left: 220px;
        padding: 20px;
      }

      .rotating-card {
        width: 260px;
        height: 160px;
      }
    }

    @media (max-width: 700px) {
      .sidebar {
        position: relative;
        width: 100%;
        height: auto;
      }

      .main-content {
        margin-left: 0;
        padding: 15px;
      }
    }

    .services-card-wrapper {
      display: flex;
      justify-content: space-between;
      gap: 40px;
      align-items: center;
      margin-top: 20px;
    }

    .modal-content {
      background: #fff;
      width: 90%;
      max-width: 450px;
      padding: 20px;
      border-radius: 10px;
      animation: popup 0.3s ease-out;

      /* FIXES */
      box-sizing: border-box;
      max-height: 85vh;
      overflow-y: auto;
      margin: 0 10px;
      /* Prevent side overflow */
    }

    /* ---------- Form Section ---------- */


    .form-section h3 {
      text-align: center;
      color: #00bfff;
      margin-bottom: 20px;
      font-size: 1.6em;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    input,
    textarea {
      padding: 10px;
      border: none;
      border-radius: 5px;
      outline: none;
      font-size: 0.95em;
    }

    input[type="file"] {
      color: #fff;
    }

    button[type="submit"] {
      background-color: #00bfff;
      color: #fff;
      border: none;
      padding: 12px;
      font-size: 1em;
      border-radius: 8px;
      cursor: pointer;
      transition: 0.3s;
    }

    button[type="submit"]:hover {
      background-color: #0077b6;
      box-shadow: 0 0 15px #00ffff;
    }
    /* ---------------- MOBILE HEADER ---------------- */
.mobile-header {
  display: none;
  justify-content: space-between;
  align-items: center;
  padding: 12px 15px;
  background: white;
  border-bottom: 1px solid #ddd;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  z-index: 2000;
}

.mobile-logo {
  height: 60px;
}

.hamburger {
  background: #1a65e6;
  color: #fff;
  font-size: 26px;
  border: none;
  padding: 8px 12px;
  border-radius: 6px;
  cursor: pointer;
}

/* ---------------- SIDEBAR ---------------- */
.sidebar {
  width: 250px;
  background: rgb(70, 141, 255);
  height: 100vh;
  position: fixed;
  left: 0;
  top: 0;
  padding-top: 20px;
  border-right: 1px solid #ddd;
  transition: left 0.3s ease;
  z-index: 1500;
}

/* ---------------- DESKTOP ONLY ---------------- */
@media (min-width: 769px) {
  .sidebar {
    left: 0 !important;   /* Always visible on desktop */
  }
  .mobile-header {
    display: none;        /* hidden on desktop */
  }
  
}
/* Prevent content from hiding under mobile header */
@media (max-width: 768px) {
  .main-content {
    margin-top: 70px !important; /* adjust as needed */
  }
}


/* ---------------- MOBILE ONLY ---------------- */
@media (max-width: 768px) {
  .mobile-header {
    display: flex;
  }
  

  .sidebar {
    left: -260px;         /* hide sidebar by default */
    top: 60px;            /* push below mobile header */
  }

  .sidebar.open {
    left: 0;              /* slide in */
  }
}
@media (max-width: 768px) {
  
  .services-card-wrapper {
    flex-direction: column;
    align-items: center;
   
  }
  .logo-box{
    display: none;
  }

  .services-list {
    width: 100%;
    padding-left: 0;
    font-size: 14px;
    margin-bottom: 15px;
  }

  .services-list li {
    margin-bottom: 8px;
    list-style: none;
  }

  .image-slider {
    width: 100%;
  }

  .image-slider img {
    max-width: 260px; /* Bigger for mobile */
    width: 90%;
  }

  .services-box h5 {
    text-align: center;
  }
}

@media (max-width: 768px) {
  .image-slider img {
    width: 100% !important;
    max-width: 300px !important; /* bigger for phone */
    height: auto !important;
    object-fit: contain !important; /* ensures image is NOT cropped */
    margin-left: 30px;
  }

  .image-slider {
    width: 100%;
    display: flex;
    justify-content: center;
  }
}
  </style>
</head>

<body>

  <!-- ==================== SIDEBAR ==================== -->
   
  <!-- MOBILE HEADER (only visible on phone) -->
  <div class="mobile-header">
    <img src="treelogo.jpg" class="mobile-logo" alt="Logo">
    <button class="hamburger" onclick="toggleSidebar()" id="hamIcon">☰</button>
  </div>
  
  <!-- SIDEBAR -->
  <div class="sidebar" id="sidebar">
  
    <div class="logo-box">
      <img src="treelogo.jpg" alt="RX Medocard Logo">
    </div>
  
    <button class="menu-btn" onclick="showSection('dashboard', this)">Profile</button>
    <button class="menu-btn" onclick="showSection('cards', this)">Card Details</button>
    <button class="menu-btn" onclick="showSection('membership', this)">Family Plans</button>
    <button class="menu-btn" onclick="showSection('hospitals', this)">Hospitals</button>
    <button class="menu-btn" onclick="showSection('pharmacy', this)">Pharmacy</button>
    <button class="menu-btn" onclick="showSection('diagnostic', this)">Diagnostic</button>
    <button class="menu-btn" onclick="showSection('pathology', this)">Pathology</button>
  
    <button class="menu-btn" onclick="showSection('rediology', this)">Rediology</button>
  
    <button class="menu-btn" onclick="showSection('OPD', this)">OPD</button>
    <button class="menu-btn" onclick="showSection('payment', this)">Payment History</button>
    <button class="menu-btn" onclick="showSection('documents', this)">Upload Documents</button>
  
    <button class="menu-btn btn btn-danger mt-3" style="margin: 0 15px;" onclick="return confirmLogout()">Logout</button>
  </div>

  <!-- ==================== MAIN CONTENT ==================== -->
  <div class="main-content">

    <!-- DASHBOARD (profile) -->
    <div id="dashboard" class="content-section active">
      <div class="modern-card-section">

        <h3 class="fade-in">Welcome,
          <?php echo htmlspecialchars($name); ?>
        </h3>

        <div class="info-box fade-in-delay-1">
          <p><strong>Selected Card:</strong>
            <?php echo htmlspecialchars($card_type); ?>
          </p>
          <p><strong>Type:</strong> Health Insurance</p>
          <p><strong>Validity:</strong>
            <?php echo $validityText; ?>
          </p>
        </div>

        <div class="button-group fade-in-delay-3">
          <a href="generate_card.php" class="modern-btn primary-btn">
            📥 Download RX Medo Card
          </a>

          <a href="download_receipt.php?order_id=<?php echo $orderId; ?>" class="modern-btn secondary-btn">
            📄 Download Receipt
          </a>
        </div>

        <div class="services-box fade-in-delay-2">

          <h5>Included Services</h5>

          <div class="services-card-wrapper">

            <!-- LEFT SIDE : Services -->
            <ul class="services-list">
              <li>✔ Waiver of First Year Exclusion</li>
              <li>✔ Waiver of 30 Days Waiting Period</li>
              <li>✔ Waiver of First Two Year Exclusion</li>
              <li>✔ Cover for Pre-Existing Diseases</li>
              <li>✔ ₹5 Lakh Insurance + ₹50 Lakh Accidental Cover</li>
              <li>✔ Free Online Consultation</li>
              <li>✔ Up to <b>20%</b> long-term renewal discount</li>
            </ul>

            <!-- RIGHT SIDE : Rotating Card -->
            <div class="image-slider">
              <img src="images/yellowfront.jpg" class="slide">
              <img src="images/yellowback.jpg" class="slide">
              <img src="images/bluefront.jpg" class="slide">
              <img src="images/blueback.jpg" class="slide">
            </div>

          </div>
        </div>
        <style>
          .image-slider {
            width: 350px;
            height: 220px;
            position: relative;
            overflow: hidden;
          }

          .slide {
            width: 100%;
            height: 100%;
            object-fit: cover;

            position: absolute;
            top: 0;
            left: 0;

            opacity: 0;
            animation: fadeSlide 12s infinite;
          }

          /* Image Timings */
          .slide:nth-child(1) {
            animation-delay: 0s;
          }

          .slide:nth-child(2) {
            animation-delay: 3s;
          }

          .slide:nth-child(3) {
            animation-delay: 6s;
          }

          .slide:nth-child(4) {
            animation-delay: 9s;
          }

          /* Fade Animation */
          @keyframes fadeSlide {
            0% {
              opacity: 0;
            }

            10% {
              opacity: 1;
            }

            25% {
              opacity: 1;
            }

            35% {
              opacity: 0;
            }

            100% {
              opacity: 0;
            }
          }
        </style>



      </div>
    </div>


    <!-- CARDS -->
    <div id="cards" class="content-section">
      <div id="cards" class="card-box dynamic-section">
        <h3>Available RX Medo Cards</h3>
        <p>You can select the other card that suits your needs:</p>



        <div class="card-grid">
          <!-- ✅ First Card with View Services Button -->
          <div class="card-item">
            <h4>RX Medo Card</h4>
            <p>Offer: ₹2500 (Original ₹3500)</p>
            <button onclick="showServices()" class="view-btn">View Services</button>
          </div>

          <!-- Other Cards -->
          <div class="card-item">
            <h4>RX Medo Insure Card</h4>
            <p>Coverage up to ₹5 Lakh</p>
            <button onclick="showInsureServices()" class="view-btn">View Services</button>
          </div>

          <div class="card-item">
            <h4>RX Insure Top Up Card</h4>
            <p>Complete protection</p>
          </div>
          <div class="card-item">
            <h4>RX Medo Insure Top-Up Card</h4>
            <p>Combo Plan</p>
          </div>
        </div>
        <div class="button-group">
          <!--<a href="process_payment.php" class="btn">Make Payment</a>-->
          <a href="yellowcardfront.jpg" class="modern-btn secondary-btn">Download Your Card</a>
          <a href="receipt.html" class="modern-btn secondary-btn">Download Your Receipt</a>
        </div>
      </div>

    </div>

    <!-- MEMBERSHIP -->
    <div id="membership" class="content-section">
      <div class="modern-card-section">
        <h2 class="fw-bold mb-3">Family Plans</h2>
        <ul>
          <li>Basic – ₹5,000/year (2 members)</li>
          <li>Standard – ₹10,000/year (4 members)</li>
          <li>Premium – ₹15,000/year (6 members + benefits)</li>
        </ul>
      </div>
    </div>

    <!-- HOSPITALS -->
    <div id="hospitals" class="content-section">
      <h2 style="text-align: center;">Our Hospital Network</h2>
      <div style="display: flex;gap: 10px;margin-top: 40px;" class="hsp">
        <!-- Hospital Cards -->
        <div class="hospital-cards-container row g-4">
          <!-- MD City Hospital -->
          <div class="col-lg-4 col-md-6">
            <div class="hospital-card">
              <div class="hospital-card-img-container">
                <img src="./images/MD.jpg" alt="MD City Hospital" class="hospital-card-img">
                <div class="hospital-card-overlay"></div>
                <div class="hospital-card-rating">
                  <i class="bi bi-star-fill"></i> 4.5
                </div>
              </div>
              <div class="hospital-card-content">
                <h3 class="hospital-card-title">MD City Hospital</h3>
                <div class="hospital-card-location">
                  <i class="bi bi-geo-alt-fill"></i> Sector 21, Noida, India
                </div>
                <p class="hospital-card-description">
                  Multi-specialty services and emergency care with priority access for Rx Medo Card holders.
                </p>
                <ul class="hospital-card-features">
                  <li>Multi-Specialty Care</li>
                  <li>Advanced Diagnostics</li>
                  <li>International Patients</li>
                </ul>
                <a href="https://www.google.com/search?q=MD+City+Hospital" target="_blank" class="hospital-card-btn">
                  View Location <i class="bi bi-arrow-right"></i>
                </a>
              </div>
            </div>
          </div>

          <!-- Sarvodaya Hospitals -->
          <div class="col-lg-4 col-md-6">
            <div class="hospital-card">
              <div class="hospital-card-img-container">
                <img src="./images/sarvodya(greater).jpg" alt="Sarvodaya Hospital" class="hospital-card-img">
                <div class="hospital-card-overlay"></div>
                <div class="hospital-card-rating">
                  <i class="bi bi-star-fill"></i> 4.4
                </div>
              </div>
              <div class="hospital-card-content">
                <h3 class="hospital-card-title">Sarvodaya Hospital</h3>
                <div class="hospital-card-location">
                  <i class="bi bi-geo-alt-fill"></i> Greater Noida, India
                </div>
                <p class="hospital-card-description">
                  Advanced medical facilities and experienced staff offering seamless care.
                </p>
                <ul class="hospital-card-features">
                  <li>Multi-Specialty Care</li>
                  <li>Emergency Services</li>
                  <li>International Patients</li>
                </ul>
                <a href="https://www.google.com/search?q=sarvodaya+hospital" target="_blank" class="hospital-card-btn">
                  View Location <i class="bi bi-arrow-right"></i>
                </a>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6">
            <div class="hospital-card">
              <div class="hospital-card-img-container">
                <img src="./images/sorvodya-hospital.jpg" alt="Sarvodaya Hospital Sector 8" class="hospital-card-img">
                <div class="hospital-card-overlay"></div>
                <div class="hospital-card-rating">
                  <i class="bi bi-star-fill"></i> 4.4
                </div>
              </div>
              <div class="hospital-card-content">
                <h3 class="hospital-card-title">Sarvodaya Hospital</h3>
                <div class="hospital-card-location">
                  <i class="bi bi-geo-alt-fill"></i> Sector 8, Faridabad
                </div>
                <p class="hospital-card-description">
                  A leading healthcare center in Faridabad, known for quality patient care and advanced medical
                  facilities.
                </p>
                <ul class="hospital-card-features">
                  <li>Multi-Specialty Care</li>
                  <li>Emergency Services</li>
                  <li>International Patients</li>
                </ul>
                <a href="https://www.google.com/search?q=sarvodaya+hospital" target="_blank" class="hospital-card-btn">
                  View Location <i class="bi bi-arrow-right"></i>
                </a>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6">
            <div class="hospital-card">
              <div class="hospital-card-img-container">
                <img src="./images/faridabad.jpg" alt="Sarvodaya Hospital Sector 19" class="hospital-card-img">
                <div class="hospital-card-overlay"></div>
                <div class="hospital-card-rating">
                  <i class="bi bi-star-fill"></i> 4.4
                </div>
              </div>
              <div class="hospital-card-content">
                <h3 class="hospital-card-title">Sarvodaya Hospital</h3>
                <div class="hospital-card-location">
                  <i class="bi bi-geo-alt-fill"></i> Sector 19, Faridabad
                </div>
                <p class="hospital-card-description">
                  A leading healthcare center in Faridabad, known for quality patient care and advanced medical
                  facilities.
                </p>
                <ul class="hospital-card-features">
                  <li>Multi-Specialty Care</li>
                  <li>Emergency Services</li>
                  <li>International Patients</li>
                </ul>
                <a href="https://www.google.com/search?q=sarvodaya+hospital" target="_blank" class="hospital-card-btn">
                  View Location <i class="bi bi-arrow-right"></i>
                </a>
              </div>
            </div>
          </div>

          <!-- Felix Hospitals -->
          <div class="col-lg-4 col-md-6">
            <div class="hospital-card">
              <div class="hospital-card-img-container">
                <img src="./images/felix.jpg" alt="Felix Hospital Noida" class="hospital-card-img">
                <div class="hospital-card-overlay"></div>
                <div class="hospital-card-rating">
                  <i class="bi bi-star-fill"></i> 4.5
                </div>
              </div>
              <div class="hospital-card-content">
                <h3 class="hospital-card-title">Felix Hospital</h3>
                <div class="hospital-card-location">
                  <i class="bi bi-geo-alt-fill"></i> Sector-137, Noida
                </div>
                <p class="hospital-card-description">
                  Comprehensive healthcare services with modern infrastructure and support.
                </p>
                <ul class="hospital-card-features">
                  <li>Multi-Specialty Care</li>
                  <li>Advanced Diagnostics</li>
                  <li>International Patients</li>
                </ul>
                <a href="https://www.google.com/search?q=Felix+Hospital+Noida" target="_blank"
                  class="hospital-card-btn">
                  View Location <i class="bi bi-arrow-right"></i>
                </a>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6">
            <div class="hospital-card">
              <div class="hospital-card-img-container">
                <img src="./images/felix(greater).jpg" alt="Felix Hospital Greater Noida" class="hospital-card-img">
                <div class="hospital-card-overlay"></div>
                <div class="hospital-card-rating">
                  <i class="bi bi-star-fill"></i> 4.5
                </div>
              </div>
              <div class="hospital-card-content">
                <h3 class="hospital-card-title">Felix Hospital</h3>
                <div class="hospital-card-location">
                  <i class="bi bi-geo-alt-fill"></i> Greater Noida West
                </div>
                <p class="hospital-card-description">
                  Comprehensive healthcare services with modern infrastructure and support.
                </p>
                <ul class="hospital-card-features">
                  <li>Multi-Specialty Care</li>
                  <li>Advanced Diagnostics</li>
                  <li>International Patients</li>
                </ul>
                <a href="https://www.google.com/search?q=Felix+Hospital+Noida" target="_blank"
                  class="hospital-card-btn">
                  View Location <i class="bi bi-arrow-right"></i>
                </a>
              </div>
            </div>
          </div>

          <!-- Max Hospital -->
          <div class="col-lg-4 col-md-6">
            <div class="hospital-card">
              <div class="hospital-card-img-container">
                <img src="./images/max.webp" alt="Max Hospital" class="hospital-card-img">
                <div class="hospital-card-overlay"></div>
                <div class="hospital-card-rating">
                  <i class="bi bi-star-fill"></i> 4.2
                </div>
              </div>
              <div class="hospital-card-content">
                <h3 class="hospital-card-title">Max Hospital</h3>
                <div class="hospital-card-location">
                  <i class="bi bi-geo-alt-fill"></i> Saket, New Delhi
                </div>
                <p class="hospital-card-description">
                  Specialized treatments and priority appointments for Rx Medo Card members.
                </p>
                <ul class="hospital-card-features">
                  <li>Multi-Specialty Care</li>
                  <li>Advanced Diagnostics</li>
                  <li>International Patients</li>
                </ul>
                <a href="https://www.google.com/search?q=Max+Hospital+Saket+Delhi" target="_blank"
                  class="hospital-card-btn">
                  View Location <i class="bi bi-arrow-right"></i>
                </a>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

    <style>
      .hospital-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: transform 0.3s, box-shadow 0.3s;
      }

      .hospital-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
      }

      .hospital-card-img-container {
        position: relative;
        width: 100%;
        height: 200px;
        overflow: hidden;
      }

      .hospital-card-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
      }

      .hospital-card-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0));
      }

      .hospital-card-rating {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #ffd700;
        padding: 5px 10px;
        border-radius: 8px;
        font-weight: bold;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 5px;
      }

      .hospital-card-content {
        padding: 20px;
      }

      .hospital-card-title {
        font-size: 1.3rem;
        margin-bottom: 8px;
        color: #333;
      }

      .hospital-card-location {
        font-size: 0.9rem;
        margin-bottom: 10px;
        color: #777;
        display: flex;
        align-items: center;
        gap: 5px;
      }

      .hospital-card-description {
        font-size: 0.95rem;
        color: #555;
        margin-bottom: 12px;
      }

      .hospital-card-features {
        list-style: none;
        padding: 0;
        margin-bottom: 15px;
      }

      .hospital-card-features li {
        font-size: 0.85rem;
        color: #555;
        margin-bottom: 5px;
        position: relative;
        padding-left: 15px;
      }

      .hospital-card-features li::before {
        content: "✔";
        position: absolute;
        left: 0;
        color: #28a745;
      }

      .hospital-card-btn {
        display: inline-block;
        padding: 8px 16px;
        background: #007bff;
        color: #fff;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 500;
        transition: background 0.3s;
      }

      .hospital-card-btn:hover {
        background: #0056b3;
      }

      .card-item {
        border: 2px solid blue;
      }

      .btn {
        border: 2px solid blue;
      }

      @media (max-width:800px) {
        .hsp {
          display: flex;
          flex-direction: column;
        }

      }
    </style>



    <div id="pharmacy" class="content-section">
      <section class="hospital-info">
        <img
          src="https://media.istockphoto.com/photos/modern-hospital-building-picture-id1312706413?b=1&k=20&m=1312706413&s=170667a&w=0&h=VRi3w2E1UqvCCcK-nDV6mH7FhDZoTU9MM2QKSom96X4="
          alt="Hospital Image" />
        <div class="info-text">
          <h2>About Our Pharmacy Services</h2>
          <p>We connect you with top hospitals in the city offering the best outpatient (Pharmacy) care with exclusive
            discounts and free checkups for a limited time.</p>
          <div class="discount">
            🎉 <strong>Special Offer:</strong> Get <b>20% OFF</b> on select hospital Pharmacy Consultations this week!
          </div> <br>
          <button class="appointment-btn">
            <a href="#pharmacy-form">Book Appointment</a>
          </button>

        </div>
      </section>

      <!-- Recommended Hospitals -->
      <section class="recommended">
        <h2>🏥 Our Best Recommendations for You</h2>
        <div class="hospital-grid">

          <!-- MD City Hospital -->
          <div class="hospital-box">
            <img style="height: 250px;" src="./images/MD.jpg" alt="MD City Hospital" />
            <h3>MD City Hospital</h3>
            <p><b>Address:</b> Sector 21,Noida,India</p>
            <p>Top-rated multi-speciality hospital providing Pharmacy consultation this month.</p>
            <div class="offer">20% OFF on pharmacy</div>
          </div>

          <!-- Sarvodaya Hospital -->
          <div class="hospital-box">
            <img style="height: 250px;" src="./images/sorvodya-hospital.jpg" alt="Sarvodaya Hospital" />
            <h3>Sarvodaya Hospital</h3>
            <p><b>Address:</b> Greater Noida,India</p>
            <p>Providing expert care and modern facilities. Enjoy a special limited-time Pharmacy discount.</p>
            <div class="offer">🔥 20% OFF on Pharmacy</div>
          </div>

          <!-- Felix Hospital -->
          <div class="hospital-box">
            <img style="height: 250px;" src="./images/felix.jpg" alt="Felix Hospital" />
            <h3>Felix Hospital</h3>
            <p><b>Address:</b> Sector 137, Noida, Uttar Pradesh 201305</p>
            <p>Comprehensive healthcare services with modern Pharmacy facilities and experienced doctors.</p>
            <div class="offer">💰 20% OFF on Pharmacy</div>
          </div>
        </div>
      </section>

      <section id="opd" class="contact">
        <h2>📞 Contact Us</h2>
        <a href="tel:+919999307517"><button class="call-btn">Call Us Now</button></a>
      </section>

      <section class="form-section" id="pharmacy-form">
        <h3 style="text-align: center;">🩺 Pharmacy Appointment Form</h3>
        <form id="opdForm">
          <input type="text" placeholder="Full Name" required />
          <input type="text" placeholder="Hospital Name" required />
          <input type="text" placeholder="Patient ID / Email" required />
          <input type="text" placeholder="Contact Number (for easy reach-out)" required />
          <input type="date" required />
          <input type="text" placeholder="Card Number" />
          <input type="file" accept="image/*,application/pdf" />
          <textarea rows="3" placeholder="Description of receipt or upload directly above"></textarea>
          <button type="submit">Submit Form</button>
        </form>
      </section>




    </div>

    <!-- DIAGNOSTIC -->
    <div id="diagnostic" class="content-section">
      <section class="hospital-info">
        <img
          src="https://media.istockphoto.com/photos/modern-hospital-building-picture-id1312706413?b=1&k=20&m=1312706413&s=170667a&w=0&h=VRi3w2E1UqvCCcK-nDV6mH7FhDZoTU9MM2QKSom96X4="
          alt="Hospital Image" class="hospital-img" />

        <div class="info-text">
          <h2>About Our Diagnostic Services</h2>
          <p>
            We connect you with top hospitals in the city offering the best outpatient diagnostic care with exclusive
            discounts and free checkups for a limited time.
          </p>
          <div class="discount">
            🎉 <strong>Special Offer:</strong> Get <b>20% OFF</b> on select hospital Diagnostic Consultations this week!
          </div> <br>
          <button class="appointment-btn">
            <a href="#diagnostic-form">Book Appointment</a>
          </button>


        </div>

        <div class="radiology-box">
          <h1>About Radiology</h1>
          <p>
            Radiology is the medical specialty that uses imaging techniques such as X-rays, CT scans, and MRIs to
            diagnose and
            treat diseases.
          </p>
          <button class="menu-btn" onclick="showSection('rediology', this)">Rediology</button>
        </div>
      </section>
      <script>
        const btn = document.getElementById("radiology-btn");

        btn.addEventListener("click", () => {
          btn.classList.add("clicked");

          setTimeout(() => {
            window.location.href = "rediologydash.html"; // Redirects to the page
          }, 1000);
        });
      </script>



      <style>
        .hospital-info {
          display: flex;
          flex-wrap: wrap;
          justify-content: center;
          align-items: center;
          padding: 40px 20px;
          gap: 40px;

          border-radius: 20px;
          box-shadow: 0 0 40px rgba(0, 191, 255, 0.2);
          transition: transform 0.3s ease;
        }

        .hospital-info:hover {
          transform: scale(1.01);
        }

        .hospital-info img {
          max-width: 400px;
          width: 100%;
          border-radius: 15px;
          box-shadow: 0 0 30px #00bfff;
          transition: transform 0.5s ease, box-shadow 0.3s ease;
        }

        .hospital-info img:hover {
          transform: scale(1.05);
          box-shadow: 0 0 40px #00ffff;
        }

        .info-text {
          max-width: 500px;
          animation: fadeIn 1.2s ease;
        }

        .info-text h2 {
          color: #00bfff;
          font-size: 1.8rem;
          margin-bottom: 10px;
        }

        .discount {
          background-color: rgba(255, 255, 255, 0.1);
          padding: 15px;
          margin-top: 15px;
          border-left: 4px solid #00bfff;
          animation: glow 2s infinite alternate;
          border-radius: 10px;
        }

        .radiology-box {
          background: rgba(255, 255, 255, 0.05);
          padding: 25px;
          border-radius: 15px;
          box-shadow: 0 0 20px rgba(0, 191, 255, 0.2);
          max-width: 450px;
          text-align: center;
          transition: transform 0.3s ease;
          animation: fadeInUp 1.2s ease;
        }

        .radiology-box:hover {
          transform: translateY(-10px);
        }

        .radiology-box h1 {
          color: #00bfff;
          margin-bottom: 10px;
        }

        .radiology-box button {
          background: linear-gradient(90deg, #00bfff, #00ffff);
          border: none;
          color: #000;
          padding: 12px 25px;
          border-radius: 30px;
          cursor: pointer;
          font-weight: bold;
          transition: 0.3s ease;
        }

        .radiology-box button:hover {
          transform: scale(1.1);
          box-shadow: 0 0 20px #00ffff;
        }

        .radiology-box button.clicked {
          background: linear-gradient(90deg, #00ffff, #00bfff);
          opacity: 0.8;
        }

        @keyframes glow {
          from {
            box-shadow: 0 0 10px #00bfff;
          }

          to {
            box-shadow: 0 0 30px #00ffff;
          }
        }

        @keyframes fadeIn {
          from {
            opacity: 0;
            transform: translateY(20px);
          }

          to {
            opacity: 1;
            transform: translateY(0);
          }
        }

        @keyframes fadeInUp {
          from {
            opacity: 0;
            transform: translateY(50px);
          }

          to {
            opacity: 1;
            transform: translateY(0);
          }
        }

        @media (max-width: 768px) {
          .hospital-info {
            text-align: center;
            gap: 30px;
          }

          .hospital-info img {
            max-width: 100%;
          }

          .radiology-box {
            width: 100%;
          }
        }
      </style>


      <!-- Recommended Hospitals -->
      <section class="recommended">
        <h2>🏥 Our Best Recommendations for You</h2>
        <div class="hospital-grid">

          <!-- MD City Hospital -->
          <div class="hospital-box">
            <img style="height: 250px;" src="./images/MD.jpg" alt="MD City Hospital" />
            <h3>MD City Hospital</h3>
            <p><b>Address:</b> Sector 21,Noida,India</p>
            <p>Top-rated multi-speciality hospital providing Diagnostic consultation this month.</p>
            <div class="offer">💙 30% OFF on Diagnostic</div>
          </div>

          <!-- Sarvodaya Hospital -->
          <div class="hospital-box">
            <img style="height: 250px;" src="./images/sorvodya-hospital.jpg" alt="Sarvodaya Hospital" />
            <h3>Sarvodaya Hospital</h3>
            <p><b>Address:</b>Greater Noida,India</p>
            <p>Providing expert care and modern facilities. Enjoy a special limited-time Diagnostic discount.</p>
            <div class="offer">🔥 20% OFF on Diagnostic</div>
          </div>

          <!-- Felix Hospital -->
          <div class="hospital-box">
            <img style="height: 250px;" src="./images/felix.jpg" alt="Felix Hospital" />
            <h3>Felix Hospital</h3>
            <p><b>Address:</b> Sector 137, Noida, Uttar Pradesh 201305</p>
            <p>Comprehensive healthcare services with modern Diagnostic facilities and experienced doctors.</p>
            <div class="offer">💰 20% Discount on Diagnostic</div>
          </div>
        </div>
      </section>

      <section class="contact" id="diagnostic">
        <h2>📞 Contact Us</h2>
        <a href="tel:+919999307517"><button class="call-btn">Call Us Now</button></a>
        <!-- <h3 style="font-size: 25px;">🩺 Diagnostic Appointment Form</h3> -->
      </section>

      <section class="form-section" id="diagnostic-form">
        <div style="display: flex;margin-bottom: 30px;">
          <img style="width: 100px;height: 100px;justify-content: center;" src="./images/logo.webp" alt="">
          <p style="text-align: center;margin-left: 50px;font-size: 20px;">Please fill the form for book your
            appointment</p>

        </div>

        <form id="opdForm">
          <input type="text" placeholder="Full Name" required />
          <input type="text" placeholder="Hospital Name" required />
          <input type="text" placeholder="Patient ID / Email" required />
          <input type="text" placeholder="Contact Number (for easy reach-out)" required />
          <input type="date" required />
          <input type="text" placeholder="Card Number" />
          <input type="file" accept="image/*,application/pdf" />
          <textarea rows="3" placeholder="Description of receipt or upload directly above"></textarea>
          <button type="submit">Submit Form</button>
        </form>
      </section>



      <script>
        document.getElementById("opdForm").addEventListener("submit", (e) => {
          e.preventDefault();
          alert("✅ Form Submitted Successfully! Thank you for booking your OPD appointment.");
          e.target.reset();
        });
      </script>


    </div>

    <!-- pathology section -->
    <div id="pathology" class="content-section">
      <section class="hospital-info">
        <img
          src="https://media.istockphoto.com/photos/modern-hospital-building-picture-id1312706413?b=1&k=20&m=1312706413&s=170667a&w=0&h=VRi3w2E1UqvCCcK-nDV6mH7FhDZoTU9MM2QKSom96X4="
          alt="Hospital Image" />
        <div class="info-text">
          <h2>About Our Pathology Services</h2>
          <p>We connect you with top hospitals in the city offering the best outpatient Pathology care with exclusive
            discounts and free checkups for a limited time.</p>
          <div class="discount">
            🎉 <strong>Special Offer:</strong> Get <b>20% OFF</b> on select hospital Pathology Consultations this
            week!
          </div> <br>
          <button class="appointment-btn">
            <a href="#pathology-form">Book Appointment</a>
          </button>
        </div>
      </section>

      <!-- Recommended Hospitals -->
      <section class="recommended">
        <h2>🏥 Our Best Recommendations for You</h2>
        <div class="hospital-grid">

          <!-- MD City Hospital -->
          <div class="hospital-box">
            <img style="height: 250px;" src="./images/MD.jpg" alt="MD City Hospital" />
            <h3>MD City Hospital</h3>
            <p><b>Address:</b> Sector 21,Noida,India</p>
            <p>Top-rated multi-speciality hospital providing Pathology consultation this month.</p>
            <div class="offer">💙 30% OFF on Pathology</div>
          </div>

          <!-- Sarvodaya Hospital -->
          <div class="hospital-box">
            <img style="height: 250px;" src="./images/sorvodya-hospital.jpg" alt="Sarvodaya Hospital" />
            <h3>Sarvodaya Hospital</h3>
            <p><b>Address:</b>Greater Noida,India</p>
            <p>Providing expert care and modern facilities. Enjoy a special limited-time Pathology discount.</p>
            <div class="offer">🔥 20% OFF on Pathology</div>
          </div>

          <!-- Felix Hospital -->
          <div class="hospital-box">
            <img style="height: 250px;" src="./images/felix.jpg" alt="Felix Hospital" />
            <h3>Felix Hospital</h3>
            <p><b>Address:</b> Sector 137, Noida, Uttar Pradesh 201305</p>
            <p>Comprehensive healthcare services with modern Pathology facilities and experienced doctors.</p>
            <div class="offer">💰 20% Discount on Pathology</div>
          </div>

        </div>
      </section>

      <section class="contact">
        <h2>📞 Contact Us</h2>
        <a href="tel:+919999307517"><button class="call-btn">Call Us Now</button></a>
      </section>

      <section class="form-section" id="pathology-form">
        <h3>🩺 Pathology Appointment Form</h3>
        <form id="opdForm">
          <input type="text" placeholder="Full Name" required />
          <input type="text" placeholder="Hospital Name" required />
          <input type="text" placeholder="Patient ID / Email" required />
          <input type="text" placeholder="Contact Number (for easy reach-out)" required />
          <input type="date" required />
          <input type="text" placeholder="Card Number" />
          <input type="file" accept="image/*,application/pdf" />
          <textarea rows="3" placeholder="Description of receipt or upload directly above"></textarea>
          <button type="submit">Submit Form</button>
        </form>
      </section>

    </div>

    <div id="rediology" class="content-section">
      <section class="hospital-info">
        <img src="https://media.istockphoto.com/photos/modern-hospital-building-picture-id1312706413?b=1&k=20&m=1312706413&s=170667a&w=0&h=VRi3w2E1UqvCCcK-nDV6mH7FhDZoTU9MM2QKSom96X4="
            alt="Hospital Image" />
        <div class="info-text">
            <h2>About Our Rediology Services</h2>
            <p>We connect you with top hospitals in the city offering the best outpatient Rediology care with exclusive
                discounts and free checkups for a limited time.</p>
            <div class="discount">
                🎉 <strong>Special Offer:</strong> Get <b>20% OFF</b> on select hospital Rediology Consultations this
                week!
            </div>
        </div>
    </section>

    <!-- Recommended Hospitals -->
    <section class="recommended">
        <h2>🏥 Our Best Recommendations for You</h2>
        <div class="hospital-grid">

            <!-- MD City Hospital -->
            <div class="hospital-box">
                <img style="height: 250px;" src="./images/MD.jpg" alt="MD City Hospital" />
                <h3>MD City Hospital</h3>
                <p><b>Address:</b> Sector 21,Noida,India</p>
                <p>Top-rated multi-speciality hospital providing Rediology consultation this month.</p>
                <div class="offer">💙 30% OFF on Rediology</div>
            </div>

            <!-- Sarvodaya Hospital -->
            <div class="hospital-box">
                <img style="height: 250px;" src="./images/sorvodya-hospital.jpg" alt="Sarvodaya Hospital" />
                <h3>Sarvodaya Hospital</h3>
                <p><b>Address:</b>Greater Noida,India</p>
                <p>Providing expert care and modern facilities. Enjoy a special limited-time Rediology discount.</p>
                <div class="offer">🔥 20% OFF on Pathology</div>
            </div>

            <!-- Felix Hospital -->
            <div class="hospital-box">
                <img style="height: 250px;" src="./images/felix.jpg" alt="Felix Hospital" />
                <h3>Felix Hospital</h3>
                <p><b>Address:</b> Sector 137, Noida, Uttar Pradesh 201305</p>
                <p>Comprehensive healthcare services with modern Rediology facilities and experienced doctors.</p>
                <div class="offer">💰 20% Discount on Rediology</div>
            </div>

        </div>
    </section>

    <section class="contact">
        <h2>📞 Contact Us</h2>
        <a href="tel:+919999307517"><button class="call-btn">Call Us Now</button></a>
    </section>

    <section class="form-section">
        <h3>🩺 Rediology Appointment Form</h3>
        <form id="opdForm">
            <input type="text" placeholder="Full Name" required />
            <input type="text" placeholder="Hospital Name" required />
            <input type="text" placeholder="Patient ID / Email" required />
            <input type="text" placeholder="Contact Number (for easy reach-out)" required />
            <input type="date" required />
            <input type="text" placeholder="Card Number" />
            <input type="file" accept="image/*,application/pdf" />
            <textarea rows="3" placeholder="Description of receipt or upload directly above"></textarea>
            <button type="submit">Submit Form</button>
        </form>
    </section>

    </div>

    <!-- opd -->
    <div id="OPD" class="content-section">
      <section class="hospital-info">
        <img
          src="https://media.istockphoto.com/photos/modern-hospital-building-picture-id1312706413?b=1&k=20&m=1312706413&s=170667a&w=0&h=VRi3w2E1UqvCCcK-nDV6mH7FhDZoTU9MM2QKSom96X4="
          alt="Hospital Image" />
        <div class="info-text">
          <h2>About Our OPD Services</h2>
          <p>We connect you with top hospitals in the city offering the best outpatient (OPD) care with exclusive
            discounts and free checkups for a limited time.</p>
          <div class="discount">
            🎉 <strong>Special Offer:</strong> Get <b>20% OFF</b> on select hospital OPD Consultations this week!
          </div><br>
          <button class="appointment-btn">
            <a href="#opd-form">Book Appointment</a>
          </button>
        </div>
      </section>

      <!-- Recommended Hospitals -->
      <section class="recommended">
        <h2>🏥 Our Best Recommendations for You</h2>
        <div class="hospital-grid">

          <!-- MD City Hospital -->
          <div class="hospital-box">
            <img style="height: 250px;" src="./images/MD.jpg" alt="MD City Hospital" />
            <h3>MD City Hospital</h3>
            <p><b>Address:</b> Sector 21,Noida,India</p>
            <p>Top-rated multi-speciality hospital providing free OPD consultation this month.</p>
            <div class="offer">💙 Free OPD Consultation</div>
          </div>

          <!-- Sarvodaya Hospital -->
          <div class="hospital-box">
            <img style="height: 250px;" src="./images/sorvodya-hospital.jpg" alt="Sarvodaya Hospital" />
            <h3>Sarvodaya Hospital</h3>
            <p><b>Address:</b> Greater Noida,India</p>
            <p>Providing expert care and modern facilities. Enjoy a special limited-time OPD discount.</p>
            <div class="offer">🔥 20% OFF on OPD</div>
          </div>

          <!-- Felix Hospital -->
          <div class="hospital-box">
            <img style="height: 250px;" src="./images/felix.jpg" alt="Felix Hospital" />
            <h3>Felix Hospital</h3>
            <p><b>Address:</b> Sector 137, Noida, Uttar Pradesh 201305</p>
            <p>Comprehensive healthcare services with modern OPD facilities and experienced doctors.</p>
            <div class="offer">💰 20% Discount on OPD</div>
          </div>

        </div>
      </section>

      <section class="contact" id="opd-form">
        <h2>📞 Contact Us</h2>
        <a href="tel:+919999307517"><button class="call-btn">Call Us Now</button></a>
      </section>

      <section class="form-section">
        <h3>🩺 OPD Appointment Form</h3>
        <form id="opdForm">
          <input type="text" placeholder="Full Name" required />
          <input type="text" placeholder="Hospital Name" required />
          <input type="text" placeholder="Patient ID / Email" required />
          <input type="text" placeholder="Contact Number (for easy reach-out)" required />
          <input type="date" required />
          <input type="text" placeholder="Card Number" />
          <input type="file" accept="image/*,application/pdf" />
          <textarea rows="3" placeholder="Description of receipt or upload directly above"></textarea>
          <button type="submit">Submit Form</button>
        </form>
      </section>

    </div>

    <div class="content-section" id="rediology">
      <section class="form-section">
        <div style="display: flex; align-items: center; margin-bottom: 30px;">
          <img style="width: 100px; height: 100px; margin-right: 15px;" src="treelogo.jpg" alt="RX Medo Logo">
          <h3>🩺 Radiology Appointment Form</h3>
        </div>
        <h5>Please fill the form for booking an appointment</h5>

        <form id="radiologyForm" action="submit_rediology.php" method="POST" enctype="multipart/form-data">

          <input type="text" name="name" placeholder="Full Name" required />
          <input type="text" name="hospital" placeholder="Hospital Name" required />
          <input type="text" name="patient_id" placeholder="Patient ID / Email" required />
          <input type="text" name="contact" placeholder="Contact Number (for easy reach-out)" required />
          <input type="date" name="date" required />
          <input type="text" name="card_number" placeholder="Card Number" />

          <!-- ✅ Radiology Services Selection -->
          <label>Select Radiology Services:</label>
          <div class="checkbox-group" style="margin-bottom: 15px;">
            <label><input type="checkbox" name="services[]" value="CT Scan"> CT Scan</label>
            <label><input type="checkbox" name="services[]" value="3D/4D Ultrasound"> 3D/4D Ultrasound</label>
            <label><input type="checkbox" name="services[]" value="X-Ray"> X-Ray</label>
            <label><input type="checkbox" name="services[]" value="Echo"> Echo</label>
            <label><input type="checkbox" name="services[]" value="ECG"> ECG</label>
            <label><input type="checkbox" name="services[]" value="TMT"> TMT</label>
            <label><input type="checkbox" name="services[]" value="PFT"> PFT</label>
          </div>

          <label for="receipt">Please upload your prescription</label>
          <input type="file" id="receipt" name="receipt" accept="image/*,application/pdf" />

          <textarea name="description" rows="3"
            placeholder="Description of prescription or upload directly above"></textarea>

          <button type="submit">Submit Form</button>
        </form>
      </section>

    </div>


    <!-- PAYMENT -->
    <div id="payment" class="content-section">
      <div class="modern-card-section">
        <h2 class="fw-bold mb-3">Payment History</h2>
        <div id="paymentList"></div>
      </div>
    </div>

    <!-- DOCUMENTS -->
    <div id="documents" class="content-section">
      <div class="modern-card-section upload-section">
        <h2 class="fw-bold mb-3">Upload Documents</h2>
        <p>Upload and manage your Aadhaar and PAN card documents.</p>

        <form id="documentUploadForm">
          <div class="file-upload">
            <label for="aadhaarFront">Aadhaar Front:</label>
            <input type="file" id="aadhaarFront" accept="image/*,application/pdf" required>
            <img id="aadhaarFrontPreview" src="#" alt="Preview" style="display:none; max-width:100px; margin-top:5px;">
          </div>

          <div class="file-upload">
            <label for="aadhaarBack">Aadhaar Back:</label>
            <input type="file" id="aadhaarBack" accept="image/*,application/pdf" required>
            <img id="aadhaarBackPreview" src="#" alt="Preview" style="display:none; max-width:100px; margin-top:5px;">
          </div>

          <div class="file-upload">
            <label for="panCard">PAN Card:</label>
            <input type="file" id="panCard" accept="image/*,application/pdf" required>
            <img id="panCardPreview" src="#" alt="Preview" style="display:none; max-width:100px; margin-top:5px;">
          </div>

          <button type="submit" class="btn-upload">Upload Documents</button>


        </form>
      </div>

      <script>
        const previewFile = (inputId, previewId) => {
          const input = document.getElementById(inputId);
          const preview = document.getElementById(previewId);
          input.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
              const reader = new FileReader();
              reader.onload = e => {
                preview.src = e.target.result;
                preview.style.display = 'block';
              };
              reader.readAsDataURL(file);
            }
          });
        };

        previewFile('aadhaarFront', 'aadhaarFrontPreview');
        previewFile('aadhaarBack', 'aadhaarBackPreview');
        previewFile('panCard', 'panCardPreview');
      </script>

      <style>
        .upload-section {
          background: #fff;
          padding: 20px;
          border-radius: 12px;
          box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
          max-width: 700px;
          margin: 20px auto;
        }

        .upload-section .file-upload {
          margin-bottom: 15px;
        }

        .upload-section input[type="file"] {
          display: block;
          margin-top: 5px;
        }

        .btn-upload {
          background-color: #007bff;
          color: #fff;
          border: none;
          padding: 10px 18px;
          border-radius: 6px;
          cursor: pointer;
          transition: background 0.3s ease;
        }

        .btn-upload:hover {
          background-color: #0056b3;
        }
      </style>

    </div>

  </div>

  <!-- Services Popup Modal -->
  <div id="servicesModal" class="modal">
    <div class="modal-content">
      <span class="close-btn" onclick="closeServices()">&times;</span>
      <h2>RX Medo Card Services</h2>
      <ul class="services-list">
        <li>Discount upto 20–50% in Diagnostics and Laboratories</li>
        <li>Discount upto 20–50% in OPD</li>
        <li>Discount upto 20–22% in Pharmacy</li>
        <li>Discount upto 10–20% in Hospital Operations (Eye, ENT, Dental, etc.)</li>
        <li>Discount upto 10–20% in Short-Term Hospitalization</li>
        <li>Discount upto 10–20% on Domiciliary/Home Nurses</li>
        <li>Discount upto 10–20% on Purchase/Rental Medical Devices</li>
        <li>Discount upto 10–20% on Physio and Other Therapies</li>
        <li>Free OPD in Multiple Hospitals</li>
        <li>Free Delivery of Medicines & Free Ambulance</li>
        <li>Online Free E-Consultation</li>
      </ul>
    </div>
  </div>
  <style>
    /* Background Overlay */
    .modal {
      display: none;
      position: fixed;
      z-index: 9999;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.6);
      justify-content: center;
      align-items: center;
    }

    /* Modal Box */
    .modal-content {
      background: #fff;
      width: 90%;
      max-width: 450px;
      padding: 20px;
      border-radius: 10px;
      animation: popup 0.3s ease-out;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
    }

    @keyframes popup {
      from {
        transform: scale(0.8);
        opacity: 0;
      }

      to {
        transform: scale(1);
        opacity: 1;
      }
    }

    .close-btn {
      float: right;
      font-size: 26px;
      cursor: pointer;
    }

    .services-list {
      margin-top: 15px;
      padding-left: 20px;
    }

    .services-list li {
      margin: 8px 0;

      font-size: 15px;
    }
  </style>

  <!-- Insure Card Services Popup Modal -->
  <div id="insureServicesModal" class="modal">
    <div class="modal-content">
      <span class="close-btn" onclick="closeInsureServices()">&times;</span>

      <h2>🛡️ Our Insure Services</h2>

      <h3>1. Young India (18–35 Years)</h3>
      <ul class="services-list">
        <li>✔ Waiver of First Year Exclusion</li>
        <li>✔ Waiver of 30 Days Waiting Period</li>
        <li>✔ Waiver of First Two Year Exclusion</li>
        <li>✔ Cover for Pre-Existing Diseases</li>
        <li>✔ Insurance Coverage of ₹5 Lakhs & ₹50 Lakhs Accidental</li>
        <li>✔ Free Online Consultation with Empanelled Doctors</li>
        <li>✔ Long-term discount upto 20% for 5 Years extension on this Insure Card</li>
      </ul>

      <h3>2. Matured Individuals (36–50 Years)</h3>
      <ul class="services-list">
        <li>✔ Add Family Members to This Card</li>
      </ul>

      <h3>3. Senior Citizens (50+ Years)</h3>
      <ul class="services-list">
        <li>✔ Free Home Medication Services</li>
      </ul>

      <p style="margin-top: 15px; font-weight: bold;">
        It also includes all the benefits of the RX Medo Card
      </p>

      <p style="font-size: 13px; opacity: 0.7;">
        * Not applicable for Corporate Insurance card holders
      </p>

    </div>
  </div>


  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- SECTION SWITCHING -->
  <script>
    function showInsureServices() {
      document.getElementById("insureServicesModal").style.display = "flex";
    }

    function closeInsureServices() {
      document.getElementById("insureServicesModal").style.display = "none";
    }

    window.onclick = function (e) {
      const modal = document.getElementById("insureServicesModal");
      if (e.target === modal) {
        closeInsureServices();
      }
    }
  </script>

  <script>
    function showServices() {
      document.getElementById("servicesModal").style.display = "flex";
    }

    function closeServices() {
      document.getElementById("servicesModal").style.display = "none";
    }

    // Close if clicked outside
    window.onclick = function (e) {
      if (e.target == document.getElementById("servicesModal")) {
        closeServices();
      }
    }
  </script>

  <script>
    function showSection(sectionId, btnEl) {
      // Hide all sections
      document.querySelectorAll('.content-section').forEach(section => {
        section.classList.remove('active');
      });

      // Show selected
      const target = document.getElementById(sectionId);
      if (target) {
        target.classList.add('active');
        // Scroll to top of main content (optional)
        document.querySelector('.main-content').scrollTop = 0;
      } else {
        console.warn('No section with id:', sectionId);
      }

      // Sidebar active highlight
      // remove active from all menu items
      document.querySelectorAll('.sidebar .menu-btn, .sidebar a').forEach(el => {
        el.classList.remove('active');
      });

      // add active to the clicked element (if provided)
      if (btnEl) btnEl.classList.add('active');
    }

    function confirmLogout() {
        if (confirm("Are you sure you want to logout?")) {
          window.location.href = "index.html"; // OK → Logout
          return false;
        }
        return false; // Cancel → Stay on same page
      }

    // Optionally show dashboard on load (it's already active in HTML, but this ensures highlight)
    document.addEventListener('DOMContentLoaded', function () {
      // highlight the first sidebar button (Profile)
      const firstBtn = document.querySelector('.sidebar .menu-btn');
      if (firstBtn) firstBtn.classList.add('active');
      // ensure dashboard visible
      showSection('dashboard', firstBtn);
    });
  </script>
  <script>
    document.querySelectorAll('.appointment-btn a').forEach(btn => {
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
          window.scrollTo({
            top: target.offsetTop - 20,
            behavior: 'smooth'
          });
        }
      });
    });
  </script>
  <script>
    function toggleSidebar() {
      let sidebar = document.getElementById("sidebar");
      let hamIcon = document.getElementById("hamIcon");

      sidebar.classList.toggle("open");

      if (sidebar.classList.contains("open")) {
        hamIcon.innerHTML = "✕"; // Close icon
      } else {
        hamIcon.innerHTML = "☰"; // Hamburger icon
      }
    }
    
  </script>
  



  <script src="script.js"></script>
</body>

</html>