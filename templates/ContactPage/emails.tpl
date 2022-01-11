
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
