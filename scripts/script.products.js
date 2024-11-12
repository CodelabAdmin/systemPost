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
    return { count: 0 };
  }
}
