{extends file="page.tpl"}

{block name="dialog"}
  <div id="MoveMemberDialog" style="display: none">
  <form method="post" action="donations.php"
        id="MoveMemberForm">
    <table class="edit">
      <tr>
        <td>{$tx.first_name}</td>
        <td>"{$tx.nickname}"</td>
        <td>{$tx.middle_name}</td>
        <td>{$tx.primary_name}</td>
      </tr>
      { * address = a1, a2, city, state, country, postal_code * }
      <tr>New Household Address</tr>
      <tr><td class="label">Address 1</td>
          <td><input id="EditAddress1" value="Address1"></td>
      </tr>
      <tr><td class="label">Address 2</td>
          <td><input id="EditAddress2" value="Address2"></td>
      </tr>
      <tr><td class="label">City</td>
          <td><select id="EditCity" name="City"></td>
      </tr>
      <tr><td class="label">State</td>
          <td><input id="EditState" value="State"></td>
      </tr>
      <tr><td class="label">Country</td>
          <td><input id="EditCountry" value="Country"></td>
      </tr>
      <tr><td class="label">Postal Code</td>
          <td><input id="EditPostalCode" value="Postal Code"></td>
      </tr>
  </table>
  </form> {* MoveMemberForm *}
  </div> {* MoveMemberDialog *}
{/block}

{block name="dialog"}
  <div id="MemberDialog" style="display: none">
  <form method="post" action="donations.php"
        id="MemberForm">
    <table class="edit">
      <tr>
        <td>{$tx.first_name}</td>
        <td>"{$tx.nickname}"</td>
        <td>{$tx.middle_name}</td>
        <td>{$tx.primary_name}</td>
      </tr>
      <tr>New Household Address</tr>
      <tr>
        <td><select id="SelectContact" name="SelectContact">
          {foreach from=$contact_search_list item=fl}
            <option value="{$fl.primary_name}></option>
          {/foreach}
        </td>
        <td><input id="EditAddress1" value="Address1"></td>
      </tr>
  </table>
  </form> {* MemberForm *}
  </div> {* MemberDialog *}
{/block}

{block name="dialog"}
  <div id="SearchDialog" style="display: none">
  <form method="post" action="details.php"
        id="SearchForm">
    <table class="edit">
      <tr>
        <td>{$tx.first_name}</td>
        <td>"{$tx.nickname}"</td>
        <td>{$tx.middle_name}</td>
        <td>{$tx.primary_name}</td>
      </tr>
      <tr>New Household Address</tr>
      <tr>
        <td><select id="SelectContact" name="SelectContact">
          {foreach from=$contact_search_list item=fl}
            <option value="{$fl.primary_name}></option>
          {/foreach}
        </td>
        <td><input id="EditAddress1" value="Address1"></td>
      </tr>
  </table>
  </form> {* MemberForm *}
  </div> {* MemberDialog *}
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
        <td>{$tx.first_name}</td>
        <td>"{$tx.nickname}"</td>
        <td>{$tx.middle_name}</td>
        <td>{$tx.primary_name}</td>
        <td>{$tx.degree}</td>
        <td><button type="button" onClick="moveMember($tx.contact_id);"
               style="vertical-align: top;">
               <img src="images/edit.png" title="Move">
        </td>
      </tr>
      {/foreach}
    </table>

    <button type="button" onClick="addMember({$house->hd.household_id})"
            style="vertical-align: top;">Add Member</button>

  </form>
{/block}
