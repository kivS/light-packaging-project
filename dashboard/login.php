<?php
require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/../.env.php');

use Mailgun\Mailgun;

if (SENTRY_DSN) {
    \Sentry\init(['dsn' => SENTRY_DSN]);
}
session_start();

if (isset($_SESSION[SESSION_USER_UID_KEY])) {
    header("Location: " . DASHBOARD_URL);
}

$db = new SQLite3(DB_FILE);

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
    $_SESSION[SESSION_USER_UID_KEY] = $user['uid'];
    session_write_close();

    header("Location: " . DASHBOARD_URL);
    exit();
}

// user is asking for a login link
if (isset($_POST['email'])) {
    // get user from email
    $q = $db->prepare('SELECT * FROM user WHERE email = :email LIMIT 1');
    $q->bindValue(':email', $_POST['email']);
    $result = $q->execute();
    $user = $result->fetchArray(SQLITE3_ASSOC);

    if (!$user) {
        $error_msg = 'Account not found';
        // redirect with error to signup
        header('Location: /login?error_msg=' . $error_msg);
        exit();
    }

    $dashboard_url = DASHBOARD_URL;
    $email_html = "
        <p>
            You can login by clicking  <a href='{$dashboard_url}/login?login_hash={$user['login_hash']}'>here</a>
        </p>
    ";
    $email_text = "
       
        You can login by clicking/pasting in the browser the following link: {$dashboard_url}/login?login_hash={$user['login_hash']}
       
    ";

    $_GET['success'] = true;
   
    // send email
    $mgClient = Mailgun::create(MAILGUN_API_KEY, MAILGUN_API_ENDPOINT);
    $params = array(
        'from'    => MAILGUN_FROM,
        'to'      => $user['email'],
        'subject' => 'Login link',
         'html'    => $email_html,
        'text'    => $email_text
    );
    $mgClient->messages()->send(MAILGUN_DOMAIN, $params);
}


?>

<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="/assets/dashboard.css">
</head>

<body class="h-full">

    <div class="min-h-full flex flex-col justify-start py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <!-- <img class="mx-auto h-12 w-auto" src="https://tailwindui.com/img/logos/workflow-mark-indigo-600.svg" alt="Workflow"> -->
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Sign in to your account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Or
                <a href="/signup" class="font-medium text-indigo-600 hover:text-indigo-500">
                    create a new account
                </a>
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <form class="space-y-6" action="/login" method="POST">
                    <?php if (isset($_GET['error_msg'])) { ?>
                        <div class="rounded-md bg-red-50 p-4">
                            <p class="text-sm font-medium text-red-800 text-center">
                                <?php echo $_GET['error_msg']; ?>
                            </p>
                        </div>
                    <?php }; ?>

                    <?php if (isset($_GET['success'])) { ?>
                        <div class="rounded-md bg-green-50 p-4">
                            <p class="text-sm font-medium text-green-800 text-center">
                                Your login link has been sent to your email.
                            </p>
                        </div>
                    <?php }; ?>

                    <div>
                        <label for=" email" class="block text-sm font-medium text-gray-700">
                            Email address
                        </label>
                        <div class="mt-1">
                            <input id="email" name="email" type="email" autocomplete="email" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Sign in
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

</body>

</html>