<?php

include "inc.php";

// Reading the HTTP Request
$request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();

?>

<h1>IdP Login Page</h1>

<form action="post-saml.php">
    <div>
        <label>Email:</label>
        <input name="user_email" type="email">
    </div>
    <input type="submit">
    <input type="hidden" name="SAMLRequest"
           value="<?php print $request->get("SAMLRequest") ?>">
    <input type="hidden" name="RelayState"
           value="<?php print $request->get("RelayState") ?>">
</form>
<?php print $request->get("SAMLRequest") ?>
<br> <br>
<?php print $request->get("RelayState") ?>