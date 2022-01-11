
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
          <td class="label">Contact ID:</td>
          <td>{$cdata->contact_id}</td>
        </tr><tr>
          <td class="label">Contact Type:</td>
          <td>{$cdata->Contact.contact_type}</td>
        </tr><tr>
          <td class="label">First:</td>
          <td id="first">{$cdata->Contact.first_name}</td>
        </tr><tr>
          <td class="label">Middle:</td>
          <td id="middle">{$cdata->Contact.middle_name}</td>
        </tr><tr>
          <td class="label">Last:</td>
          <td id="last">{$cdata->Contact.primary_name}</td>
        </tr><tr>
          <td class="label">Degree:</td>
          <td>{$cdata->Contact.degree}</td>
        </tr><tr>
          <td class="label">Nickname:</td>
          <td id="nickname">{$cdata->Contact.nickname}</td>
        </tr><tr>
          <td class="label">DOB:</td>
          <td>{$cdata->Contact.dob|date_format:"%m/%d/%Y"}</td>
        </tr><tr>
          <td class="label">Gender:</td>
          <td id="gender">{$cdata->Contact.gender}</td>
        </tr><tr>
          <td class="label">Deceased:</td>
          <td id="deceased">{$cdata->Contact.deceased}</td>
        </tr><tr>
          {if $cdata->Contact.redrocks}
            <td>Red Rocks Society</td>
          {/if}
        </tr><tr>
          <td><button type="button"
                onClick="editContact({$cdata->contact_id})"
                class="mini-button">Edit Contact</button></td>
        </tr>
        <input type="hidden" id="contact_type_id"
           value="{$cdata->Contact.contact_type_id}">
        <input type="hidden" id="degree"
           value="{$cdata->Contact.degree_id}">
        <input type="hidden" id="dob" value="{$cdata->Contact.dob}">
        <input type="hidden" id="username"
           value="{$cdata->Contact.username}">
        <input type="hidden" id="redrocks"
           value="{$cdata->Contact.redrocks}">
        
