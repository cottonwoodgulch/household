{extends file="page.tpl"}

{block name="js"}
  <script src="js/import.js"></script>
{/block}

{block name="dialog"}
  {include file="templates/ImportDialog.html"}
{/block}

{block name="content"}
  <form method="post" action="import.php" id="diForm">
  <input type="hidden" id="fy" name="fy" value="{$di.fy}">
  <input type="hidden" id="recno" name="recno" value="{$di.recno}">
  <input type="hidden" name="hid" value="{$hid}">
  <input type="hidden" name="buttonAction">
  <input type="hidden" name="subAction">
  <div style="display: flex;">
  <div style="border: thin solid black; padding: 10px; width: 45%;">
  {if $hid != 0}
  <div style="font-weight: bold; font-size: 1.1em;">
  {$house->hd.mailname} household ({$house->hd.household_id})</div>
  <table class="edit">
    <tr><td class="label">Salutation:</td>
        <td>{$house->hd['salutation']}</td></tr>
    <tr><td class="label">Mail Name:</td>
        <td>{$house->hd['mailname']}</td></tr>
  </table>
  <table class="edit">
    {foreach $house->members as $tx}
      <tr><td><a href="contact.php?cid={$tx.contact_id}"
             class="filelist_normal">
        {$tx.first_name} {$tx.primary_name} {$tx.degree}</a></td></tr>
    {/foreach}
  </table>
  <table class="edit">
    {section name=x loop=5 start=-5}
      {if $house->donations[x]['amount'] >0}
      <tr><td>{$house->donations[x]['ddate']|date_format:"%m/%d/%Y"}
          {$house->donations[x]['amount']}</td>
          <td>{$house->donations[x]['fund']}</td>
          <td>{$house->donations[x]['purpose']}</td>
          <td>({$house->donations[x]['first_name']})</td>
          </tr>
      {/if}
    {/section}
  </table>
  <table class="edit">
    {foreach $house->addresses as $tx}
      <tr><td class="label">
         {if $tx.preferred == 1}* {else}&nbsp;&nbsp;{/if}
         {$tx.address_type}:</td>
      <td>{$tx.street_address_1} {$tx.street_address_2}
         {$tx.city} {$tx.state} {$tx.postal_code} {$tx.country}</td>
      <td>({$tx.first_name})</td></tr>
    {/foreach}
  </table>
  <table class="edit">
    {foreach $house->emails as $tx}
      <tr><td class="label">
         {if $tx.preferred == 1}* {else}&nbsp;&nbsp;{/if}{$tx.email_type}:</td>
      <td>{$tx.email}</td>
        <td>({$tx.first_name})</td></tr>
    {/foreach}
    {foreach $house->phones as $tx}
      <tr><td class="label">{$tx.phone_type}:</td>
      <td>{$tx.number|Phone:$tx.formatted}</td>
      <td>({$tx.first_name})</td></tr>
    {/foreach}
    {if $current|count > 1}
      <tr><td><select name="selecthouse" id="selecthouse"
         onchange="switchhouse()">
        {foreach $current as $tx}
          <option value="{$tx.household_id}">{$tx.mailname}</option>
        {/foreach}
      </select></td></tr>
    {else}
      <tr><td>Current count: </td><td>{$current|count}</td></tr>
    {/if}
  </table>
  {else}
  <div style="font-weight: bold; font-size: 1.1em;">
  No household - to load donation, create and add members</div>
  {/if}
  </div> {* target household *}
  
  {* import record *}
  <div style="border: thin solid black; padding: 10px;">
  <div style="font-weight: bold; font-size: 1.1em;">
  {$di.fname} {$di.lname} Contribution from Spreadsheet
  ({$di.fy} {$di.recno})</div>
  
  <table class="edit"><tr>
    <input type="hidden" id="value" name="value">
    <td class="label">Salutation:</td><td>{$di.salutation}</td>
    {if $di.salutationmatch == 1}
      <td>OK</td>
    {elseif $hid != 0}
        <td><button onClick="di_submit('Update','salutation')"
         type="button"
         {if $di.salutationmatch==0} style="color: red;"{/if}>
        Update</button></td>
    {/if}
    </tr><tr>
    <td class="label">Mail Name:</td><td>{$di.mailname}</td>
    {if $di.mailname == $house->hd.mailname}
      <td>OK</td>
    {elseif $hid != 0}
        <td><button onClick="di_submit('Update','mailname')"
         type="button">Update</button></td>
    {/if}
    </tr>
    </table>
    <table class="edit" style="border: thin solid black;">
    <tr><td class="label">Address:</td>
    <td>{$di.street} {$di.city} {$di.state} {$di.zip} {$di.country}
    </td></tr>
    {if $hid != 0}
      <tr><td>
      <button onClick="editAddress('{$di.street}','{$di.city}',
        '{$di.state}','{$di.zip}','{$di.country}')"
        type="button">Update</button>
      </td></tr>
    {/if}
    </table>
    <table class="edit" style="border: thin solid black;">
    <tr><td class="label">Donation:</td><td>{$di.ddate|date_format:"%m/%d/%Y"} {math equation="x" x=$di.amount format="%.2f"}</td></tr><tr>
    <td class="label">Fund:</td><td id="FundName">{$di.fund}</td>
    </tr><tr>
    <td class="label">Purpose:</td><td>{$di.dedication}</td>
    </tr><tr>
    <td class="label">Anonymous:</td><td>?</td>
    </tr><tr>
    <td class="label">Notes:</td><td>{$di.donornote}</td>
    </tr>
    <tr><td></td><td>{$di.contactnote}</td></tr>
    {if $hid != 0}
    <tr><td><button onClick="editDonation('{$di.ddate}',
       {$di.amount},'{$di.fund}','{$di.dedication}',0)"
       type="button"
       {if $di.donationmatch==0}style="color: red;"{/if}>
       Add</button></td></tr>
    {*<input type="checkbox" name="donation"> Add</td></tr>*}
    {/if}
    </table>
    <table class="edit">
    <tr><td class="label">Email:</td><td>{$emails.email}</td>
    {if $emails.ok == 1}
       <td>OK</td>
    {elseif $hid != 0}
        <td><button onClick="editEmail('{$emails.email}')"
         type="button">Add</button></td>
    {/if}
    </tr>
    {foreach $phones as $px name=phones}
      {if $smarty.foreach.phones.first}
        <tr><td class="label">Phones:</td>
      {else}
        <tr><td></td>
      {/if}
      <td>{$px.number}</td>
      {if $px['ok'] == 1}
        <td>OK</td>
      {elseif $hid != 0}
        <td><button onClick="editPhone('{$px.number}')"
         type="button">Add</button></td>
      {/if}
    {/foreach}
    <tr><td>&nbsp;</td></tr><tr><td>
      <button type="button" onClick="di_submit('Next')"
        {if $eof==1}disabled{/if}>Next</button>
      <button type="button" onClick="di_submit('ReLoad')">Re-Load</button>
    </td><td>
      <button type="button" onClick="di_submit('MarkDone')"
        {if $eof==1}disabled{/if}>Mark Done</button> 
      <button type="button" 
        onClick="di_submit('ClearList')">Clear Import List</button>
    </td></tr>
  </table>
  </div>
  </div> {* to supply flex (side-by-side) display *}
  </form>
{/block}
