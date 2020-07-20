{extends file="page.tpl"}

{block name="js"}
  <link rel="stylesheet" href="css/jquery-ui.css" />
  <script src="vendor/components/jqueryui/jquery-ui.js"></script>
  <script src="js/donations.js"></script>
{/block}

{block name="dialog"}
  {include file="js/ConfirmDialog.html"}
  <div id="DonationEditDialog" style="display: none">
  <form method="post" action="donations.php"
        id="DonationEditForm">
    <input type="hidden" name="EditDonationID" id="EditDonationID">
    <input type="hidden" name="OldPrimaryDonorID" id="OldPrimaryDonorID">
    <input type="hidden" name="buttonAction">
    <table class="edit">
      </tr>
      <tr><td class="label">Date</td>
          <td><input type="date" id="EditDate" name="EditDate" value="EditDate">
          </td>
      </tr>
      <tr><td class="label">Amount</td>
          <td><input id="EditAmount" name="EditAmount" value="EditAmount"></td>
      </tr>
      <tr><td class="label">Fund</td>
          <td><select id="EditFund" name="EditFund">
              {foreach from=$fund_list item=fl}
                <option value="{$fl.fund_id}">{$fl.fund}</option>
              {/foreach}
          </td>
      </tr>
      <tr><td class="label">Purpose</td>
          <td><input id="EditPurpose" name="EditPurpose" value="EditPurpose">
          </td>
      </tr>
      <tr><td class="label">Anonymous</td>
          <td><input type="checkbox" name="EditAnonymous" id="EditAnonymous">
            </td>
      </tr>
      <tr><td class="label">Primary Donor</td>
          <td><select id="EditPrimaryDonor" name="EditPrimaryDonor">
            <option class="HouseMember" value="0">Select Primary Donor</option>
            {foreach from=$members item=mx}
              <option class="HouseMember"
              value="{$mx.contact_id}">{$mx.first_name}</option>
            {/foreach}
          </td>
      </tr>
  </table>
  </form> {* DonationEditForm *}
  </div> {* DonationEditDialog *}
{/block}

{block name="content"}
  {if isset($house)}
    <table class="edit">
    <tr style="font-size: 1.1em; font-weight: bold;">
    <td>Donations for {$house->hd.name} household</td><td>&nbsp;</td>
    <td><button onClick="addDonation()">Add Donation</button></td>
    </tr>
    </table>
    <table class="edit">
      {foreach from=$house->donations item=md name=donations}
        {if $smarty.foreach.donations.first}
          <tr>
          <th></th>
          <th>Date</th>
          <th>Amount</th>
          <th>Fund</th>
          <th>Purpose</th>
          <th>Anonymous</th>
          <th>Donor</th>
          </tr>
        {/if}
        <tr>
          <td><button type="button" onClick="editDonation({$md.donation_id})">
               <img src="images/edit.png" title="Edit"></button>
            <td class="label">{$md.date|date_format:"%m/%d/%Y"}</td>
            <td class="label">{$md.famount}</td>
            <td id="Fund{$md.donation_id}">{$md.fund}</td>
            <td id="Purpose{$md.donation_id}">{$md.purpose}</td>
            <td id="Anonymous{$md.donation_id}">{if $md.anonymous}x{/if}</td>
            <td id="PrimaryDonor{$md.donation_id}">{$md.first_name} {$md.degree}</td>
            <td id="Date{$md.donation_id}" class="hiddenkey">{$md.date}</td>
            <td id="Amount{$md.donation_id}" class="hiddenkey">{$md.amount}</td>
            <td id="DonorID{$md.donation_id}" class="hiddenkey">{$md.donor_id}</td>
            {*<td>{$md.donation_id}</td>*}
        </tr> 
      {foreachelse}
        <tr><td>No donations</td></tr>
      {/foreach}

    </table>
  {else}
    <p>No household specified</p>
  {/if}
{/block}
