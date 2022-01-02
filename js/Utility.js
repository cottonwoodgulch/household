{* javascript for house/Utility page *}
<script>
function AddressLookup() {
  /* display dialog for user to paste list of names
   * php will look up salutation & address & create csv
  */
  $("#AddressLookupDialog").dialog({
    dialogClass: "no-close",  // hide close button in top corner
    height: "auto",
    width: "auto",
    closeOnEscape: true,
    title: "Address Lookup - paste list of names",
    buttons: [
      {
        text: "Submit",
        type: "button",
        click: function() {
          $("#AddressLookupForm").submit();
          $("#AddressText").val('');
          $(this).dialog("destroy");
        }
      },
      {
        text: "Cancel",
        click: function() {
          $("#AddressText").val('');
          $(this).dialog("destroy");
        }
      }
    ]
  });
}

</script>
