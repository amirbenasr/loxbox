

$(document).on('ready',function () {
  //global
    if(isLoxbox==true)
    {
      $('.loxbox-widget').show();
    }   
    else {
      $('.loxbox-widget').hide();
      SELECTED=true;

    }

    $(document).on("submit", "#form", function (e) {
      return (
        (SELECTED) ||
        (e.preventDefault(),
        e.stopPropagation(),
        alert('Vous devez sélectionner un point relais'))
      );
    });
  var selectedI;
  $(".delivery_options").on("change", function () {
    var value = $('.delivery_option_radio:checked', "#form").val();
    selectedI = value.slice(0, -1);
    $.ajax({
      type: "POST",
      url: baseUri + "module/loxbox/task",

      data: "product_id=" + selectedI + "&ajax=1",
      success: function (response) {

        var body = JSON.parse(response);
        var carrier = body.message;
        console.log(carrier); 

        if (carrier.external_module_name === "loxbox") {
          isLoxbox=true;
          // $('.loxbox_container').show();
        } else {

          
          // $('.loxbox_container').hide();

          isLoxbox=false;
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
