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
