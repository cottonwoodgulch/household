{extends file="page.tpl"}

{block name="content"}
  {include file="js/ErrorDialog.html"}
  {include file="js/NewHouseDialog.html"}
  {include file="js/ConfirmDialog.html"}
  {include file="js/LookupDialog.html"}
  {*{include file="js/AddDialog.html"}*}

  <form id="details_form" action="details.php" method="post"
     style="border: thin solid black; padding: 1%;">
    <table class="edit">
      <tr>
        <td class="label">
          <label for="house_name">Household Name</label>
        </td>
        <td>
          <input name="house_name" value="{$house->hd.name}" 
            id="house_name" autocomplete="off"/>
        </td>
      </tr>
      <tr>
        <td class="label">
          <label for="salutation">Salutation</label>
        </td>
        <td>
          <input name="salutation" value="{$house->hd.salutation}"
             autocomplete="off"/>
        </td>
      </tr>
      <tr>
        <td class="label">
          <label for="mail_name">Mail Name</label>
        </td>
        <td>
          <input name="mail_name" value="{$house->hd.mailname}" autocomplete="off"/>
        </td>
      </tr>
    </table>

    <table class="edit">
      <tr>
        <th colspan="2" id="preferred_label">Preferred</th>
      </tr>
      {foreach $house->addresses as $tx}
      <tr>
        <td><input type="radio"
          {if $tx.address_id==$house->hd.address_id} checked{/if}
          name="prefaddress" value="{$tx.address_id}"></td>
        <td>{$tx.address_type}</td>
        <td>{$tx.street_address_1}</td>
        <td>{$tx.street_address_2}</td>
        <td>{$tx.city}</td>
        <td>{$tx.state}</td>
        <td>{$tx.postal_code}</td>
        <td>{$tx.country}</td>
        <td>({$tx.first_name})</td>
      </tr>
      {/foreach}
    </table>
    
    <table class="edit">
      {foreach $house->emails as $tx}
      <tr>
        <td><input type="checkbox" {if $tx.preferred}checked{/if}
          name="prefemail{$tx.email_id}" value="{$tx.email_id}"></td>
        <td>{$tx.email_type}</td>
        <td>{$tx.email}</td>
        <td>({$tx.first_name})</td>
      </tr>
      {/foreach}
    </table>

    <br />
    <button type="submit" name="buttonAction" value="saveChange">Save</button>
    <input type="button" onClick="newHouse()" value="New">
    <input type="button" onClick="Confirm('Delete',
      '{$house->hd.name} Household','#details_form')" value="Delete">
    <input type="button"
      onClick="lookupHouse('Look up new default household','details.php')"
      value="Look Up Household">
  </form>

  <br />

  <table class="edit" style="border: thin solid black; padding: 1%;">
    <tr>
      <th id="members_label">Members</th>
    </tr>
    {foreach $house->members as $tx}
    <tr>
      <td>
      <button type="button" value="Move" onClick="{
        $('#SelectedContactID').val({$tx.contact_id});
        var contact_name = '{if strlen($tx.nickname)} {$tx.nickname} {else} {$tx.first_name} {/if} {$tx.primary_name}';
        $('#SelectedContactName').val(contact_name);
        lookupHouse('Select another household to place '+contact_name,
        'details.php', 'moveMember');}" style="vertical-align:top;">
        <img src="images/edit.png" title="Move">
        </button>
      </td>
      <td>{$tx.first_name}</td>
      <td>{$tx.nickname|NickName}</td>
      <td>{$tx.middle_name}</td>
      <td>{$tx.primary_name}</td>
      <td>{$tx.degree}</td>
    </tr>
    {/foreach}
  </table>
  
  <input type="button" onClick="{
    $('#AddHouseID').val({$house->hd.household_id});
    lookupMember('Add a member to this household', 'details.php', 'addMember');}" 
    style="vertical-align:top;" value='Add Member'>
{/block}
