in details.tpl, the household field needs

            <input id="roster_year" name="roster_year"
               value="{$roster_members->roster_year}"
               onKeyup="getRosters(this.value)"
               onClick="selectAll('#roster_year')"
               autocomplete="off" size="5"/>

and a <td id="household_error">

onKeyup="validateHousehold(this.value,{$house->household_id})"
               
               
/* ajax_details.js */

function validateHousehold(hname,hid) {
  $.ajax({
    type: "GET",
    url: "ajax/ajax_details.php",
    data: "hname="+hname&"hid="+hid,
    dataType: "text",
    done: function(res_html, textStatus, errorThrown) {
      $("#house_name_error").text(res_html);
    },
    error: function(xhr, textStatus, errorThrown) {
      $("#content").html(textStatus);
    }
  })
}
