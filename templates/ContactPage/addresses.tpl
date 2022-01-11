
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
