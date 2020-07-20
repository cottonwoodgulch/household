/* house/js/house.js - javascript functions */

function slideDropDown() {
  $(".drop-down li ul").hide().removeClass("fallback");
  $(".drop-down li").hover(
    function () {
      $(this).find("ul").stop().slideDown(400);
    },
    function () {
      $(this).find("ul").stop().slideUp(400);
    }
  );
}

$(document).ready(function () {
  slideDropDown();
} );

function hideFooter() {
  $("#footer").hide(200);  
}
