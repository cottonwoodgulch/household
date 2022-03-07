/* js functions for donation import screen */

function di_submit(buttonAction,subAction='') {
  /* for Next, Re-Load, and MarkDone, just use ButtonAction
     to update salutation, mailname that don't require a popup dialog,
     using value from import record */
  $('#diForm input[name=buttonAction]').val(buttonAction);
  $('#diForm input[name=subAction]').val(subAction);
  $('#diForm').submit();
}

function switchhouse() {
  $('#diForm input[name=hid]').val($("#selecthouse").val());
  $('#diForm input[name=buttonAction]').val('SwitchHouse');
  $('#diForm').submit();
}

function editPhone(pnumber) {
  // default to phone type 3 = mobile
  $("#EditPhoneType").val(3);
  $("#EditNumber").val(pnumber);
  $("#EditFormatted").attr("checked",false);
  CoordinateDialog("Phone");
}

function editEmail(email) {
  // default to email type 1 = Personal
  $("#EditEmailType").val(1);
  $("#EditEmail").val(email);
  CoordinateDialog("Email");
}

function editAddress(addr1,city,state,zip) {
  // default to address type 1 = Home
  $("#EditAddressType").val(1);
  $("#EditAddr1").val(addr1);
  $("#EditAddr2").val("");
  $("#EditCity").val(city);
  $("#EditState").val(state);
  $("#EditZip").val(zip);
  $("#EditCountry").val("United States");
  CoordinateDialog("Address");
}

function editDonation(ddate,amount,fund,purpose,anonymous) {
  $("#EditDate").val(ddate);
  $("#EditAmount").val(amount);
  $("#EditFund option").each(function() {
    if($("#FundName").html() == $(this).html()) {
      $(this).attr("selected",true);
    }
    else {
      $(this).attr("selected",false);
    }
  });
  $("#EditPurpose").val(purpose);
  /* no field in contribs ss for anonymous */
  $("#EditAnonymous").prop("checked",false);
  $("#EditPrimaryDonor option").each(function(index) {
    $(this).prop("selected",false);
  });
  CoordinateDialog('Donation');
}

function CoordinateDialog(coordinate) {
  /* coordinate is Donation, Phone, or Email */
  $("#"+coordinate+"EditDialog").dialog({
    dialogClass: "no-close",  // hide close button in top corner
    height: "auto",
    width: "auto",
    closeOnEscape: true,
    title: "Add "+coordinate,
    buttons: [
      {
        text: "Save",
        type: "button",
        click: function() {
            /*editform="#"+coordinate+"EditForm ";*/
            $("#"+coordinate+"EditForm input[name=buttonAction]").
               val('Update');
            $("#"+coordinate+"EditForm input[name=subAction]").
               val(coordinate);
            $("#"+coordinate+"EditForm").submit();
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
