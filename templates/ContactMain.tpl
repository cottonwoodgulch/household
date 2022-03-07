{extends file="page.tpl"}

{block name="js"}
  <script src="js/Contact.js" defer="true"></script>
  {include file="js/ConfirmDialog.html"}
{/block}

{block name="dialog"}
  {include file="templates/ContactPage/ContactDialog.html"}
{/block}

{block name="content"}
  {if isset($cdata)}
    <table class="edit"> {* main *}
    <tr><td>
    <table class="edit"> {* contact, coordinates *}
      <tr>
      
      <td>
      <table class="edit"> {* contact *}
        {include file="templates/ContactPage/contact.tpl"}
      </table> {* contact *}
      </td>

      <td>
      <table class="edit"> {* phones, emails, addresses *}
      {* Phones *}
      {include file="templates/ContactPage/phones.tpl"}

      {* Emails *}
      {include file="templates/ContactPage/emails.tpl"}

      {* Addresses *}
      {include file="templates/ContactPage/addresses.tpl"}
      
      </table> {* phones, emails, addresses *}
      </td></tr>

      {* relationships *}
      {include file="templates/ContactPage/relationships.tpl"}
    
    </table>  {* contact, coordinates *}
    </td>

    <td>
    <table class="edit"> {* groups *}
      <tr><td style="font-size: .81em;">
        <button type="button" class="mini-button"
             onClick="editGroup(0)" title="Add">Groups:</button>
      </td></tr>
      {foreach from=$cdata->groups item=$cx}
        <tr><td style="padding-left: 7px;"><button type="button"
             class="mini-button" title="Show Roster" onClick=
               "rosterLookup({$cx.year},'{$cx.group|escape:'quotes'}',
               {$cx.group_id},{$cx.roster_id})"
             style="border: 0; font-size: .81em;">
          {if $cx.role != ''}{$cx.role}, {/if}
          {if $cx.year > 0}{$cx.year} {/if}
          {$cx.group}</button>
        </td></tr>
      {/foreach}
    </table>  {* groups *}
    </td>

    {* Rosters *}
    <td>
    <table class="edit" id="rosters">
    </table>
    <form method="post" action="contact.php" id="RosterMemberDeleteForm">
      <input type="hidden" name="ContactID" value="{$cdata->contact_id}">
      <input type="hidden" name="EditRosterID">
    </form>
    </td>
    
    </tr>
  </table> {* main *}

  {* Notes *}
  <table class="edit">
  <tr><td style="font-size: .81em;">
    <button type="button" class="mini-button"
             onClick="editNote(0)" title="Add"
             style="border: 0">Notes:</button>
  </td></tr>
  {foreach from=$cdata->notes item=$cx}
    <tr><td class="label" style="font-size: .81em;">
      <button type="button" class="mini-button" title="Edit"
            onClick="editNote({$cx.note_id})">
             {$cx.ddate|date_format:"%m/%d/%Y"}:</button></td>
        <td style="font-size: .81em;"
           id="note{$cx.note_id}">{$cx.note}</td>
        <input type="hidden" id="ddate{$cx.note_id}"
           value="{$cx.ddate}">
    </tr>
  {/foreach}
  </table>

  {/if}
  <table><tr><td>
  <form id="FindContactForm" action="contact.php" method="post">
    <input type="hidden" name="CurrentHouseID" id="CurrentHouseID">
    <input type="hidden" name="ContactID" id="ContactID">
    <label for="FindContact">Find: </label>
    <input id="FindContact" name="FindContact" value="" autofocus>
  </form></td><td>
  <button onClick="editContact(0)">Add Contact</button></td>
  </table>
{/block}
