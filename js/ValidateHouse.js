/* ValidateHouse.js */

function validateHousehold(hname,hid) {
  ErrMsg = new String();
  if(hname.length <= 0) {
    ErrMsg += "Household name is required<br />";
  }
  $.ajax({
    type: "GET",
    url: "ajax/ajax_details.php?hname="+hname+"&hid="+hid,
    dataType: "text",
    success: function(res_html, textStatus, xhr) {
      response("0");
    },
    error: function(xhr, textStatus, errorThrown) {
      $("#house_name_error").html(textStatus+" "+errorThrown);
    }
  })
}
