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
    <table class="edit">
    <tr><td>
    <table class="edit"> {* contact, coordinates *}
      <tr><td>
      <table class="edit"> {* contact *}
        <tr>
          {if $cdata->Contact.household_id == 0}
            <td>Not in household</td>
          {else}
            <td class="label">Household:</td>
            <td>{$cdata->Contact.mailname} 
                   ({$cdata->Contact.household_id})</td>
          {/if}
        </tr>
        </tr><tr>
          <td class="label">Contact ID:</td><td>{$cdata->contact_id}</td>
        </tr><tr>
          <td class="label">Contact Type:</td><td>{$cdata->Contact.contact_type}</td>
        </tr><tr>
          <td class="label">First:</td><td>{$cdata->Contact.first_name}</td>
        </tr><tr>
          <td class="label">Middle:</td><td>{$cdata->Contact.middle_name}</td>
        </tr><tr>
          <td class="label">Last:</td><td>{$cdata->Contact.primary_name}</td>
        </tr><tr>
          <td class="label">Nickname:</td><td>{$cdata->Contact.nickname}</td>
        </tr><tr>
          <td class="label">DOB:</td><td>{$cdata->Contact.dob}</td>
        </tr><tr>
          <td class="label">Gender:</td><td>{$cdata->Contact.gender}</td>
        </tr><tr>
          <td class="label">Deceased:</td><td>{$cdata->Contact.deceased}</td>
        </tr>
      </table> {* contact *}
      </td>

      <td><table class="edit"> {* phones, emails, addresses *}
      {* Phones *}
        <tr><td><button type="button"
        class="mini-button"
        onClick="editPhone(0)" title="Add">
               Phones:</button></td></tr>
        {foreach from=$cdata->phones item=$cx}
          <tr><td class="label">
             <button type="button"
             class="mini-button" title="Edit"
             onClick="editPhone({$cx.phone_id})">
             {$cx.phone_type}:</button></td>
             <td>{$cx.number|Phone:$cx.formatted} 
          {if $cx.owner_id != $cdata->contact_id}
              ({$cx.first_name} {$cx.primary_name} {$cx.degree} ({$cx.owner_id}))
          {/if}
          </td></tr>
          <tr><td>&nbsp;</td>
          <td>Formatted: {if $cx.formatted}yes{else}no{/if}</td>
            <input type="hidden" id="phone_type_id{$cx.phone_id}"
            value="{$cx.phone_type_id}">
            <input type="hidden" id="Number{$cx.phone_id}"
            value="{$cx.number}">
            <input type="hidden" id="Formatted{$cx.phone_id}"
               value="{$cx.formatted}">
          </tr>
        {/foreach}

        {* Emails *}
        <tr><td><button type="button"
        class="mini-button"
        onClick="editEmail(0)" title="Add">
               E-mails:</button></td></tr>
        {foreach from=$cdata->emails item=$cx}
          <tr><td class="label"><button type="button"
             class="mini-button" title="Edit"
             onClick="editEmail({$cx.email_id})">
             {$cx.email_type}:</button></td>
             <td>{$cx.email} 
          {if $cx.owner_id != 0 && $cx.owner_id != $cdata->contact_id}
              ({$cx.first_name} {$cx.primary_name} {$cx.degree} ({$cx.owner_id}))
          {/if}
          </td>
            <input type="hidden" id="email_type_id{$cx.email_id}"
            value="{$cx.email_type_id}">
            {* owner id can't be changed here *}
            <input type="hidden" id="owner{$cx.email_id}"
            value="{$cx.owner_id}">
            <input type="hidden" id="email{$cx.email_id}" value="{$cx.email}">
          </tr>
        {/foreach}

        {* Addresses *}
        <tr><td><button type="button"
               class="mini-button"
               onClick="editAddress(0)" title="Add"
               style="border: 0">Addresses:</button></td></tr>
        {foreach from=$cdata->addresses item=$cx}
          <tr><td class="label"><button type="button"
             class="mini-button" title="Edit"
             onClick="editAddress({$cx.address_id})">
             {$cx.address_type}:</button>
             </td>
             <td>{$cx.addr1} {if $cx.owner_id != 0 &&
                 $cx.owner_id != $cdata->contact_id}
                   ({$cx.first_name} {$cx.primary_name} {$cx.degree} ({$cx.owner_id}))
                 {/if}
          </td></tr>
          {if $cx.addr2 != ''}
            <tr><td>&nbsp;</td><td>{$cx.addr2}</td></tr>
          {/if}
          <tr><td>&nbsp;</td>
          <td>{$cx.city}, {$cx.state} {$cx.zip}</td></tr>
          <tr><td>&nbsp;</td>
          <td id="country{$cx.address_id}">{$cx.country}</td>
            <input type="hidden" id="address_type_id{$cx.address_id}"
            value="{$cx.address_type_id}">
            {* owner id can't be changed here *}
            <input type="hidden" id="owner{$cx.address_id}"
            value="{$cx.owner_id}">
            <input type="hidden" id="addr1{$cx.address_id}" value="{$cx.addr1}">
            <input type="hidden" id="addr2{$cx.address_id}" value="{$cx.addr2}">
            <input type="hidden" id="city{$cx.address_id}" value="{$cx.city}">
            <input type="hidden" id="state{$cx.address_id}" value="{$cx.state}">
            <input type="hidden" id="zip{$cx.address_id}" value="{$cx.zip}">
          </tr>
        {/foreach}
      </table> {* phones, emails, addresses *}
      </td></tr>
    </table>  {* contact, coordinates *}
    </td>

    <td><table class="edit">
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
    </table></td>

    {* Rosters *}
    <td><table class="edit" id="rosters">
    </table></td>
    </tr>
  </table>

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
