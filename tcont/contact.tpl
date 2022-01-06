
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
