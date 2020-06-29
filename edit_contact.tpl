{extends file="page.tpl"}

{block name="js"}
  <script src="js/ContactData.js"></script>
  <script src="js/edit_contact.js"></script>
{/block}

{block name="dialog"}
  <div id="AddressDialog" title="New Address" style="display: none;">
  <form method="post" action="edit_contact.php"
        id="AddressDialogForm">
  <table class="edit" 
        style="margin: 1em; border: 10px solid #C49F75;">
    <tr>
      <td class="label">
        <label for="add_address_type">Address Type</label></td>
      <td><select id="add-address-type" name="add_address_type_id"
        form="AddressDialogForm" class="content">
        {foreach $address_types as $tx}
          <option value="{$tx.address_type_id}">              
             {$tx.address_type}</option>
        {/foreach}
        </select>  
      </td>
    </tr><tr>
      <td class="label">
        <label for="add_street_address_1">Address</label></td>
      <td><input name="add_street_address_1"/></td>
    </tr><tr>
      <td></td>
      <td><input name="add_street_address_2"/></td>
    </tr><tr>
      <td class="label"><label for="add_city">City</label></td>
      <td><input name="add_city" /></td>
    </tr><tr>
      <td class="label"><label for="add_state">State</label></td>
      <td><input name="add_state" /></td>
    </tr><tr>
      <td class="label">
        <label for="add_postal_code">Postal Code</label></td>
      <td><input name="add_postal_code" /></td>
    </tr><tr>
      <td class="label"><label for="add_country">Country
        </label></td>
      <td><input name="add_country" /></td>
    </tr>
  </table>
  <input type="hidden" name="buttonAction" value="AddAddress"/>
  <input type="hidden" name="contact_id" 
         value="{$user->contact_id}"/>
  </form>
  Address will be saved to a holding file pending release
     to the live database.
  </div>
  <div id="PhoneDialog" title="New Phone" style="display: none;">
  <form method="post" action="edit_contact.php"
        id="PhoneDialogForm">
  <table class="edit" 
        style="margin: 1em; border: 10px solid #C49F75;">
    <tr>
      <td class="label">
        <label for="add_phone_type">Phone Type</label></td>
      <td><select id="add-phone-type" name="add_phone_type_id"
        form="PhoneDialogForm" class="content">
        {foreach $phone_types as $tx}
          <option value="{$tx.phone_type_id}">              
             {$tx.phone_type}</option>
        {/foreach}
        </select>  
      </td>
    </tr><tr>
      <td class="label">
        <label for="add_number">Number</label></td>
      <td><input name="add_number"></td>
    </tr>
  </table>
  <input type="hidden" name="add_formatted" value="0" />
  <input type="hidden" name="buttonAction" value="AddPhone"/>
  <input type="hidden" name="contact_id" 
         value="{$user->contact_id}"/>
  </form>
  Phone will be saved to a holding file pending release
     to the live database.
  </div>
  
  <div id="EmailDialog" title="New Email" style="display: none;">
  <form method="post" action="edit_contact.php"
        id="EmailDialogForm">
  <table class="edit" 
        style="margin: 1em; border: 10px solid #C49F75;">
    <tr>
      <td class="label">
        <label for="add_email_type">Email Type</label></td>
      <td><select id="add-email-type" name="add_email_type_id"
        form="EmailDialogForm">
        {foreach $email_types as $tx}
          <option value="{$tx.email_type_id}">              
             {$tx.email_type}</option>
        {/foreach}
        </select>  
      </td>
    </tr><tr>
      <td class="label">
        <label for="add_email">E-mail Address</label></td>
      <td><input name="add_email"></td>
    </tr>
  </table>
  <input type="hidden" name="buttonAction" value="AddEmail"/>
  <input type="hidden" name="contact_id" 
         value="{$user->contact_id}"/>
  </form>
  Email will be saved to a holding file pending release
     to the live database.
  </div>
{/block}

{block name="content"}
  <form id="edit_contact_form" action="edit_contact.php" method="post">
    <table class="edit">
      <tr>
        <td class="label">
          <label for="title_id">Title</label>
        </td>
        <td>
          <select class="{$user->ud.title_id.c}"
            name="title_id" id="title_id">
            {foreach $titles as $tx}
            {if $user->ud.title_id.v == $tx['title_id']}
              <option value="{$tx.title_id}" selected="selected">
                 {$tx.title}</option>
            {else}
              <option value="{$tx.title_id}">{$tx.title}</option>
            {/if}
            {/foreach}
          </select>
        </td>
      </tr>
      <tr>
        <td class="label">
          <label for="first_name">First Name</label>
        </td>
        <td>
          <input name="first_name"
             class="{$user->ud.first_name.c}"
             value="{$user->ud.first_name.v}"/>
        </td>
      </tr>
      <tr>
        <td class="label">
          <label for="nickname">Nickname</label>
        </td>
        <td>
          <input name="nickname"
             value="{$user->ud.nickname.v}"
             class="{$user->ud.nickname.c}"/>
        </td>
      </tr>
      <tr>
        <td class="label">
          <label for="middle_name">Middle/Maiden Name</label>
        </td>
        <td>
          <input name="middle_name"
             class="{$user->ud.middle_name.c}"
             value="{$user->ud.middle_name.v}"/>
        </td>
      </tr>
      <tr>
        <td class="label">
          <label for="primary_name">Last Name</label>
        </td>
        <td>
          <input name="primary_name"
             class="{$user->ud.primary_name.c}"
             value="{$user->ud.primary_name.v}" />
        </td>
      </tr>
      <tr>
        <td class="label">
          <label for="degree">Degree</label>
        </td>
        <td>
          <select class="{$user->ud.degree_id.c}"
             name="degree_id">
            {foreach $degrees as $tx}
            {if $user->ud.degree_id.v == $tx.degree_id}
              <option value="{$tx.degree_id}" selected="selected">
                 {$tx.degree}</option>
            {else}
              <option value="{$tx.degree_id}">{$tx.degree}</option>
            {/if}
            {/foreach}
          </select>  
        </td>
      </tr>
    <tr><td>&nbsp;</td></tr>
    <tr><td class="label">
          <label for="birth_date">Date of Birth</label>
        </td>
        <td>
          <input name="birth_date"
            class="dob {$user->ud.birth_date.c}"
            value="{$user->ud.birth_date.v|date_format: "%m/%d/%Y"}"/>
        </td>
      </tr>
      <tr>
        <td class="label">
          <label for="gender">Gender</label>
        </td>
        <td>
          <select class="{$user->ud.gender.c}"
             name="gender">
            {foreach array('Female','Male','') as $tx}
              {if $user->ud.gender.v == $tx}
                <option value="{$tx}" selected="selected">{$tx}</option>
              {else}
                <option value="{$tx}">{$tx}</option>
              {/if}
            {/foreach}
          </select>
        </td>
      </tr>
      <tr><td>&nbsp;</td></tr>
      {foreach $contact->ad as $tx}
        <tr><td class="label">
          <select class="{$tx.address_type_id.c}"
             name="{$tx.address_id.v}_address_type_id">
            {foreach $address_types as $ty}
              {if $ty.address_type_id == $tx.address_type_id.v}
                <option value="{$ty.address_type_id}"
                   class="{$tx.address_type_id.c}"
                   selected="selected">{$ty.address_type}</option>
              {else}
                <option value="{$ty.address_type_id}">
                   {$ty.address_type}</option>
              {/if}
            {/foreach}
            </select></td>
          <td><input name="{$tx.address_id.v}_street_address_1"
              class="{$tx.street_address_1.c}"
              value="{$tx.street_address_1.v}" /></td>
          <td>{if $tx.address_id.c == 'del'}
                <button type="submit" name="buttonAction"
                   value="UnDeleteAddress_{$tx.address_id.v}">
                   Undelete</button>
              {else}
                <button type="submit" name="buttonAction"
                   value="DeleteAddress_{$tx.address_id.v}">
                 Delete this Address</button>
              {/if}
          </td>
          {if $tx@first}
            <td><button type="button" onClick="getAddress();">
               Add Address</button></td>
          {/if}
        </tr>
        <tr><td></td><td><input
          name="{$tx.address_id.v}_street_address_2"
          class="{$tx.street_address_2.c}"
          value="{$tx.street_address_2.v}" /></td></tr>
        <tr><td></td><td><input
          name="{$tx.address_id.v}_city"
          class="{$tx.city.c}"
          value="{$tx.city.v}" /></td></tr>
        <tr><td></td><td><input
          name="{$tx.address_id.v}_state"
          class="{$tx.state.c}"
          value="{$tx.state.v}" /></td></tr>
        <tr><td></td><td><input
          name="{$tx.address_id.v}_postal_code"
          class="{$tx.postal_code.c}"
          value="{$tx.postal_code.v}" /></td></tr>
        <tr><td></td><td><input
          name="{$tx.address_id.v}_country"
          class="{$tx.country.c}"
          value="{$tx.country.v}" /></td></tr>
      {foreachelse}
        <tr><td></td><td></td>
          <td><button type="button" onClick="getAddress();">
               Add Address</button></td>
        </tr>
      {/foreach}
      <tr><td>&nbsp;</td></tr>
      {foreach $contact->ph as $tx}
        <tr><td class="label">
           <select class="{$tx.phone_type_id.c}"
              name="{$tx.phone_id.v}_phone_type_id">
           {foreach $phone_types as $ty}
             {if $ty.phone_type_id == $tx.phone_type_id.v}
               <option value="{$ty.phone_type_id}"
                  selected="selected">{$ty.phone_type}</option>
             {else}
               <option value="{$ty.phone_type_id}">
                  {$ty.phone_type}</option>
             {/if}
           {/foreach}
           </select></td>
          <td><input
            name="{$tx.phone_id.v}_number"
            class="{$tx.number.c}"
            value="{$tx.number.v|formatPhone:$tx.formatted.v}" /></td>
          <td>{if $tx.number.c == 'del'}
                <button type="submit" name="buttonAction"
                   value="UnDeletePhone_{$tx.phone_id.v}">
                   Undelete</button>
              {else}
                <button type="submit" name="buttonAction"
                   value="DeletePhone_{$tx.phone_id.v}">
                 Delete this Phone</button>
              {/if}
          </td>
          {if $tx@first}
            <td><button type="button" onClick="getPhone();">
               Add Phone</button></td>
          {/if}
        </tr>
      {foreachelse}
        <tr><td></td><td></td>
        <td><button type="button" onClick="getPhone();">
               Add Phone</button></td></tr>
      {/foreach}
      <tr><td>&nbsp;</td></tr>
      {foreach $contact->em as $tx}
        <tr><td class="label">
              <select class="{$tx.email_type_id.c}"
                name="{$tx.email_id.v}_email_type_id">
           {foreach $email_types as $ty}
             {if $ty.email_type_id == $tx.email_type_id.v}
               <option value="{$ty.email_type_id}"
                  selected="selected">{$ty.email_type}</option>
             {else}
               <option value="{$ty.email_type_id}">
                  {$ty.email_type}</option>
             {/if}
           {/foreach}
           </select></td>
          <td><input
            name="{$tx.email_id.v}_email"
            class="{$tx.email.c}"
            value="{$tx.email.v}" /></td>
          <td>{if $tx.email_id.c == 'del'}
                <button type="submit" name="buttonAction"
                   value="UnDeleteEmail_{$tx.email_id.v}">
                   Undelete</button>
              {else}
                <button type="submit" name="buttonAction"
                   value="DeleteEmail_{$tx.email_id.v}">
                 Delete this E-mail</button>
              {/if}
          </td>
          {if $tx@first}
            <td><button type="button" onClick="getEmail();">
              Add E-mail</button></td>
          {/if}
        </tr>
      {foreachelse}
        <tr><td></td><td></td>
        <td><button type="button" onClick="getEmail();">
             Add E-mail</button></td></tr>
      {/foreach}
      <tr><td>&nbsp;</td></tr>
    </table>
    {* Save contact id. Referrer can be edit_contact or release.
       If release, this form was entered via the release form,
       so show button to return to release *}
    <input type="hidden" name="contact_id"
           value="{$user->contact_id}"/>
    <input type="hidden" name="referrer"
           value="{$referrer}" />
  </form>

{/block}

  {block name="localmenu"}
  <button form="edit_contact_form" type="submit"
          name="buttonAction" class="menu"
          value="Save">Save Changes</button>
  <br />
  <button form="edit_contact_form" type="reset"
          name="buttonAction" class="menu"
          value="Reset">Reset</button>
  {if ($referrer) == 'release'}
    <form id="release_form" action="release.php" method="post">
      Save Changes First!<br />
      <button type="submit"
          name="buttonAction" class="menu"
          value="edit">Back to Release Screen</button>
      <input type="hidden" name="contact_id"
             value="{$user->contact_id}"/>
    </form>
  {/if}
  {/block}
