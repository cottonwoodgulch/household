/* js functions for dialogs on details.php and details.tpl */

function lookupHouseDialog() {
  $("#MoveMemberDialog").dialog ({
    classes: {"ui-dialog": "no-titlebar"},
    height: "auto",
    width: "auto",
    closeOnEscape: true,
    /* title: title, */
    buttons: [
      {
        text: "Cancel",
        click: function() {
          $("#NameLookup").val("");
          $("#NameLookup").autocomplete("destroy");
          $("#MemberLookup").val("");
          $("#MemberLookup").autocomplete("destroy");
          $(this).dialog("destroy");
        }
      }
    ]
  });
  $("#NameLookup").autocomplete({
    minLength: 3,
    position: {my: "left top", at: "left bottom", collision: "fit"},
    source: function(request, response) {
      $.ajax({
        type: "GET",
        url: "ajax/NameLookup.php?value="+$("#NameLookup").val(),
        dataType: "json",
        success: function(res_html, textStatus, xhr) {
          //alert('name success: '+res_html);
          response(res_html);
          res_html="";
        },
        error: function(xhr, textStatus, errorThrown) {
          $("#content").html(textStatus+errorThrown);
        }
      })
    },
    select: function(event, ui) {
      $("#MoveMemberForm input[name=buttonAction]").val("house");
      $("#MoveMemberForm").submit();
    }
  })
  $("#MemberLookup").autocomplete({
    minLength: 3,
    position: {my: "left top", at: "left bottom", collision: "fit"},
    source: function(request, response) {
      $.ajax({
        type: "GET",
        url: "ajax/MemberLookup.php?value="+$("#MemberLookup").val(),
        dataType: "json",
        success: function(res_html, textStatus, xhr) {
          response(res_html);
          res_html="";
        },
        error: function(xhr, textStatus, errorThrown) {
          $("#content").html(textStatus+errorThrown);
        }
      })
    },
    select: function(event, ui) {
      $("#MoveMemberForm input[name=buttonAction]").val("contact");
      $("#MoveMemberForm").submit();
    }
  })
}

function lookupContactDialog() {
  //pass
}

function moveMember() {
  /* lookup household from db to move this person*/
  lookupHouseDialog();
  $("LookupInfo").html("<p>Move member to another household</p>");
}

function addMember() {
  /* lookup contact from db to add to this household*/
  lookupContactDialog();
  $("LookupInfo").html("<p>Add a contact to this household</p>");
}