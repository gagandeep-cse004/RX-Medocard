window.addEventListener("DOMContentLoaded", function () {
  // ✅ Fetch order from backend
  fetch("payment.php")
    .then(res => res.json())
    .then(order => {
      const options = {
        key: "rzp_test_Rcrn4lW5mVtwQO", // ✅ Your Test Key ID
        amount: order.amount,
        currency: "INR",
        name: "RX Medo Card",
        description: "Card Purchase",
        order_id: order.id, // ✅ From backend
        handler: function (response) {
          alert("✅ Payment Successful! Payment ID: " + response.razorpay_payment_id);
          window.location.href = "dashboard.html"; // ✅ Redirect after success
        },
        prefill: {
          name: "Palak",
          email: "palak@example.com",
          contact: "9876543210"
        },
        theme: {
          color: "#0a7cff"
        },
        modal: {
          ondismiss: function () {
            alert("❌ Payment Cancelled.");
          }
        }
      };

      const rzp = new Razorpay(options);
      rzp.open();
    })
    .catch(() => {
      alert("❌ Failed to initiate payment.");
    });
});
