<?php
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
        $q = $db->prepare('SELECT * FROM user WHERE slug = :company');
        $q->bindValue(':company', $company_slug);
        $result = $q->execute();
        $company = $result->fetchArray(SQLITE3_ASSOC);

        // prepare company project query
        $query_project = $db->prepare('SELECT * FROM project WHERE slug = :project_slug AND user_id = :user_id');   
        $query_project->bindValue(':project_slug', $project_slug);
        $query_project->bindValue(':user_id', $company['id']);
        

        if ($company_slug && $project_slug && $document_slug) {
            $page = 'company_project_and_document';

        } elseif ($company_slug && $project_slug) {
            $page = 'company_and_project';

            // get company project
            $result = $query_project->execute();
            $project = $result->fetchArray(SQLITE3_ASSOC);

            // get project documents
            $q = $db->prepare('SELECT * FROM document WHERE project_id = (SELECT id from project WHERE slug = :project_slug)');
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

<?php if($page == 'company_and_project'){ ?>
<div>
    <h1>company and project page</h1>
    <h2><?= $company['name'] ?></h2>
    <h3>Project name: <?= $project['name'] ?></h3>

    <?php foreach ($documents as $document) { ?>
    <div class="">
        <h4><?= $document['name'] ?></h4>
        <p><?= $document['created_at'] ?></p>
    </div>
    <?php } ?>
    
</div>
<?php }; ?>

<?php if($page == 'company'){ ?>
<div>
    <h1>company page</h1>
    <p>company name: <?= $company['name'] ?></p>

    <?php foreach ($projects as $project) { ?>
    <div>
        <h2><?= $project['name'] ?></h2>
        <p>project description: <?= $project['description'] ?></p>
        <p>project slug: <?= $project['slug'] ?> </p>

    </div>
    <?php }; ?>

</div>
<?php }; ?>


<!-- 
<div>company page</div>

<div>company document</div> -->