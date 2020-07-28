{extends file="page.tpl"}

{block name="js"}
  <link rel="stylesheet" href="css/jquery-ui.css" />
  <script src="vendor/components/jqueryui/jquery-ui.js"></script>
  <script src="js/newhouse.js"></script>
{/block}

{block name="dialog"}
  {include file="js/LookupDialog.html"}

  <div id="NewHouseDialog" style="display: none">
    <h2>Add new household</h2>
    <form method="post" id="NewHouseForm" action="newhouse.php">
      <table class="edit">
        <tr>
          <td class="label">Household Name</td>
          <td><input id="HouseholdName" name="HouseholdName"></td>
        </tr>
        <tr>
          <td class="label">Salutation</td>
          <td><input id="Salutation" name="Salutation"></td>
        </tr>
        <tr>
          <td class="label">Mail Name</td>
          <td><input id="MailName" name="MailName"></td>
        </tr>
      </table>
    </form> {* NewHouseForm *}
  </div> {* NewHouseDialog *}
{/block}

{block name="content"}
  <ul style="border: thin solid black; padding: 1%;">
    <li><button 
      onClick="lookupHouse('Look up new default household')"
      >Look up Household</button></li>
    <li>&nbsp;</li>
    <li><button onClick="newHouse()">New Household</button></li>
  </ul>
{/block}
