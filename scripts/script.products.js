$(document).ready(async function () {
  $("#countProducts").html('<i class="fas fa-spinner fa-spin"></i>');

  let count = await countProducts();
  $("#countProducts").html(count.count);
});

async function countProducts() {
  try {
    let res = await fetch(
      "http://localhost/server/systemPost/api/products/count"
    );
    let data = await res.json();

    return { count: data["COUNT(*)"] };
  } catch (error) {
    console.error(error);
    Toastify({
        text: "Error al cargar el conteo de productos",
        duration: 3000,
        close: true,
        gravity: "top",
        position: "right",
        style: {
            background: "#ff4444",
            borderRadius: "8px",
            padding: "12px 24px",
            boxShadow: "0 3px 6px rgba(0,0,0,0.16)",
            fontSize: "14px",
        fontFamily: "Poppins",
      },
    }).showToast();
    return { count: 0 };
  }
}
