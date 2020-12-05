/* js functions for dialogs on donations.php and donations.tpl */

/* add donation button will call addDonation()
 * change donation will call editDonation(donation_id) */

function editDonation(donation_id) {
  var donation_date = $("#Date"+donation_id).html();
  var xdd = new String(donation_date);
  DonationDialog("Edit",xdd.substring(5,7)+'/'+xdd.substring(8)+'/'+
       xdd.substring(0,4),donation_id);
  /* so PHP has access to the donation and old donor IDs to change the
       donation_assn rec */
  $("#EditDonationID").val(donation_id);
  $("#OldPrimaryDonorID").val($("#DonorID"+donation_id).html());
  $("#EditDate").val(donation_date);
  $("#EditAmount").val($("#Amount"+donation_id).html());
  selectedfund=$("#Fund"+donation_id).html();
  $("#EditFund option").each(function(index) {
    if($(this).html() == selectedfund) {
      $(this).prop("selected",true);
      return false;
    }
  });
  $("#EditPurpose").val($("#Purpose"+donation_id).html());
  if($("#Anonymous"+donation_id).html() == "x") {
    $("#EditAnonymous").prop("checked",true);
  }
  else {
    $("#EditAnonymous").prop("checked",false);
  }
  $("#EditPrimaryDonor option").each(function(index) {
    if($(this).val() == $("#DonorID"+donation_id).html()) {
      $(this).prop("selected",true);
      return false;
    }
  });  
};

function addDonation() {
  $("#EditDate").val("");
  $("#EditAmount").val("");
  $("#EditFund option").each(function(index) {
    $(this).prop("selected",false);
  });
  $("#EditPurpose").val("");
  $("#EditAnonymous").prop("checked",false);
  $("#EditPrimaryDonor option").each(function(index) {
    $(this).prop("selected",false);
  });
  // old donation_id and old primary donor id are not needed for add
  DonationDialog("Add","",0);
}

function DonationDialog(title,donation,donation_id) {
  $("#DonationEditDialog").dialog({
    dialogClass: "no-close",  // hide close button in top corner
    height: "auto",
    width: "auto",
    closeOnEscape: true,
    title: title+" "+donation+" Donation",
    buttons: [
      {
        text: "Delete",
        type: "button",
        id: "DeleteButton",
        click: function() {
          //$("#DonationEditForm input[name=buttonAction]").val("Delete");
          Confirm("Delete","Delete "+donation+" donation", "Delete","#DonationEditForm");
        }
      },
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
  if(title != "Edit") {
    $("#DeleteButton").hide();
  }
  /* so PHP has access to the donation id
         to change or delete the donation rec */
  $("#EditDonationID").val(donation_id);
  /* this was only needed for the donation_association rec
   *$("#OldPrimaryDonorID").val($("#DonorID"+donation_id).html());*}*/
}

function validateDonation() {
  var ErrMsg = new String("");
  if(isNaN(Date.parse($("#EditDate").val()))) {
    ErrMsg=buildErrorMessage(ErrMsg,'Invalid Date');
  }
  if($("#EditAmount").val() <= 0) {
    ErrMsg=buildErrorMessage(ErrMsg,'Amount is required');
  }
  if($("#EditPrimaryDonor").val() == 0) {
    ErrMsg=buildErrorMessage(ErrMsg,'Select Primary Donor');
  }
  if(ErrMsg.length) {
    displayError("Incomplete Entries",ErrMsg);
    return false;
  }
  return true;
}

function buildErrorMessage(ErrMsg,newerror) {
  return ErrMsg.concat(ErrMsg.length ? "<br />" : "",newerror);  
}
