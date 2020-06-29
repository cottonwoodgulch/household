/* house/js/house.js - javascript functions */

"use strict";

function markChange(currentElement,saveButtonId) {
  //var currentObject=document.getElementById(elementID);
  currentElement.style.backgroundColor="#DCEAFC";
  document.getElementById(saveButtonId).hidden=false;
}
