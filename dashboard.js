// ✅ Section Toggle Logic
function showSection(id) {
  const sections = ['dashboard', 'cards', 'membership', 'diagnostic', 'pharmacy', 'opd', 'hospitals', 'payment']; // ✅ opd added
  sections.forEach(sec => {
    const el = document.getElementById(sec);
    if (el) el.style.display = (sec === id) ? 'block' : 'none';
  });
}

// ✅ Payment History Loader
function showPayments() {
  const list = document.getElementById('paymentList');
  list.innerHTML = `
    <ul>
      <li>Payment on 2023-01-15: ₹2,500</li>
      <li>Payment on 2023-03-10: ₹2,500</li>
      <li>Payment on 2023-06-05: ₹2,500</li>
    </ul>
  `;
}

// ✅ Rotating Card Preview
const rotatingCard = document.getElementById('rotatingCard');
const cardFaces = ['face1', 'face2', 'face3', 'face4'];
let currentFace = 0;

function rotateCardPreview() {
  cardFaces.forEach(id => {
    const face = document.getElementById(id);
    if (face) face.style.display = 'none';
  });

  const activeFace = document.getElementById(cardFaces[currentFace]);
  if (activeFace) activeFace.style.display = 'block';

  rotatingCard.style.transform = (cardFaces[currentFace].includes('face2') || cardFaces[currentFace].includes('face4'))
    ? 'rotateY(180deg)' : 'rotateY(0deg)';

  currentFace = (currentFace + 1) % cardFaces.length;
}

// ✅ Modal Controls (RX Medo Services)
function showServices() {
  document.getElementById("servicesModal").style.display = "block";
}

function closeServices() {
  document.getElementById("servicesModal").style.display = "none";
}

// ✅ Modal Controls (RX Medo Insure Services)
function showInsureServices() {
  document.getElementById("insureServicesModal").style.display = "block";
}

function closeInsureServices() {
  document.getElementById("insureServicesModal").style.display = "none";
}

// ✅ Modal Controls (if used)
function openModal() {
  document.getElementById("serviceModal").style.display = "flex";
}

function closeModal() {
  document.getElementById("serviceModal").style.display = "none";
}

function confirmLogout() {
  if (confirm("Are you sure you want to logout?")) {
    window.location.href = "logout.php";
  }
}

// ✅ Initialize on Load
document.addEventListener('DOMContentLoaded', () => {
  showPayments();
  rotateCardPreview();
  setInterval(rotateCardPreview, 3000);
});
