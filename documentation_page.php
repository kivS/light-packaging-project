<?php

require_once('functions.php');

$db = new SQLite3(__DIR__ . '/db.sqlite3');


switch ($_SERVER['DOCUMENT_URI']) {
    case '/':
        $page = 'home';
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

        if(!$company) {
            header('HTTP/1.1 404 Not Found');
            echo 'Company not found';
            exit;
        };

        // prepare company project query
        $query_project = $db->prepare('SELECT * FROM project WHERE slug = :project_slug  LIMIT 1');
        $query_project->bindValue(':project_slug', $project_slug);


        if ($company_slug && $project_slug && $document_slug) {

            $page = 'company_project_and_document';

            // get document info
            $q = $db->prepare('SELECT * FROM document WHERE slug = :document_slug AND project_id = (SELECT id from project WHERE slug = :project_slug) LIMIT 1');
            $q->bindValue(':document_slug', $document_slug);
            $q->bindValue(':project_slug', $project_slug);
            $result = $q->execute();
            $document = $result->fetchArray(SQLITE3_ASSOC);

        } elseif ($company_slug && $project_slug) {
            $page = 'company_and_project';

            // get company project
            $result = $query_project->execute();
            $project = $result->fetchArray(SQLITE3_ASSOC);

            // get project documents
            $q = $db->prepare('SELECT * FROM document WHERE project_uid = (SELECT uid from project WHERE slug = :project_slug)');
            $q->bindValue(':project_slug', $project_slug);
            $result = $q->execute();
            $documents = [];
            while ($document = $result->fetchArray(SQLITE3_ASSOC)) {
                $documents[] = $document;
            }
        } else {
            $page = 'company';

            // get company projects
            $q = $db->prepare('SELECT * FROM project WHERE user_id = :user_id');
            $q->bindValue(':user_id', $company['id']);
            $result = $q->execute();
            $projects = [];
            while ($project = $result->fetchArray(SQLITE3_ASSOC)) {
                $projects[] = $project;
            }
        }
        break;


    default:
        $page = 'home';
        break;
}



?>

<?php if ($page == 'home') { ?>
    <div>
        <h1>home page</h1>
    </div>
<?php }; ?>

<?php if ($page == 'company_project_and_document') { ?>
    <div>
        <h1><?= $page ?></h1>
        <div>

            <h2><?= $document['name'] ?></h2>
            <h3><?= $document['slug'] ?></h3>
            <p><?= $document['text'] ?></p>
            <h4><?= $document['created_at'] ?></h4>
          <?php echo json_encode($document); ?>
        </div>
    </div>
<?php }; ?>

<?php if ($page == 'company_and_project') { ?>
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

                <p>encoded slug: <?= slug($project['slug']) ?> </p>

            </div>
        <?php }; ?>

    </div>
<?php }; ?>


