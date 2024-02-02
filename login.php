<h1>IdP Login Page</h1>

<form action="post-saml.php">
    <div>
        <label>Username:</label>
        <input name="username" type="text">
    </div>
    <div>
        <label>Pass:</label>
        <input type="password" name="password">
    </div>
    <input type="submit">
    <input type="hidden" name="SAMLRequest"
           value="<?php include 'metadata.php'; ?>">
    <input type="hidden" name="RelayState"
           value="aHR0cHM6Ly9yaW9tYWMuem9ob2Rlc2suY29tL3BvcnRhbC8=">
</form>
