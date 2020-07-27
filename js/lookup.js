/* lookup.js - functions for look up household dialog 
     by member name or household name */

function lookupHouseDialog() {
  $("#LookupDialog").dialog ({
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
      $("#LookupForm input[name=buttonAction]").val("house");
      $("#LookupForm").submit();
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
      $("#LookupForm input[name=buttonAction]").val("member");
      $("#LookupForm").submit();
    }
  })
}

function lookupHouse() {
  /* look up household for default */
  lookupHouseDialog();
  $("#LookupInfo").html("Lookup new default household");
}

function lookupMoveHouse() {
  /* look up target household to move member */
  lookupHouseDialog();
  $("#LookupInfo").html("Hello from Tom");
  
}

