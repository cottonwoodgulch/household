{extends file="page.tpl"}

{block name="js"}
  {include file="js/ConfirmDialog.html"}
  {include file="js/Dialog.html"}
{/block}

{block name="content"}
  {if isset($house)}
    <table class="edit">
      <tr>
        <td style="font-weight: bold;margin: 2px 0 10px">
             Household: {$house->hd.name}
             <br />
             ID: {$house->household_id}
             </p>
        </td>
        <td>
          <table class="edit">
            <tr>
              <td class="label">Salutation:</td><td>{$house->hd.salutation}</td>
            </tr>
            <tr>
              <td class="label">Mail Name:</td><td>{$house->hd.mailname}</td>
            </tr>
            <tr>
              <td class="label">Members:</td>
              <td>
               {foreach from=$house->members item=md}
                 <a href="contact.php?cid={$md.contact_id}"
                    class="filelist_normal">
                 {$md.first_name} {$md.nickname|NickName} {$md.middle_name}
                 {$md.primary_name} {$md.degree}</a> ({$md.contact_id})<br />
                 {if $md.trek_list != ''}
                   {$md.trek_list|TrekList}<br />
                 {/if}
               {foreachelse}
                  No members
               {/foreach}
              </td>
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
              {foreach $house->emails as $tx name=emails}
                <tr>
                  {if $smarty.foreach.emails.first}
                    <td class="label">Emails:</td>
                  {else}
                    <td></td>
                  {/if}
                  {if $tx.preferred}
                    <td>{$tx.email}</td>
                  {/if}
                </tr>
              {/foreach}
              {foreach $house->phones as $tx name=phones}
                <tr>
                  {if $smarty.foreach.phones.first}
                    <td class="label">Phones:</td>
                  {else}
                    <td></td>
                  {/if}
                  <td>{$tx.number|Phone:$tx.formatted} ({$tx.first_name})</td>
                </tr>
              {/foreach}
          </table>
        </td>
       <td><input type="button"
         onClick="lookupHouse('Look up new default household','summary.php','selectHouse')"
         value="Look Up Household">
       </td>
      </tr>
    </table>
    <br />

    <table class="edit">
      <tr><th>Donations</th></tr>
      {foreach $stats as $tx}
      <tr>
        <td class="label">{$tx.label}</td>
        <td class="label">{$tx.amount}</td>
        {if $tx.ddate != ''}
          <td>{$tx.ddate|date_format:"%m/%d/%Y"}</td>
        {/if}
      </tr>
    {/foreach}
    </table>
  {else}
    <input type="button"
         onClick="lookupHouse('Look up new default household','summary.php','selectHouse')"
         value="Look Up Household">
  {/if}
{/block}
