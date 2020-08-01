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
          if(validateHousehold($("#HouseholdName").val(),0)) {
            $("#NewHouseForm input[name=buttonAction]").val("save");
            $("#NewHouseForm").submit();
          }
        }
      },
      {
        text: "Cancel",
        click: function() {
          $("#HouseholdName").val("");
          $("#Salutation").val("");
          $("#MailName").val("");
          $(this).dialog("destroy");
        }
      }
    ]
  });
}

function validateHousehold(hname,hid) {
  let ErrMsg = "";
  let ErrType = "";
  let retval=true;
  if(hname.length <= 0) {
    ErrMsg += "Household name is required<br />";
  }
  $.ajax({
    method: "GET",
    url: "../ajax/ValidateHouse.php",
    data: {"hname": hname, "&hid=": hid},
    dataType: "text",
    success: function(res_html, textStatus, xhr) {
      if(res_html) {
        ErrMsg += "Household name is in use</br>";
      }
      if(ErrMsg.length) {
        retval=false;
        ErrType="Household Name Error";
      }
    },
    error: function(xhr, textStatus, errorThrown) {
      ErrMsg = textStatus+" "+errorThrown;
      ErrType = "Ajax Error";
      retval=false;
    }
  });
  displayError(ErrType,ErrMsg);
  console.log('retval: '+retval+', error: '+ErrMsg+', ErrType: '+ErrType);
  alert("hello");
  return retval;
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
