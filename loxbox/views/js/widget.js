$(document).ready(function () {
  ///global

  var selectedI;
  $("#form input").on("change", function () {
    var value = $('input[name="delivery_option[5]"]:checked', "#form").val();

    selectedI = value.slice(0, -1);
    var parent = $(".parent");
    var relay = $('.relay-content');

    $.ajax({
      type: "POST",
      url: baseUri + "module/loxbox/task",

      data: "product_id=" + selectedI + "&ajax=1",
      success: function (response) {
        var body = JSON.parse(response);
        var carrier = body.message;

        if (carrier.external_module_name === "Loxbox") {
       
          parent.show();
        } else {
          parent.hide();
        }
      },
    });
  });

  ///global
});

function tokenValid(token) {
  var isValid = false;

  $.ajax({
    type: "get",
    url: "https://www.loxbox.tn/api/Welcome/",

    headers: {
      Authorization: `Token ${token}`,
    },
    success: function (xhr, status) {
      console.log("we are running the api");
      return xhr;
    },
    statusCode: {
      200: (data) => {
        isValid = true;
      },
      401: function () {
        // Only if your server returns a 403 status code can it come in this block. :-)
        alert("Token is invalid check https://loxbox.tn");
      },
    },
  });
  return isValid;
}
