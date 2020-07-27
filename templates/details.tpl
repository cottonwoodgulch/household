{extends file="page.tpl"}

{block name="js"}
  <link rel="stylesheet" href="css/jquery-ui.css" />
  <script src="vendor/components/jqueryui/jquery-ui.js"></script>
  <script src="js/details.js"></script>
{/block}

{block name="dialog"}
  <div id="MoveMemberDialog" style="display: none">
  <div id="LookupInfo"></div>
  <form method="post" id="MoveMemberForm" action="details.php">
    <input type="hidden" name="buttonAction">
    <table class="edit">
      <tr>
        <td class="label" id="LookupByName">by Household Name</td>
        <td>
          <input id="NameLookup" name="HouseName" value="">
        </td>
      </tr>
      <tr><td>&nbsp;</td></tr>
      <tr>
        <td class="label">by a Member's Name</td>
        <td>
          <input id="MemberLookup" name="MemberName" value="">
        </td>
      </tr>
    </table>
  </form> {* MoveMemberForm *}
  </div> {* MoveMemberDialog *}

  <div id="AddMemberDialog" style="display: none">
  <h2>Look up contact to add to this household</h2>
  <form method="post" id="AddMemberForm" action="details.php">
    <input type="hidden" name="buttonAction">
    <table class="edit">
      <tr>
        <td class="label" id="LookupByName">by Household Name</td>
        <td>
          <input id="NameLookup" name="HouseName" value="">
        </td>
      </tr>
      <tr><td>&nbsp;</td></tr>
      <tr>
        <td class="label">by a Member's Name</td>
        <td><input id="MemberLookup" name="MemberName" value=""></td>
      </tr>
    </table>
  </form> {* AddMemberForm *}
  </div> {* AddMemberDialog *}
{/block}

{block name="content"}
  <form id="details_form" action="details.php?action=save" method="post"
     style="border: thin solid black; padding: 1%;">
    <table class="edit">
      <tr>
        <td class="label">
          <label for="house_name">Household Name</label>
        </td>
        <td>
          <input name="house_name" value="{$house->hd.name}" 
            id="house_name">
        </td>
        <td id="house_name_error"></td>
      </tr>
      <tr>
        <td class="label">
          <label for="salutation">Salutation</label>
        </td>
        <td>
          <input name="salutation" value="{$house->hd.salutation}"/>
        </td>
      </tr>
      <tr>
        <td class="label">
          <label for="mail_name">Mail Name</label>
        </td>
        <td>
          <input name="mail_name" value="{$house->hd.mailname}"/>
        </td>
      </tr>
    </table>
    <br />

    <table class="edit">
      <tr>
        <th id="preferred_label">Preferred</th>
      </tr>
      {foreach $house->addresses as $tx}
      <tr>
        <td><input type="radio"
          {if $tx.address_id==$house->hd.address_id} checked{/if}
          name="pref" value="{$tx.address_id}"></td>
        <td>{$tx.address_type}</td>
        <td>{$tx.street_address_1}</td>
        <td>{$tx.street_address_2}</td>
        <td>{$tx.city}</td>
        <td>{$tx.state}</td>
        <td>{$tx.postal_code}</td>
        <td>{$tx.country}</td>
      </tr>
      {/foreach}
    </table>

    <br />
    <input type="submit" id="saveDetailsButton" value="Save">
  </form>

  <br />

  <form id="members_form" action="details.php?action=move" method="post"
     style="border: thin solid black; padding: 1%;">

    <table class="edit">
      <tr>
        <th id="members_label">Members</th>
      </tr>
      {foreach $house->members as $tx}
      <tr>
        <td><button type="button" onClick="moveMember()"
          style="vertical-align: top;">
          <img src="images/move.jpg" title="Move">
        </td>
        <td>{$tx.first_name}</td>
        <td>"{$tx.nickname}"</td>
        <td>{$tx.middle_name}</td>
        <td>{$tx.primary_name}</td>
        <td>{$tx.degree}</td>
      </tr>
      {/foreach}
    </table>

    <button type="button" onClick="addMember()"
            style="vertical-align: top;">Add Member</button>

  </form>
{/block}
