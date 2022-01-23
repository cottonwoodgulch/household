{extends file="page.tpl"}

{block name="js"}
  {include file="js/Utility.js"}
{/block}

{block name="dialog"}
  {include file="UtilityDialog.html"}
{/block}

{block name="content"}
  <table class="edit">
    <tr><td><button type="button"
      onClick="AddressLookup()" title="Address Lookup">
             Address Lookup</button>
    </td></tr>
    <tr><td><button type="button"
      onClick="Contributions()"
      title="Paste rows from Contributions SS">
             Contributions</button>
    </td></tr>
  </table>
{/block}
