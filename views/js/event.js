$(document).on("ready loadThis", function (event, par1, par2) {
  console.log(par1);
  console.log(par2);
  if (isLoxbox) {
    alert("we should show the plugin");
    loadPlugin();
  } else {
    alert("we should hide the plugin");

    hidePlugin();
  }
});
