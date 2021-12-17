<?php
require(__DIR__ . '/../.env.php');
require_once(__DIR__ . '/../functions.php');

$db = new SQLite3(DB_FILE);


switch ($_SERVER['DOCUMENT_URI']) {
    case '/':
        $page = 'home';
        $title = 'Home';
        break;


        // match path and remove company, project and document from the path
        // e.g. /company/project<optional>/document<optional>
    case (preg_match('/^\/(?<company>[a-zA-Z0-9-_]+)\/?(?<project>[a-zA-Z0-9-_]*)\/?(?<document>[a-zA-Z0-9-_]*)\/?$/', $_SERVER['DOCUMENT_URI'], $matches) ? true : false):

        $company_slug = $matches['company'] ?? null;
        $project_slug = $matches['project'] ?? null;
        $document_slug = $matches['document'] ?? null;

        // get company info
        $q = $db->prepare('SELECT * FROM user WHERE slug = :company_slug');
        $q->bindValue(':company_slug', $company_slug);
        $result = $q->execute();
        $company = $result->fetchArray(SQLITE3_ASSOC);

        if (!$company) {
            header('HTTP/1.1 404 Not Found');
            echo 'Company not found';
            exit;
        };

        // prepare company project query
        $query_project = $db->prepare('SELECT * FROM project WHERE slug = :project_slug  LIMIT 1');
        $query_project->bindValue(':project_slug', $project_slug);


        if ($company_slug && $project_slug && $document_slug) {

            $page = 'document';
            $title = 'Document page';

            // get document info
            $q = $db->prepare('SELECT * FROM document WHERE slug = :document_slug LIMIT 1');
            $q->bindValue(':document_slug', $document_slug);
            $result = $q->execute();
            $document = $result->fetchArray(SQLITE3_ASSOC);
        } elseif ($company_slug && $project_slug) {
            $page = 'project';
            $title = 'Project page';

            // get company project
            $result = $query_project->execute();
            $project = $result->fetchArray(SQLITE3_ASSOC);

            // get project documents
            $q = $db->prepare('SELECT * FROM document WHERE project_uid = (SELECT uid from project WHERE slug = :project_slug)');
            $q->bindValue(':project_slug', $project_slug);
            $result = $q->execute();
            $documents = [];
            while ($document = $result->fetchArray(SQLITE3_ASSOC)) {
                $document['url'] = "/{$company['slug']}/{$project['slug']}/{$document['slug']}";
                $documents[] = $document;
            }
        } else {
            $page = 'company';
            $title = 'Company page';

            // get company projects
            $q = $db->prepare('SELECT * FROM project WHERE user_uid = :user_uid');
            $q->bindValue(':user_uid', $company['uid']);
            $result = $q->execute();
            $projects = [];
            while ($project = $result->fetchArray(SQLITE3_ASSOC)) {
                $project['url'] = "/{$company['slug']}/{$project['slug']}";
                $projects[] = $project;
            }
        }
        break;


    default:
        $page = 'home';
        break;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?></title>
</head>

<body>
    <?php if ($page == 'home') { ?>
        <div>
            <h1>home page</h1>
        </div>
    <?php }; ?>

    <?php 
        if ($page == 'document') { 
            require_once('document.php');
        }; 
     ?>

    <?php if ($page == 'project') { ?>
        <div>
            <h1>company and project page</h1>
            <h2><?= $company['name'] ?></h2>
            <h3>Project name: <?= $project['name'] ?></h3>

            <h4>Project documents:</h4>
            <?php foreach ($documents as $document) { ?>
                <div class="">
                    <h4><?= $document['name'] ?></h4>
                    <h5><?= $document['slug'] ?></h5>
                    <p><?= $document['created_at'] ?></p>
                    <a href="<?= $document['url'] ?>">View</a>
                </div>
            <?php } ?>

        </div>
    <?php }; ?>

    <?php if ($page == 'company') { ?>
        <div>
            <h1>company page</h1>
            <p>company name: <?= $company['name'] ?></p>
            <p>company slug: <?= $company['slug'] ?></p>

            <h1>Projects:</h1>

            <?php foreach ($projects as $project) { ?>
                <div>
                    <h2><?= $project['name'] ?></h2>
                    <p>project description: <?= $project['description'] ?></p>
                    <p>project slug: <?= $project['slug'] ?> </p>
                    <a href="<?= $project['url'] ?>">View</a>
                    <p>encoded slug: <?= slug($project['slug']) ?> </p>

                </div>
            <?php }; ?>

        </div>
    <?php }; ?>
</body>

</html>