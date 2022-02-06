{extends file="page.tpl"}

{block name="js"}
  <script src="js/import.js"></script>
{/block}

{block name="dialog"}
{/block}

{block name="content"}
  <form method="post" action="import.php" id="diForm">
  <input type="hidden" id="fy" name="fy" value="{$di.fy}">
  <input type="hidden" id="recno" name="recno" value="{$di.recno}">
  <input type="hidden" id="hid" name="hid" value="{$hid}">
  <input type="hidden" id="buttonAction" name="buttonAction">
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
      <tr><td>{$tx.first_name} {$tx.primary_name} {$tx.degree}</td>
      </tr>
    {/foreach}
  </table>
  <table class="edit">
    {foreach $house->addresses as $tx}
      <tr><td class="label">{$tx.address_type}:</td>
      <td>{$tx.street_address_1} {$tx.street_address_2}
         {$tx.city} {$tx.state} {$tx.postal_code} {$tx.country}</td>
      {if $tx.preferred==1}<td>Preferred</td>{/if}
      <td>({$tx.first_name})</td></tr>
    {/foreach}
  </table>
  <table class="edit">
    {foreach $house->emails as $tx}
      <tr><td class="label">{$tx.email_type}:</td>
      <td>{$tx.email}</td>
      {if $tx.preferred==1}<td>Preferred</td>{/if}
        <td>({$tx.first_name})</td></tr>
    {/foreach}
  </table>
  <table class="edit">
    {foreach $house->phones as $tx}
      <tr><td class="label">{$tx.phone_type}:</td>
      <td>{$tx.number|Phone:$tx.formatted}</td>
      <td>({$tx.first_name})</td></tr>
    {/foreach}
    {if $current|count > 1}
      <tr><td><select name="selecthouse" id="selecthouse"
         onchange="switchhouse()">
        {foreach $current as $tx}
          <option value="{$tx.household_id}">{$tx.mailname}
            {if $tx.houssehold_id == $hid}selected{/if}</option>
        {/foreach}
      </select></td></tr>
    {/if}
  </table>
  {else}
  <div style="font-weight: bold; font-size: 1.1em;">
  No household</div>
  {/if}
  </div>
  <div style="border: thin solid black; padding: 10px;">
  <div style="font-weight: bold; font-size: 1.1em;">
  {$di.fname} {$di.lname} donation</div>
  <table class="edit">
    <tr><td></td><td></td><td style="font-weight: bold;">Update</td>
    </tr><tr>
    <td class="label">Salutation:</td><td>{$di.salutation}</td>
    <td><input type="checkbox" name="salutation"></td>
    </tr><tr>
    <td class="label">Mail Name:</td><td>{$di.fname} {$di.lname}</td>
    <td><input type="checkbox" name="mailname"></td>
    </tr><tr>
    </tr><tr>
    <td class="label">Address:</td>
    <td>{$di.street} {$di.city} {$di.state} {$di.zip} {$di.country}</td>
    <td><input type="checkbox" name="address"></td>
    </tr><tr>
    <td class="label">Donation:</td><td>{$di.ddate|date_format:"%m/%d/%Y"} {math equation="x" x=$di.amount format="%.2f"}</td>
    <td><input type="checkbox" name="donation"></td>
    </tr><tr>
    <td class="label">Fund:</td><td>{$di.fund}</td>
    </tr><tr>
    <td class="label">Purpose:</td><td>{$di.dedication}</td>
    </tr><tr>
    <td class="label">Anonymous:</td><td>?</td>
    </tr><tr>
    <td class="label">Notes:</td><td>{$di.donornote}</td>
    </tr><tr>
    <td></td><td>{$di.contactnote}</td></tr>
    <tr><td></td><td></td><td style="font-weight: bold;">Add</td></tr>
    <td>names</td><td></td>
    <td><input type="checkbox" name="names"></td>
    </tr><tr>
    <td>phones</td><td></td>
    <td><input type="checkbox" name="phones"></td>
    </tr><tr>
    <td class="label">Email:</td><td>{$di.emails}</td>
    <td><input type="checkbox" name="email"></td>
  </table>
  <button type="button" onClick="di_submit('Save')">Save</button>
  <button type="button" onClick="di_submit('Next')"
      {if $eof==1}disabled{/if} autocomplete="off">Next</button>
  <button type="button" onClick="di_submit('ReLoad')">Re-Load</button>
  </div>
  </div> {* to supply flex (side-by-side) display *}
  </form>
{/block}
