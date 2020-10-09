{extends file="page.tpl"}

{* prevent nav <div> from showing *}
{block name="nav"}{/block}

{block name="content"}
      <form method="post" action="login.php">
        <table class="edit">
          <tr>
            <td class="label"><label for="username">User Name</label></td>
            <td><input id="username" name="username" autofocus/></td>
          </tr>
          <tr>
            <td class="label"><label for="password">Password</label></td>
            <td><input name="password" type="password"/></td>
          </tr>
          <tr>
            <td></td>
            <td><input type="submit" value="Log In"/></td>
          </tr>
        </table>
      </form>
{/block}
