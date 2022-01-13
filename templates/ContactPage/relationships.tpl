
      <tr><td colspan="2">
      <table class="edit">
        <tr><td><button type="button"
                  class="mini-button"
                onClick="editRelationship(0)" title="Add"
                  style="border: 0">Relationships:</button></td></tr>
        {foreach from=$cdata->relationships item=$cx}
          <tr><td class="label"><button type="button"
             class="mini-button" title="Edit"
             onClick="editRelationship({$cx.relationship_id})">
             {$cx.relationship} of</button></td>
          <td><a href="contact.php?cid={$cx.relative_id}"
                 class="filelist_normal">
                 {$cx.first} {$cx.primary_name}</a></td></tr>
          <input type="hidden"
            id="relationship_type_id{$cx.relationship_id}"
            name="relationship_type_id{$cx.relationship_id}"
            value="{$cx.relationship_type_id}">
          <input type="hidden"
            id="relative_id{$cx.relationship_id}"
            name="relative_id{$cx.relationship_id}"
            value="{$cx.relative_id}">
          <input type="hidden"
            id="relative_name{$cx.relationship_id}"
            name="relative_name{$cx.relationship_id}"
            value="{$cx.first} {$cx.primary_name}">
        {/foreach}
      </table> {* relationships *}
    </td></tr>
