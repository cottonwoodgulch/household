{extends file="page.tpl"}

{block name="js"}
  {include file="js/ErrorDialog.html"}
  {include file="js/NewHouseDialog.html"}
  {include file="js/ConfirmDialog.html"}
  {include file="js/Dialog.html"}
{/block}

{block name="content"}
  <form id="details_form" action="details.php" method="post"
     style="border: thin solid black; padding: 1%;">
    <table class="edit">
      <tr>
        <td class="label">
          <label for="house_name">Household Name</label>
        </td>
        <td>
          <input name="house_name" value="{$house->hd.name}" 
            id="house_name" size="50" autocomplete="off"/>
        </td>
      </tr>
      <tr>
        <td class="label">
          <label for="salutation">Salutation</label>
        </td>
        <td>
          <input name="salutation" value="{$house->hd.salutation}"
             size="50" autocomplete="off"/>
        </td>
      </tr>
      <tr>
        <td class="label">
          <label for="mail_name">Mail Name</label>
        </td>
        <td>
          <input name="mail_name" value="{$house->hd.mailname}" size="50" autocomplete="off"/>
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
      <tr>
        <td><input type="radio"
          {if $house->hd.address_id==0} checked{/if}
          name="prefaddress" value="0"></td>
        <td>None</td>
      </tr>
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
    <input type="hidden" name="buttonAction">
    <input type="submit" value="Save Household" onClick="{
        $('#details_form input[name=buttonAction]').val('saveChange');
        $('#details_form').submit();
      }">
    <input type="button" onClick="newHouse()" value="New Household">
    <input type="button" onClick="Confirm('Delete',
      'Really delete {$house->hd.name|escape} Household',
      'Delete', '#details_form')" value="Delete Household">
    <input type="button"
      onClick="lookupHouse('Look up new default household','details.php','selectHouse')"
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
      <button type="button" value="Move" 
        onClick="moveMember('{$tx.nickname}', '{$tx.first_name}', '{$tx.primary_name}', '{$tx.contact_id}', 'details.php')" 
        style="vertical-align:top;">
        <img src="images/edit.png" title="Move">
        </button>
      </td>
      <td>{$tx.first_name} {$tx.nickname|NickName} {$tx.middle_name} 
           {$tx.primary_name} {$tx.degree}</td>
    </tr>
    {/foreach}
  </table>
  
  <input type="button" onClick="addMember({$house->hd.household_id}, 'details.php')"
    style="vertical-align:top;" value='Add Member'>

{/block}
