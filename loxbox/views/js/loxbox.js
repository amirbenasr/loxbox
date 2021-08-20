$(document).ready(function () {
  var token = $("#_ltoken").val();

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
        alert(`Welcome ${data.Name} to LoxBox Services Plugin`);
        $(".alert-success").show();
      },
      401: function () {
        // Only if your server returns a 403 status code can it come in this block. :-)
        // alert("Token is invalid");
        $(".alert-warning").show();
      },
    },
  });
  var formElements = document.forms["formName"].elements["ltoken"].value;
  console.log(formElements);
  $("#formToken").submit(function (e) {
    document.getElementById("#saveToken").submit();
  });

  ///welcome api if it's success add welcome message

  
  
});
