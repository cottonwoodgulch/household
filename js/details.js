/* js functions for dialogs on details.php and details.tpl */

import { lookupHouse } from "LookupDialog.html";

function moveMember(title, move_action) {
  /* lookup household from db to move this person*/
  lookupHouse(title, move_action);
}

function addMember(title, add_action) {
  /* lookup contact from db to add to this household*/
  lookupHouse(title, add_action);
}

function lookupContactDialog() {
  //pass
}