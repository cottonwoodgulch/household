/* newhouse.js - functions for new household dialog */

function newHouse() {
  $("#NewHouseDialog").dialog ({
    classes: {"ui-dialog": "no-titlebar"},
    height: "auto",
    width: "auto",
    closeOnEscape: true,
    buttons: [
      {
        text: "Save",
        type: "button",
        click: function() {
          if(validateHousehold()) {
            $("#DonationEditForm input[name=buttonAction]").val(title);
            $("#DonationEditForm").submit();
          }
        }
      },
      {
        text: "Cancel",
        click: function() {
          $(this).dialog("destroy");
        }
      }
    ]
  });
}

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
      alert("validate success: "+res_html);
      if(res_html == "1") {
        $ErrMsg += "Household name is in use</br>";
      }
    },
    error: function(xhr, textStatus, errorThrown) {
      $ErrMsg +=      $("#house_name_error").html(textStatus+" "+errorThrown);
    }
  })
}

/* for delete household
      {
        text: "Delete",
        type: "button",
        id: "DeleteButton",
        click: function() {
          $("#DonationEditForm input[name=buttonAction]").val("Delete");
          Confirm("Delete",donation+" Donation","#DonationEditForm");
        }
      },
*/
