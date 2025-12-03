<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Book Services</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

  body {
    font-family: 'Poppins', sans-serif;
    background: #e6f0ff;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 40px 20px;
  }

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
</style>
</head>
<body>

<div class="form-container">
  <h2>Book Services</h2>

  <form method="POST" action="submit_request.php" enctype="multipart/form-data">
    <!-- Service type -->
    <label for="service_type">Service type</label>
    <select id="service_type" name="service_type" required>
      <option value="">-- Select Service --</option>
      <option value="Hospital Visit">Hospital Visit</option>
      <option value="OPD">OPD</option>
      <option value="Pharmacy">Pharmacy</option>
      <option value="Diagnostic">Diagnostic</option>
    </select>

    <!-- Hospital block -->
    <div id="hospital_block">
      <label>Select service provider</label>
      <div class="dropdown">
        <div class="dropdown-btn" id="dropdownBtn">-- Select Service Provider --</div>
        <div class="dropdown-content" id="dropdownContent">
          <div class="dropdown-header">
            <input type="text" id="hospital_search" placeholder="Search service providers...">
          </div>
          <div class="dropdown-list" id="hospitalList">
            <!-- List will be loaded dynamically -->
          </div>
        </div>
      </div>
      <input type="hidden" name="provider_id" id="selectedHospital" required>
    </div>

    <!-- Appointment date -->
    <div id="appointment_date_block">
      <label for="appointment_date">Appointment date</label>
      <input type="date" id="appointment_date" name="appointment_date" required>
    </div>

    <!-- Appointment time -->
    <div id="appointment_time_block">
      <label for="appointment_time">Appointment time</label>
      <select id="appointment_time" name="appointment_time" required>
        <option value="">-- Select Time --</option>
        <option value="8:00 AM">8:00 AM</option>
        <option value="9:00 AM">9:00 AM</option>
        <option value="10:00 AM">10:00 AM</option>
        <option value="11:00 AM">11:00 AM</option>
        <option value="12:00 PM">12:00 PM</option>
        <option value="1:00 PM">1:00 PM</option>
        <option value="2:00 PM">2:00 PM</option>
        <option value="3:00 PM">3:00 PM</option>
        <option value="4:00 PM">4:00 PM</option>
        <option value="5:00 PM">5:00 PM</option>
        <option value="6:00 PM">6:00 PM</option>
        <option value="7:00 PM">7:00 PM</option>
      </select>
    </div>

    <!-- Prescription upload -->
    <label for="prescription_image">Prescription (optional)</label>
    <input type="file" id="prescription_image" name="prescription_image" accept="image/*">

   <!-- ✅ Card number auto-fill -->
<!-- <label for="card_no_display">Card number</label>
<input type="text" id="card_no_display"  -->
       <!-- value="<?php echo htmlspecialchars($card_no); ?>" readonly> -->
  <label for="card_no_display">Card number</label>
<input type="text" id="card_no_display" value="<?php echo htmlspecialchars($card_no); ?>" readonly>
<input type="hidden" name="card_no" value="<?php echo htmlspecialchars($card_no); ?>">
<!-- ✅ Hidden field to ensure value goes to backend -->
<input type="hidden" id="card_no" name="card_no" 
       value="<?php echo htmlspecialchars($card_no); ?>">


    <button type="submit">Submit request</button>
  </form>
</div>

<script>
const serviceTypeSelect = document.getElementById('service_type');
const hospitalBlock = document.getElementById('hospital_block');
const appointmentDateBlock = document.getElementById('appointment_date_block');
const appointmentTimeBlock = document.getElementById('appointment_time_block');
const dropdownBtn = document.getElementById('dropdownBtn');
const dropdownContent = document.getElementById('dropdownContent');
const hospitalSearch = document.getElementById('hospital_search');
const hospitalList = document.getElementById('hospitalList');
const selectedHospital = document.getElementById('selectedHospital');

// Show/hide blocks based on service type
serviceTypeSelect.addEventListener('change', function () {
  const selectedType = this.value;

  if (selectedType === 'Pharmacy') {
    // Hide hospital and appointment fields
    hospitalBlock.style.display = 'none';
    appointmentDateBlock.style.display = 'none';
    appointmentTimeBlock.style.display = 'none';
    selectedHospital.value = "";
    dropdownBtn.textContent = "-- Select Service Provider --";
  } else if (selectedType !== '') {
    hospitalBlock.style.display = 'block';
    appointmentDateBlock.style.display = 'block';
    appointmentTimeBlock.style.display = 'block';
    loadHospitals(selectedType);
  } else {
    hospitalBlock.style.display = 'none';
    appointmentDateBlock.style.display = 'none';
    appointmentTimeBlock.style.display = 'none';
    selectedHospital.value = "";
    dropdownBtn.textContent = "-- Select Service Provider --";
  }
});

// Load hospitals dynamically
function loadHospitals(serviceType) {
  const xhr = new XMLHttpRequest();
  xhr.open("GET", "load_hospitals.php?type=" + encodeURIComponent(serviceType), true);
  xhr.onload = function() {
    if (xhr.status === 200) {
      hospitalList.innerHTML = xhr.responseText;
      dropdownBtn.textContent = "-- Select Service Provider --";
      selectedHospital.value = "";
    }
  };
  xhr.send();
}

// Toggle dropdown list
dropdownBtn.addEventListener('click', function () {
  dropdownContent.style.display = dropdownContent.style.display === 'block' ? 'none' : 'block';
});

// Select hospital from list
hospitalList.addEventListener('click', function(e) {
  if (e.target && e.target.dataset.id) {
    selectedHospital.value = e.target.dataset.id;
    dropdownBtn.textContent = e.target.textContent;
    dropdownContent.style.display = 'none';
  }
});

// Search filter
hospitalSearch.addEventListener('keyup', function() {
  const filter = this.value.toUpperCase();
  const items = hospitalList.querySelectorAll('div[data-id]');
  items.forEach(item => {
    const txt = item.textContent.toUpperCase();
    item.style.display = txt.includes(filter) ? '' : 'none';
  });
});

// Close dropdown if clicked outside
document.addEventListener('click', function(e) {
  if (!dropdownBtn.contains(e.target) && !dropdownContent.contains(e.target)) {
    dropdownContent.style.display = 'none';
  }
});


</script>

</body>
</html>
