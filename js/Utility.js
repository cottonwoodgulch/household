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
</script>
