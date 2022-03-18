
      <tr><td colspan="2">
      <table class="edit">
        <tr><td>Relationships:</td></tr>
        {foreach from=$cdata->relationships item=$cx}
          <tr><td class="label">{$cx.relationship} of</td>
          <td>{$cx.first} {$cx.primary_name}</td></tr>
        {/foreach}
      </table> {* relationships *}
    </td></tr>
