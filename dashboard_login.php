<?php
$db = new SQLite3(__DIR__ . '/db.sqlite3');

if (isset($_GET['login_hash'])) {
    $login_hash = $_GET['login_hash'];
    $query = $db->prepare('
        SELECT uid, login_hash, email, name, slug, created_at, updated_at
        FROM user
        WHERE login_hash = :login_hash
    ');
    $query->bindValue(':login_hash', $login_hash);
    $result = $query->execute();
    $user = $result->fetchArray(SQLITE3_ASSOC);

    if (!$user) {
        $error_msg = 'Invalid login link';
        // redirect with error to signup
        header('Location: /login?error_msg=' . $error_msg);
        exit();
    }

    echo "let\'s login you {$user['name']}!";
}

if (isset($_POST['email'])) {
    // get user from email
    $q = $db->prepare('SELECT * FROM user WHERE email = :email LIMIT 1');
    $q->bindValue(':email', $_POST['email']);
    $result = $q->execute();
    $user = $result->fetchArray(SQLITE3_ASSOC);

    if (!$user) {
        $error_msg = 'User not found';
        // redirect with error to signup
        header('Location: /login?error_msg=' . $error_msg);
        exit();
    }

    $email = "
        <p>
            You can login by clicking  <a href='http://dashboard.project-light-packaging.local/login?login_hash={$user['login_hash']}'>here</a>
        </p>
    ";
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>

<body>
    <h1>Login Page</h1>

    <?php if (isset($_GET['error_msg'])) { ?>
        <div>
            <p>
                <?php echo $_GET['error_msg']; ?>
            </p>
        </div>
    <?php }; ?>

    <?php if (isset($email)) { ?>
        <p>Email: <?= $email; ?></p>
    <?php }; ?>

    <form action="/login" method="post">
        <input type="email" name="email" placeholder="name@email.com" required>
        <input type="submit" value="Login">
    </form>
</body>

</html>