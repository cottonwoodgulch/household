/* javascript for house/Contacts page */

$("#FindContact").autocomplete({
  minLength: 3,
  source: function(request, response) {
    $.ajax({
      type: "GET",
      url: "ajax/AddLookup.php?value="+$("#FindContact").val(),
      dataType: "json",
      success: function(res_html, textStatus, xhr) {
        response(res_html);
      },
      error: function(xhr, textStatus, errorThrown) {
        $("#content").html(textStatus+errorThrown);
      }
    })
  },
  select: function(event, ui) {
    $("#ContactID").val(ui.item.value);
    $("#CurrentHouseID").val(ui.item.hid);
    $("#FindContactForm").submit();
  }
});

function cityLookup(tthis) {
  /* when user leaves the zip field, if there are 5 numeric digits,
  try to find city and state for zip code */
  let zip=new String($(tthis).val());
  //console.log('zip: '+zip);
  let answer="";
  if(zip.length >= 5) {
    zip=zip.slice(0,5);
    if(zip.match('[0-9]{5}') == zip) {
      //$("#Answer").html('zip is 5 numeric: '+zip);
      //console.log('zip is 5 numeric');
      $.ajax({
        type: "GET",
        url: "ajax/CityLookup.php?zip="+zip,
        dataType: "json",
        success: function(res_html, textStatus, xhr) {
          //console.log('success');
          $("#EditCity").val(res_html.city);
          $("#EditState").val(res_html.state);
        },
        error: function(xhr, textStatus, errorThrown) {
          $("#content").html(textStatus+errorThrown);
        }
      });
    }
  }
}

function rosterLookup(year,group,group_id) {
  /* display group members as table rows in table id=rosters */
  $.ajax({
    type: "GET",
    url: "ajax/RosterLookup.php?year="+year+"&group_id="+group_id,
    dataType: "json",
    success: function(res_html, textStatus, xhr) {
      console.log('success');
      gl="<tr><th>"+year+" "+group+"</th></tr>";
      res_html.forEach(function(element) {
        middle=element.middle.length>0 ? " "+element.middle+" " : " ";
        role=(element.role=='Trekker' || element.role=='') ?
            "" : " ("+element.role+")";
        namelink='<a href="contact.php?cid='+element.contact_id+
          '">'+element.first+middle+element.last+'</a> '+role;
        gl+='<tr><td>'+namelink+'</td></tr>';
      });
    $("#rosters").html(gl);
    },
    error: function(xhr, textStatus, errorThrown) {
      console.log('failure');
      $("#content").html(textStatus+errorThrown);
    }
  });
}

/* editPhone, editEmail, editAddress manage both edit and add based on 
   the phone_id, email_id, or address_id, which is passed as 0 if it's add */
function editPhone(phone_id) {
  $("#EditPhoneID").val(phone_id);
  if(phone_id && ($("#Formatted"+phone_id).val() == 1)) {
    $("#EditPhoneType").val($("#phone_type_id"+phone_id).val());
    $("#EditNumber").val($("#Number"+phone_id).val());
    $("#EditFormatted").attr("checked",true);
  }
  else if(phone_id) {
    $("#EditPhoneType").val($("#phone_type_id"+phone_id).val());
    // format the number
    let pn=$("#Number"+phone_id).val();
    let number=pn.slice(0,3)+'-'+pn.slice(3,6)+'-'+pn.slice(6,10);
    if(pn.length > 10)number+=' x'+pn.slice(10);
    $("#EditNumber").val(number);
    $("#EditFormatted").attr("checked",false);
  }
  else {
    // adding - default to phone type 3 = mobile
    $("#EditPhoneType").val(3);
    $("#EditNumber").val("");
    $("#EditFormatted").attr("checked",false);
  }
  CoordinateDialog(phone_id ? "Edit" : "Add","Phone");
}

function editEmail(email_id) {
  $("#EditEmailID").val(email_id);
  // default to email type 1 = Personal
  $("#EditEmailType").val(email_id ? $("#email_type_id"+email_id).val() : 1);
  $("#EditEmail").val(email_id ? $("#email"+email_id).val() : "");
  CoordinateDialog(email_id ? "Edit" : "Add","Email");
}

function editAddress(address_id) {
  $("#EditAddressID").val(address_id);
  $("#EditAddressType").
      val(address_id ? $("#address_type_id"+address_id).val() : 1);
  $("#EditAddr1").val(address_id ? $("#addr1"+address_id).val() : "");
  $("#EditAddr2").val(address_id ? $("#addr2"+address_id).val() : "");
  $("#EditCity").val(address_id ? $("#city"+address_id).val() : "");
  $("#EditState").val(address_id ? $("#state"+address_id).val() : "");
  $("#EditZip").val(address_id ? $("#zip"+address_id).val() : "");
  $("#EditCountry").
      val(address_id ? $("#country"+address_id).html() : "United States");
  CoordinateDialog(address_id ? "Edit" : "Add","Address");
}

function addCoordinate(coordinate_type) {
  /* to tell contact.php which coordinate type we are adding,
       Address, Phone, or Email */
  $("#Edit"+coordinate_type+"ID").val('add');
  CoordinateDialog("Add",coordinate_type);
}

function CoordinateDialog(title,coordinate) {
  /* title is Add or Edit
     coordinate is Phone, Email, or Address
  */
  $("#"+coordinate+"EditDialog").dialog({
    dialogClass: "no-close",  // hide close button in top corner
    height: "auto",
    width: "auto",
    closeOnEscape: true,
    title: title+" "+coordinate,
    buttons: [
      {
        text: "Delete",
        type: "button",
        id: "DeleteButton",
        click: function() {
          //$("#DonationEditForm input[name=buttonAction]").val("Delete");
          Confirm("Delete","Delete "+coordinate,
                  "Delete","#"+coordinate+"EditForm");
        }
      },
      {
        text: "Save",
        type: "button",
        click: function() {
            $("#"+coordinate+"EditForm input[name=buttonAction]").
               val(title);
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
  if(title != "Edit") {
    $("#DeleteButton").hide();
  }
}
