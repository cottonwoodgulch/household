{extends file="page.tpl"}

{block name="js"}
  <link rel="stylesheet" href="css/jquery-ui.css" />
  <script src="vendor/components/jqueryui/jquery-ui.js"></script>
{/block}

{block name="dialog"}
  {include file="js/LookupDialog.html"}
  {include file="js/ConfirmDialog.html"}
  {include file="js/ErrorDialog.html"}
  {include file="js/NewHouseDialog.html"}
{/block}

{block name="content"}
  <ul style="border: thin solid black; padding: 1%;">
    <li><input type="button"
      onClick="lookupHouse('Look up new default household','summary.php')"
      value="Look up Household"></li>
    <li>&nbsp;</li>
    <li><button onClick="newHouse()">New Household</button></li>
  </ul>
{/block}
