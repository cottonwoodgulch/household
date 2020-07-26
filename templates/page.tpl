<!DOCTYPE html>
<html><head>
  <title>Households</title>
  <link rel="stylesheet" href="css/house.css" />
  <link rel="icon" href="images/skull.ico" />
  <script src="vendor/components/jquery/jquery.min.js"></script>
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
            {if $menuitem['c']}
              <li>{$menuitem['d']}</li>
            {else}
              <li><a class="filelist_normal"
               href="{$menuitem['t']}.php">{$menuitem['d']}</a></li>
            {/if}
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
      {$footer}
    {/block}
  </div>
{/if}

</body></html>
