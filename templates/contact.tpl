{extends file="page.tpl"}

{block name="js"}
  <script src="js/Contact.js" defer="true"></script>
  {include file="js/ConfirmDialog.html"}
{/block}

{block name="dialog"}
  {include file="ContactDialog.html"}
{/block}

{block name="content"}
  {if isset($cdata)}
    <table class="edit"> {* main *}
    <tr><td>
    <table class="edit"> {* contact, coordinates *}
      <tr>
      
      <td>
      <table class="edit"> {* contact *}
        {include file="tcont/contact.tpl"}
      </table> {* contact *}
      </td>

      <td>
      <table class="edit"> {* phones, emails, addresses *}
      {* Phones *}
      {include file="tcont/phones.tpl"}

      {* Emails *}
      {include file="tcont/emails.tpl"}

      {* Addresses *}
      {include file="tcont/addresses.tpl"}
      
      </table> {* phones, emails, addresses *}
      </td>
      
      </tr>
      <tr><td>Relationships Here?</td></tr>
    </table>  {* contact, coordinates *}
    </td>

    <td>
    <table class="edit"> {* groups *}
      <tr><td colspan="2" style="font-size: .9em;">Groups:</td></tr>
      {foreach from=$cdata->groups item=$cx}
        <tr><td>&nbsp;</td><td><button type="button"
             class="mini-button" title="Show Roster" onClick=
               "rosterLookup({$cx.year},'{$cx.group}',{$cx.group_id})"
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
    </td>
    
    </tr>
  </table> {* main *}

  {* Notes *}
  <table class="edit" style="font-size: .81em;">
    <tr><td colspan="2">Notes:</td></tr>
    {foreach from=$cdata->notes item=$cx}
      <tr><td>&nbsp;</td>
      <td>{$cx.ddate}</td><td>{$cx.note}</td></tr>
    {/foreach}
  </table>

  {/if}
  <form id="FindContactForm" action="contact.php" method="post">
    <input type="hidden" name="CurrentHouseID" id="CurrentHouseID">
    <input type="hidden" name="ContactID" id="ContactID">
    <label for="FindContact">Find: </label>
    <input id="FindContact" name="FindContact" value="" autofocus>
  </form>
{/block}
