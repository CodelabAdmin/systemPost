export function notifyToast(message, type) {
  let color;
  let successColor = "#4CAF50";
  let errorColor = "#ff4444";

  if (type === "success") {
    color = successColor;
  } else {
    color = errorColor;
  }

  Toastify({
    text: message,
    duration: 3000,
    close: true,
    gravity: "top",
    position: "right",
    style: {
      background: color,
      borderRadius: "8px",
      padding: "12px 24px",
      boxShadow: "0 3px 6px rgba(0,0,0,0.16)",
      fontSize: "14px",
      fontFamily: "Poppins",
    },
  }).showToast();
}
