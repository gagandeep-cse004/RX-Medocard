// ✅ Show Terms Modal before form submission
function showTerms(event) {
  event.preventDefault();
  document.getElementById("termsModal").style.display = "block";
}

function acceptTerms() {
  const agreed = document.getElementById("agree").checked;
  const cardType = document.querySelector('input[name="card_type"]:checked')?.value;
  const ageGroupSelected = document.querySelector('input[name="age_group"]:checked');
  const describeAgeSelected = document.querySelector('input[name="describe_age"]:checked');

  // ✅ Modular family opt and member selection based on card type
  let familyOptSelected = null;
  let rxFamilyOptSelected = null;
  let familyMembersSelected = null;

  // ✅ Signup form field validations
  const name = document.getElementById("name")?.value.trim();
  const email = document.getElementById("mail")?.value.trim();
  const phone = document.getElementById("phone")?.value.trim();
  const pass = document.getElementById("pass")?.value.trim();
  const aadhaar = document.getElementById("aadhaar_card_no")?.value.trim();
  const pan = document.getElementById("pan_card_no")?.value.trim();

  let errors = [];

  if (!name || !/^[A-Za-z\s]+$/.test(name)) {
    errors.push("❌ Name must contain only alphabets and cannot be blank.");
  }

  if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
    errors.push("❌ Invalid email format or blank email.");
  }

  if (!phone || !/^\d{10}$/.test(phone)) {
    errors.push("❌ Phone must be exactly 10 digits and cannot be blank.");
  }

  if (!pass) {
    errors.push("❌ Password cannot be blank.");
  }

  if (!aadhaar || !/^\d{12}$/.test(aadhaar)) {
    errors.push("❌ Aadhaar must be exactly 12 digits and cannot be blank.");
  }

  if (!pan || !/^[A-Z]{5}[0-9]{4}[A-Z]$/.test(pan)) {
    errors.push("❌ PAN must be 10 characters (5 letters, 4 digits, 1 letter) and cannot be blank.");
  }

  if (errors.length > 0) {
    alert(errors.join("\n"));
    return;
  }

  if (!agreed) {
    alert("❌ Please agree to the terms before signing up.");
    return;
  }

  if (cardType === "RX Medo Insure Card") {
    if (!ageGroupSelected) {
      alert("❌ Please select your age group for RX Medo Insure Card.");
      return;
    }

    const ageValue = ageGroupSelected.value;

    if (ageValue === "Young India (18-35)") {
      if (!familyOptSelected) {
        alert("❌ Please select Individual or Family under 'Want to opt for your family?'.");
        return;
      }

      if (familyOptSelected === "Family" && !describeAgeSelected) {
        alert("❌ Please describe your age under Young India.");
        return;
      }

      if (familyOptSelected === "Family" && !familyMembersSelected) {
        alert("❌ Please select family members based on your age group.");
        return;
      }

      if (familyOptSelected === "Individual" && (describeAgeSelected || familyMembersSelected)) {
        alert("❌ Age and family members selection is only allowed when Family is selected.");
        return;
      }
    }

    if (ageValue === "Mature Individual (36-50)") {
      const matureOpt = document.querySelector('#matureFlow input[name="mature_family_opt"]:checked')?.value;
      const matureMembers = document.querySelector('#matureFamilyMembers input[name="family_members_selected"]:checked');

      if (!matureOpt) {
        alert("❌ Please select Individual or Family under Mature Individual.");
        return;
      }

      if (matureOpt === "Family" && !matureMembers) {
        alert("❌ Please select family members under Mature Individual.");
        return;
      }
    }

    if (ageValue === "Senior Citizens (51+)") {
      const seniorOpt = document.querySelector('#seniorFlow input[name="mature_family_opt"]:checked')?.value;
      const seniorMembers = document.querySelector('#seniorFamilyMembers input[name="family_members_selected"]:checked');

      if (!seniorOpt) {
        alert("❌ Please select Individual or Family under Senior Citizens.");
        return;
      }

      if (seniorOpt === "Family" && !seniorMembers) {
        alert("❌ Please select family members under Senior Citizens.");
        return;
      }
    }
  }

  if (cardType === "RX Medo Card") {
    const rxFamilyOptSelected = document.querySelector('input[name="rxmedo_card_family_opt"]:checked')?.value;
    const familyMembersSelected = document.querySelector('input[name="rxmedo_card_family_members"]:checked');

    if (!rxFamilyOptSelected) {
      alert("❌ Please select Individual or Family under RX Medo Card.");
      return;
    }

    if (rxFamilyOptSelected === "Family" && !familyMembersSelected) {
      alert("❌ Please select family members under RX Medo Card.");
      return;
    }

    if (rxFamilyOptSelected === "Individual" && familyMembersSelected) {
      alert("❌ Family members selection is only allowed when Family is selected under RX Medo Card.");
      return;
    }
  }

  if (cardType === "RX Medo Top Up Card") {
    const rxFamilyOptSelected = document.querySelector('input[name="rxmedo_topup_family_opt"]:checked')?.value;
    const familyMembersSelected = document.querySelector('input[name="rxmedo_topup_family_members"]:checked');

    document.getElementById("rxMedoFamilyMembers").style.display = "none";

    if (!rxFamilyOptSelected) {
      alert("❌ Please select Individual or Family under RX Medo Top Up Card.");
      return;
    }

    if (rxFamilyOptSelected === "Family" && !familyMembersSelected) {
      alert("❌ Please select family members under RX Medo Top Up Card.");
      return;
    }

    if (rxFamilyOptSelected === "Individual" && familyMembersSelected) {
      alert("❌ Family members selection is only allowed when Family is selected under RX Medo Top Up Card.");
      return;
    }

    if (rxFamilyOptSelected === "Individual") {
      document.getElementById("rxMedoTopUpFamilyMembers").style.display = "none";
      document.querySelectorAll('input[name="rxmedo_topup_family_members"]').forEach(el => el.checked = false);
    }
  }

  // ✅ Inject hidden input for 'agree' checkbox before submitting
  const form = document.getElementById("signupForm");
  const hiddenAgree = document.createElement("input");
  hiddenAgree.type = "hidden";
  hiddenAgree.name = "agree";
  hiddenAgree.value = "on";
  form.appendChild(hiddenAgree);

  document.getElementById("signupForm").submit();
}




// ✅ Close Terms Modal
function closeModal() {
  document.getElementById("termsModal").style.display = "none";
}

// ✅ Toggle Sections Based on Selection
document.addEventListener("DOMContentLoaded", function () {
  const ageGroupSection = document.getElementById("ageGroupSection");
  const youngIndiaFlow = document.getElementById("youngIndiaFlow");
  const matureFlow = document.getElementById("matureFlow");
  const seniorFlow = document.getElementById("seniorFlow");
  const describeAgeSection = document.getElementById("describeAgeSection");
  const membersBelow25 = document.getElementById("membersBelow25");
  const membersAbove25 = document.getElementById("membersAbove25");
  const matureFamilyMembers = document.getElementById("matureFamilyMembers");
  const seniorFamilyMembers = document.getElementById("seniorFamilyMembers");
  const rxMedoFamilySection = document.getElementById("rxMedoFamilySection");
  const rxMedoFamilyMembers = document.getElementById("rxMedoFamilyMembers");

  const rxInsureTopUpSection = document.getElementById("rxInsureTopUpSection");
  const insuranceCardUpload = document.getElementById("insuranceCardUpload");
  const upgradeOptions = document.getElementById("upgradeOptions");
  const rxMedoTopUpSection = document.getElementById("rxMedoTopUpSection");
  const rxMedoTopUpUpload = document.getElementById("rxMedoTopUpUpload");
  const rxMedoTopUpFamilyMembers = document.getElementById("rxMedoTopUpFamilyMembers");


  function resetInsureCardFlow() {
    ageGroupSection.style.display = "none";
    youngIndiaFlow.style.display = "none";
    matureFlow.style.display = "none";
    seniorFlow.style.display = "none";
    describeAgeSection.style.display = "none";
    membersBelow25.style.display = "none";
    membersAbove25.style.display = "none";
    matureFamilyMembers.style.display = "none";
    seniorFamilyMembers.style.display = "none";

    document.querySelectorAll('input[name="age_group"]').forEach(el => el.checked = false);
    document.querySelectorAll('input[name="family_opt"]').forEach(el => el.checked = false);
    document.querySelectorAll('input[name="mature_family_opt"]').forEach(el => el.checked = false);
    document.querySelectorAll('input[name="describe_age"]').forEach(el => el.checked = false);
    document.querySelectorAll('input[name="family_members_selected"]').forEach(el => el.checked = false);
  }

 function resetRxMedoCardFlow() {
  rxMedoFamilySection.style.display = "none";
  rxMedoFamilyMembers.style.display = "none";

  document.querySelectorAll('input[name="rxmedo_card_family_opt"]').forEach(el => el.checked = false);
  document.querySelectorAll('input[name="rxmedo_card_family_members"]').forEach(el => el.checked = false);
}

function resetTopUpCardFlow() {
  rxMedoTopUpSection.style.display = "none";         // ✅ Hide main Top Up section
  rxInsureTopUpSection.style.display = "none";       // ✅ Hide Insure Top Up sub-section
  upgradeOptions.style.display = "none";             // ✅ Hide upgrade options
  insuranceCardUpload.value = "";                    // ✅ Clear uploaded file
  rxMedoTopUpUpload.value = "";                      // ✅ Clear Top Up card upload

  document.querySelectorAll('input[name="upgrade_amount"]').forEach(el => el.checked = false);
  document.querySelectorAll('input[name="rxmedo_topup_family_opt"]').forEach(el => el.checked = false);
  document.querySelectorAll('input[name="rxmedo_topup_family_members"]').forEach(el => el.checked = false);
}


 document.querySelectorAll('input[name="card_type"]').forEach(radio => {
  radio.addEventListener("change", function () {
    // ✅ Reset all flows
    resetInsureCardFlow();
    resetRxMedoCardFlow();
    resetTopUpCardFlow();

    // ✅ Show relevant section based on selected card
    if (this.value === "RX Medo Insure Card") {
      ageGroupSection.style.display = "block";
    }

    if (this.value === "RX Medo Card") {
      rxMedoFamilySection.style.display = "block";
    }

    if (this.value === "RX Medo Insure Top Up Card") {
      rxInsureTopUpSection.style.display = "block";
      upgradeOptions.style.display = "block";
    }

    if (this.value === "RX Medo Top Up Card") {
      rxMedoTopUpSection.style.display = "block"; // ✅ Correct section

      // ✅ Ensure RX Medo Card section stays hidden
      rxMedoFamilySection.style.display = "none";
      rxMedoFamilyMembers.style.display = "none";
      document.querySelectorAll('input[name="rxmedo_card_family_opt"]').forEach(el => el.checked = false);
      document.querySelectorAll('input[name="rxmedo_card_family_members"]').forEach(el => el.checked = false);
    }
  });
});

  // ✅ RX Medo Card → Family Opt Toggle
// ✅ RX Medo Card — Show family members only when "Family" is selected
document.querySelectorAll('input[name="rxmedo_card_family_opt"]').forEach(radio => {
  radio.addEventListener("change", function () {
    rxMedoFamilyMembers.style.display = this.value === "Family" ? "block" : "none";
    document.querySelectorAll('input[name="rxmedo_card_family_members"]').forEach(el => el.checked = false);
  });
});

// ✅ RX Medo Insure Card — Age group toggle logic (unchanged)
document.querySelectorAll('input[name="age_group"]').forEach(radio => {
  radio.addEventListener("change", function () {
    youngIndiaFlow.style.display = "none";
    matureFlow.style.display = "none";
    seniorFlow.style.display = "none";
    describeAgeSection.style.display = "none";
    membersBelow25.style.display = "none";
    membersAbove25.style.display = "none";
    matureFamilyMembers.style.display = "none";
    seniorFamilyMembers.style.display = "none";

    document.querySelectorAll('input[name="family_opt"]').forEach(el => el.checked = false);
    document.querySelectorAll('input[name="mature_family_opt"]').forEach(el => el.checked = false);
    document.querySelectorAll('input[name="describe_age"]').forEach(el => el.checked = false);
    document.querySelectorAll('input[name="family_members_selected"]').forEach(el => el.checked = false);

    const selectedAge = this.value;

    if (selectedAge === "Young India (18-35)") {
      youngIndiaFlow.style.display = "block";
    }

    if (selectedAge === "Mature Individual (36-50)") {
      matureFlow.style.display = "block";
    }

    if (selectedAge === "Senior Citizens (51+)") {
      seniorFlow.style.display = "block";
    }
  });
});
  // ✅ Young India → Family Opt Toggle

  document.querySelectorAll('input[name="family_opt"]').forEach(radio => {
    radio.addEventListener("change", function () {
      describeAgeSection.style.display = "none";
      membersBelow25.style.display = "none";
      membersAbove25.style.display = "none";

      document.querySelectorAll('input[name="describe_age"]').forEach(el => el.checked = false);
      document.querySelectorAll('input[name="family_members_selected"]').forEach(el => el.checked = false);

      if (this.value === "Family") {
        describeAgeSection.style.display = "block";
      }
    });
  });

  document.querySelectorAll('input[name="describe_age"]').forEach(radio => {
    radio.addEventListener("change", function () {
      membersBelow25.style.display = "none";
      membersAbove25.style.display = "none";

      document.querySelectorAll('input[name="family_members_selected"]').forEach(el => el.checked = false);

      if (this.value === "Below 25") {
        membersBelow25.style.display = "block";
      } else if (this.value === "Above 25") {
        membersAbove25.style.display = "block";
      }
    });
  });

  // ✅ Mature Individual → Family Opt Toggle
document.querySelectorAll('#matureFlow input[name="mature_family_opt"]').forEach(radio => {
  radio.addEventListener("change", function () {
    matureFamilyMembers.style.display = "none";
    document.querySelectorAll('#matureFamilyMembers input[name="family_members_selected"]').forEach(el => el.checked = false);

    if (this.value === "Family") {
      matureFamilyMembers.style.display = "block";
    }
  });
});

// ✅ Senior Citizens → Family Opt Toggle
document.querySelectorAll('#seniorFlow input[name="mature_family_opt"]').forEach(radio => {
  radio.addEventListener("change", function () {
    seniorFamilyMembers.style.display = "none";
    document.querySelectorAll('#seniorFamilyMembers input[name="family_members_selected"]').forEach(el => el.checked = false);

    if (this.value === "Family") {
      seniorFamilyMembers.style.display = "block";
    }
  });
});

// ✅ RX Medo Top Up Card → Family Opt Toggle
document.querySelectorAll('#rxMedoTopUpSection input[name="rxmedo_topup_family_opt"]').forEach(radio => {
  radio.addEventListener("change", function () {
    rxMedoTopUpFamilyMembers.style.display = "none";
    document.querySelectorAll('#rxMedoTopUpFamilyMembers input[name="rxmedo_topup_family_members"]').forEach(el => el.checked = false);

    if (this.value === "Family") {
      rxMedoTopUpFamilyMembers.style.display = "block";
    }
  });
});


insuranceCardUpload?.addEventListener("change", function () {
  const selectedCard = document.querySelector('input[name="card_type"]:checked')?.value;

  // ✅ Only show upgrade options for RX Medo Insure Top Up Card
  if (selectedCard === "RX Medo Insure Top Up Card") {
    upgradeOptions.style.display = this.files.length > 0 ? "block" : "none";
  } else {
    upgradeOptions.style.display = "none";
  }
});
});

