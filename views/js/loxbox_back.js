
        
function action()
{
  $.ajax({
    type: 'POST',
    url: admin_loxbox_link,
    data: {
      controller : 'AdminLoxbox',
      action : 'AdminLoxboxAction',
      ajax : true
    },
    success: function(jsonData)
    {
        $("#text").html("hello from ajax"+jsonData);
    }
  });
}
$(document).ready( function () {
  if (typeof admin_loxbox_link !== 'undefined') {
    action();
  }
});