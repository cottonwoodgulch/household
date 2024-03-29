<!DOCTYPE html>
<html><head>
  <title>Households</title>
  <link rel="stylesheet" href="css/house.css" />
  <link rel="icon" href="images/skull.ico" />
  <script src="vendor/components/jquery/jquery.min.js"></script>
  <link rel="stylesheet" href="css/jquery-ui.css" />
  <script src="vendor/components/jqueryui/jquery-ui.js"></script>
  <script src="js/house.js"></script>
  {block name="js"}
  {/block}
</head>
<body>

{block name="header"}
  <div id="header-wrapper">
    <div id="header">
      <img src="images/transparent_logo.png" class="Logo" />
    </div>
    <div id="header2">
      <h2>Cottonwood Gulch Donor Management<br /><br /></h2>
  
      {block name="nav"}
      <div id="nav-wrapper">
        <div id="nav1"><ul class="navbar">
          {foreach $sitemenu as $menuitem}
            <li>
            <a class="{if $menuitem['c']}filelist_active{else}filelist_normal{/if}"
                href="{$menuitem['t']}.php">{$menuitem['d']}</a>
            </li>
          {/foreach}
          </ul>
        </div>
        <div id="nav2">
          <ul class="drop-down">
          <li><b>{$HelloName}</b>
              <img src="images/dropdownarrow.png" />
            <ul class="fallback">
              <li><a class="filelist_normal" href="pwreset.php">Change Password</li>
              <li><a class="filelist_normal" href="logout.php" >Logout</a></li>
            </ul>
          </li>
          </ul>
        </div>
      </div>
      {/block}
      
    </div>
  </div>
{/block}

{block name="dialog"}
{/block}

<div id="content-wrapper">
  <div id="content">
    {block name="content"}
    {/block}
  </div>
</div>

{if isset($footer)}
  <div id="footer">
    {block name="footer"}
      <table class="edit">
      {foreach $footer as $fx}
        <tr><td class="footermsg">{$fx.txt}</td>
        <td class="footermsg">{$fx.msg|default:'&nbsp;'}</td></tr>
      {/foreach}
      </table>
      <button type="button" onClick="hideFooter();">Close</button>
    {/block}
  </div>
{/if}

</body></html>
