<?php
require('.env.php');
session_start();

if(isset($_SESSION[SESSION_USER_UID_KEY])){
    header("Location: ".DASHBOARD_URL);

}

require_once('functions.php');

$db = new SQLite3(__DIR__ . '/db.sqlite3');

$email = $_POST['email'] ?? null;
$name = $_POST['name'] ?? null;

if($email && $name) {
    $user_uid = hash('ripemd160', random_bytes(32));
    $login_hash = hash('ripemd256', random_bytes(69));

    $query = $db->prepare('
        INSERT INTO user (uid, login_hash, email, name, slug, created_at, updated_at) 
        VALUES (:uid, :login_hash, :email, :name, :slug, :created_at, :updated_at)
    ');
    $query->bindValue(':uid', $user_uid);
    $query->bindValue(':login_hash', $login_hash);
    $query->bindValue(':email', $email);
    $query->bindValue(':name', $name);
    $query->bindValue(':slug', slug($name));
    $query->bindValue(':created_at', date('Y-m-d H:i:s'));
    $query->bindValue(':updated_at', date('Y-m-d H:i:s'));
    $result = $query->execute();


    # create email message with login link
    $dashboard_url = DASHBOARD_URL;
    $email = "
    You can access the dashboard with the following by clicking <a href='{$dashboard_url}/login?login_hash={$login_hash}'>here</a>.
    ";
    

}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SignUp</title>
</head>
<body>

    <form action="/signup" method="post">
        <input type="email" name="email" placeholder="name@email.com" required>
        <input type="text" name="name" placeholder="company name" required>
        <input type="submit" value="Register">
    </form>

    <?php if(isset($email)){ ?>
        <p><?= $email ?></p>
    <?php }; ?>
    

</body>
</html>