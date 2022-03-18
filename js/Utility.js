{* javascript for house/Utility page *}
<script>
function TextInput(buttonAction,title) {
  $('#TextInputForm input[name=buttonAction]').val(buttonAction);
  $("#TextInputDialog").dialog({
    dialogClass: "no-close",  // hide close button in top corner
    height: "auto",
    width: "auto",
    closeOnEscape: true,
    title: title,
    buttons: [
      {
        text: "Submit",
        type: "button",
        click: function() {
          $("#TextInputForm").submit();
          $("#InputText").val('');
          $(this).dialog("destroy");
        }
      },
      {
        text: "Cancel",
        click: function() {
          $("#InputText").val('');
          $(this).dialog("destroy");
        }
      }
    ]
  });
}

/* just set buttonAction and submit the floating UtilityForm */
function UtilitySubmit(buttonAction) {
  $('#UtilityForm input[name=buttonAction]').val(buttonAction);
  $('#UtilityForm').submit();
}

function Merge() {
  $("#MergeDialog").dialog({
    dialogClass: "no-close",  // hide close button in top corner
    height: "auto",
    width: "auto",
    closeOnEscape: true,
    title: 'Merge Duplicate Contacts',
    buttons: [
      {
        text: "Merge",
        type: "button",
        click: function() {
          $("#MergeForm input[name=buttonAction]").val('Merge');
          $("#MergeForm").submit();
        }
      },
      {
        text: "Cancel",
        click: function() {
          $("#MergeContact1").val('');
          $("#MergeContact2").val('');
          $(this).dialog("destroy");
        }
      }
    ]
  });

  MergeLookup('MergeContact1','MergeFrom');
  MergeLookup('MergeContact2','MergeTo');
}

function MergeLookup(MergeInput,MergeContact_ID) {
  $("#"+MergeInput).autocomplete({
    minLength: 3,
    source: function(request, response) {
      $.ajax({
        type: "GET",
        url: "ajax/AddLookup.php?value="+$("#"+MergeInput).val(),
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
      $("#"+MergeContact_ID).val(ui.item.value);
    }
  });
}

</script>
