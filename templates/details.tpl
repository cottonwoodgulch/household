{extends file="page.tpl"}

{block name="js"}
  <link rel="stylesheet" href="css/jquery-ui.css" />
  <script src="vendor/components/jqueryui/jquery-ui.js"></script>
{/block}

{block name="dialog"}
  {include file="js/LookupDialog.html"}
  {include file="js/ConfirmDialog.html"}
  {include file="js/ErrorDialog.html"}
  {include file="js/AddMemberDialog.html"}
{/block}

{block name="content"}
  <div id="DetailsDiv">
  {include file="js/ErrorDialog.html"}
  {include file="js/NewHouseDialog.html"}
  <form id="details_form" action="details.php" method="post"
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
    <button id="SaveButton" name="saveChange">Save</button>
    <input type="button" id="NewButton" name="new" onClick="newHouse()" value="New">
  </form>

  <br />

  <form id="members_form" action="details.php?action=move" method="post"
     style="border: thin solid black; padding: 1%;">
    <input type="hidden" id="selected_contact_id" name="selected_contact_id">

    <table class="edit">
      <tr>
        <th id="members_label">Members</th>
      </tr>
      {foreach $house->members as $tx}
      <tr>
        <td><button type="button" onClick="moveMemberDialog('Look up another household to place {if strlen($tx.nickname)} {$tx.nickname} {else} {$tx.first_name} {/if} {$tx.primary_name}',
         'details.php', {$tx.contact_id});" style="vertical-align:top;">
         <img src="images/edit.png" title="Move"></button>
        </td>
        <td>{$tx.first_name}</td>
        <td>{$tx.nickname|NickName}</td>
        <td>{$tx.middle_name}</td>
        <td>{$tx.primary_name}</td>
        <td>{$tx.degree}</td>
      </tr>
      {/foreach}
    </table>
  </form>

  <form id="add_form" action="details.php?action=add" method="post"
     style="none;">
    <input type="button" 
            onClick="addMemberDialog('Add a member to this household, 'details.php')"
            style="vertical-align: top;" value="Add Member">

  </form>
  </div>
{/block}