<form action="register" method="POST">
    <div>
        <label for="user_id">ID Number</label>
        <input type="text" name="user_id" id="user_id">
        <?= Messages::dump('dup_id');?>

    <div>
        <label for="username">Username</label>
        <input type="text" name="username" id="username">
        <?= Messages::dump('dup_username');?>
    <div>
        <label for="password">Password</label>
        <input type="password" name="password" id="password">
        <?= Messages::dump('pass_verif');?>
    <div>
        <label for="user_privilege">Privilege</label>
        <select name="user_privilege">
            <option value="999">System Admin</option>
            <option value="1">Department Head</option>
            <option value="2">WS In-Charge</option>
            <option value="3">Working Scholar</option>
            <option value="4">Guest</option>
            <option value="85">Departmental Account</option>
        </select>
    <div>
        <label for="user_lname">Last Name</label>
        <input type="text" name="user_lname" id="user_id">
    <div>
        <label for="user_fname">First Name</label>
        <input type="text" name="user_fname" id="user_id">
    <div>
        <button type="submit" name="register" value="true">Register</button>
</form>
<br>
Already have an account? <a href="/uclm_scholarship/login">Login</a>
<div><?=Messages::dump('prompt');?></div>