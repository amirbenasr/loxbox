

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

    //handles the continue button of chose delivery step
    //and stop propagation
    $("button[type='submit']button[name='confirmDeliveryOption']").click(function(e) {
      //--^------ change here
      return  (SELECTED) ||
         (e.stopPropagation(),e.preventDefault(),
         alert('Vous devez sélectionner un point relais'))
    });

    //comment the document on submit
    //because of steps signal
    // $(document).on("submit", "#js-delivery", function (e) {
    
 
    //   //  return  (SELECTED) ||
    //   //    (e.stopPropagation(),e.preventDefault(),
    //   //    alert('Vous devez sélectionner un point relais'))
    
    // });
  var selectedI;
  $(".delivery-options").on("change", function () {
    var value = $("input[name^='delivery_option']:checked").val();
    selectedI = value.slice(0, -1);

    $.ajax({
      type: "POST",
      url: "http://localhost/prestashop/en/module/loxbox/task",

      data: "product_id=" + selectedI + "&ajax=1",
      success: function (response) {

        var body = JSON.parse(response);
        var carrier = body.message;
        console.log(carrier); 

        if (carrier.external_module_name === "loxbox") {
          isLoxbox=true;
      $('.loxbox-widget').show();

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