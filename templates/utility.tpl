{extends file="page.tpl"}

{block name="js"}
  {include file="js/Utility.js"}
{/block}

{block name="dialog"}
  {include file="UtilityDialog.html"}
{/block}

{block name="content"}
  <p>CSV output files are tab-delimited</p>
  <table class="edit">
    <tr><td><button type="button"
      onClick="TextInput('AddressLookup',
         'Address Lookup - paste list of names')">
             Address Lookup</button></td>
    <td>Paste list of names, 1 per line</td></tr>
    <tr><td><button type="button"
      onClick="TextInput('Contributions',
      'Paste rows from Contributions SS')">
             Contributions</button></td>
    <td>Paste rows from Contributions SS, then visit Import page
    </td></tr>
    <tr><td>
    <button type="button" onClick="Merge()">Merge Duplicate Contacts</button>
    </td><td>Combine info from 2 contacts that are the same person.</td></tr>
    <tr><td><button type="button" form="UtilityForm"
      onClick="UtilitySubmit('RedRocks')">
      Red Rocks</button></td>
    <td>Red Rocks Society Members for NFTC (must be in a household)
    </td></tr>
    <tr><td><button type="button" form="UtilityForm"
      onClick="UtilitySubmit('Donors')">Donors</button></td>
    <td>Donors, El Morro, Donor Category for NFTC</td>
    <td class="label">As of: </td>
    <td><input type="date" form="UtilityForm"
       id="DDate" name="DDate" value="{$DefaultDate}"></td>
    </tr>

    <tr><td><button type="button" form="UtilityForm"
      onClick="UtilitySubmit('MailList')">
      Mail List</button></td>
    <td>Postal Addresses for NFTC</td></tr>

    <tr><td>&nbsp;</td></tr>
    <tr><td colspan="2">For Appeal as of: <input type="date" form="UtilityForm"
       id="AppealDate" name="AppealDate" value="{$DefaultDate}"></td>
    </tr>

    <tr><td><button type="button" form="UtilityForm"
      onClick="UtilitySubmit('LYBUNT')">LYBUNT</button></td>
    <td>Donated in previous fiscal year(s) but not since last<br>
       fiscal year end</td>
    </tr>

    <tr><td><button type="button" form="UtilityForm"
      onClick="UtilitySubmit('Current')">Current</button></td>
    <td>Donated since last fiscal year end</td>
    </tr>

    <tr><td><button type="button" form="UtilityForm"
      onClick="UtilitySubmit('Non-Donor')">Non-Donor</button></td>
      <td>No recent donations</td>
    </tr>
  </table>
{/block}
