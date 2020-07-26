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
          if(validateDonation()) {
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
