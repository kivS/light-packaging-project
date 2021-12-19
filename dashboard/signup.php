<?php
require(__DIR__ . '/../.env.php');
session_start();

if (isset($_SESSION[SESSION_USER_UID_KEY])) {
    header("Location: " . DASHBOARD_URL);
    exit;
}

require_once(__DIR__ . '/../functions.php');

$db = new SQLite3(DB_FILE);

$email = $_POST['email'] ?? null;
$name = $_POST['name'] ?? null;

if ($email && $name) {

    // check if email is already registered
    $q = $db->prepare('SELECT * FROM user WHERE email = :email LIMIT 1');
    $q->bindValue(':email', $email);
    $result = $q->execute();
    $user = $result->fetchArray();
    if ($user) {
        $error_msg = 'Email already registered';
        header("Location: " . DASHBOARD_URL . "/signup?error_msg=" . $error_msg);
        exit;
    }

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

    $_GET['success'] = true;
}


?>

<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create your account</title>
    <link rel="stylesheet" href="/assets/dashboard.css">
</head>

<body class="h-full">

    <div class="min-h-full flex flex-col justify-start py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <!-- <img class="mx-auto h-12 w-auto" src="https://tailwindui.com/img/logos/workflow-mark-indigo-600.svg" alt="Workflow"> -->
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Create your account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Or
                <a href="/login" class="font-medium text-indigo-600 hover:text-indigo-500">
                    sign in
                </a>
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <form class="space-y-6" action="/signup" method="POST">

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
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            Email address
                        </label>
                        <div class="mt-1">
                            <input id="email" name="email" type="email" autocomplete="email" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Company name
                        </label>
                        <div class="mt-1">
                            <input id="name" name="name" type="text" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                    </div>



                    <div>
                        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Sign up
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

</body>

</html>