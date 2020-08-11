{extends file="page.tpl"}

{block name="content"}
  {include file="js/LookupDialog.html"}
  {if isset($house)}
    <table class="edit">
      <tr>
        <td style="font-weight: bold;margin: 2px 0 10px">
             Household: {$house->hd.name}</p>
        </td>
        <td>
          <table class="edit">
            <tr>
              <td class="label">Salutation:</td><td>{$house->hd.salutation}</td>
            </tr>
            <tr>
              <td class="label">Mail Name:</td><td>{$house->hd.mailname}</td>
            </tr>
          </table>
        </td>
        <td>
          <table class="edit">
            <tr>
              <td class="label">Address:</td><td>{$address.street_address_1}</td>
            </tr>
            <tr>
              <td class="label"></td><td>{$address.street_address_2}</td>
            </tr>
            <tr>
              <td class="label"></td>
              <td>{$address.city} {$address.state} {$address.postal_code}</td>
            </tr>
            <tr>
              <td class="label"></td><td>{$address.country}</td>
            </tr>
          </table>
        </td>
        <td style="font-size: .9em;">
         {foreach from=$house->members item=md}
           {$md.first_name} {$md.nickname|NickName} {$md.middle_name} 
           {$md.primary_name} {$md.degree}<br />
         {foreachelse}
            No members
         {/foreach}
       </td>
       <td><input type="button"
         onClick="lookupHouse('Look up new default household','summary.php')"
         value="Look Up Household">
       </td>
      </tr>
    </table>
    <br />
    <table class="edit">
      {foreach from=$house->donations item=md name=donations}
        {if $smarty.foreach.donations.first}
          <tr>
          <th>Date</th>
          <th>Amount</th>
          <th>Fund</th>
          <th>Purpose</th>
          <th>Donor</th>
          </tr>
        {/if}
        <tr><td class="label">{$md.date|date_format:"%m/%d/%Y"}</td>
            <td class="label">{$md.famount}</td>
            <td>{$md.fund}</td>
            <td>{$md.purpose}</td>
            <td>{$md.first_name}</td>
        </tr> 
      {foreachelse}
        <tr><td>No donations</td></tr>
      {/foreach}

    </table>
  {else}
    <input type="button"
         onClick="lookupHouse('Look up new default household','summary.php')"
         value="Look Up Household">
  {/if}
{/block}
