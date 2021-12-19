<?php

if (!isset($_GET['project_uid'])) {
    die('No project UID');
}

$q = $db->prepare('SELECT * FROM project WHERE uid = :uid LIMIT 1');
$q->bindValue(':uid', $_GET['project_uid']);
$result = $q->execute();
$project = $result->fetchArray(SQLITE3_ASSOC);

$company_public_project_url = SITE_URL . "/{$user['slug']}/{$project['slug']}";

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print QR Code</title>
    <link rel="stylesheet" href="/assets/dashboard.css">
    <!-- <script src="/assets/qrcode.min.js"></script> -->
</head>

<body class="flex w-full h-[100vh] justify-center items-center ">
    <?php if (!$project) { ?>
        <div class="text-center p-4"> Project not found </div>
    <?php }; ?>

    <div class="p-4" id="qrcode"></div>
    <script type="text/javascript">
        // load script file async for /assets/qrcode.min.js with callback
        let script = document.createElement('script');
        script.src = '/assets/qrcode.min.js';
        script.async = true;
        script.onload = function() {
            let qrcode = new QRCode("qrcode", {
                text: <?= json_encode($company_public_project_url); ?>,
                width: 300,
                height: 300,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
        };
        document.getElementsByTagName('head')[0].appendChild(script);

        window.print();
    </script>

</body>

</html>