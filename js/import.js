/* js functions for donation import screen */

function di_submit(buttonAction) {
  $('#buttonAction').val(buttonAction);
  $('#diForm').submit();
}

function switchhouse() {
  $('#hid').val($("#selecthouse").val());
  $('#buttonAction').val('SwitchHouse');
  $('#diForm').submit();
}
