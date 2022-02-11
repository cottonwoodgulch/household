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
      $("#content").html(textStatus+errorThrown);
    }
  });
}

/* editContact, editPhone, editEmail, editAddress manage
   both edit and add based on the contact_id, phone_id,
   email_id, or address_id, which is passed as 0 if it's add */
function editContact(contact_id) {
  $("#EditContactID").val(contact_id);
  /* pw is never retrieved, can only be set */
  $('#EditPassword').val('');
  if(contact_id) {
    $('#EditContactType').val($("#contact_type_id").val());
    $('#EditFirst').val($('#first').html());
    $('#EditMiddle').val($('#middle').html());
    $('#EditLast').val($('#last').html());
    $('#EditDegree').val($('#degree').val());
    $('#EditNickname').val($('#nickname').html());
    $('#EditDOB').val($('#dob').val());
    if($('#gender').html().length>0) {
      $('#'+($('#gender').html())).prop('checked',true);
    }
    $('#EditDeceased').prop('checked',
      $('#deceased').html() == 'yes');
    $('#EditUsername').val($('#username').val());
    $('#EditRedRocks').prop('checked',
      $('#redrocks').val() != 0);
  }
  else {  // add
    /* default Contact Type to individual */
    $("#EditContactType").val(1);
    $('#EditFirst').val('');
    $('#EditMiddle').val('');
    $('#EditLast').val('');
    $('#EditDegree').val(0);
    $('#EditNickname').val('');
    $('#EditDOB').val('');
    $('#EditGender').val('');
    //$('#Male').prop('checked',false);
    $('#EditDeceased').prop('checked',false);
    $('#EditUsername').val('');
    $('#EditRedRocks').prop('checked',false);
  }
  CoordinateDialog(contact_id ? "Edit" : "Add","Contact");
}

/* since there can be multiple phones, e-mails, addresses,
    relationships, & notes, each one is identified by the
    field name + the _id */
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

function editRelationship(relationship_id) {
  $("#EditRelationshipID").val(relationship_id);
  if(relationship_id) {
    $("#EditRelationshipType").
      val($("#relationship_type_id"+relationship_id).val());
  $("#RelativeID").val($("#relative_id"+relationship_id).val());
    $("#EditRelation").val($("#relative_name"+relationship_id).val());
  }
  else {
    $("#EditRelationshipType").val(1);
    $("#RelativeID").val(0);
    $("#EditRelation").val("");
  }
  CoordinateDialog(relationship_id ? "Edit" : "Add",
      "Relationship",editRelation,
      function() {$("#EditRelation").autocomplete("destroy")}
  );
}

function editRelation() {
  $("#EditRelation").autocomplete({
  minLength: 3,
  source: function(request, response) {
    $.ajax({
      type: "GET",
      url: "ajax/AddLookup.php?value="+$("#EditRelation").val(),
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
    //console.log('setting RelativeID to: '+ui.item.value);
    $("#RelativeID").val(ui.item.value);
    //$("#EditRelation").val(ui.item.name);
  }
  });
  $("#EditRelation").select();
}

function editNote(note_id) {
  console.log('in editNote');
  $("#EditNoteID").val(note_id);
  if(note_id) {
    $("#EditDate").val($("#ddate"+note_id).val());
    $("#EditNote").val($("#note"+note_id).html());
    CoordinateDialog("Edit","Note");
  }
  else {
    today=new Date();
    $("#EditDate").val(today.getFullYear()+'-'+
       new String(today.getMonth()+1).padStart(2,'0')+'-'+
       new String(today.getDate()).padStart(2,'0'));
    
    $("#EditNote").val('');
    CoordinateDialog("Add","Note");
  }
}

function CoordinateDialog(title,coordinate,
        startfunction=function(){},
        cancelfunction=function(){}) {
  /* title is Add or Edit
     coordinate is Contact Phone, Email, or Address  */
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
          cancelfunction();
          $(this).dialog("destroy");
        }
      }
    ]
  });
  /* household system can't delete contacts at this point */
  if(title != "Edit" || coordinate == "Contact") {
    $("#DeleteButton").hide();
  }
  startfunction();
}
