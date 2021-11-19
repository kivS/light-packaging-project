<?php
$db = new SQLite3(__DIR__ . '/db.sqlite3');

switch ($_SERVER['DOCUMENT_URI']) {
    case '/':
        $page = 'home';
        break;
        

    // match path and remove company, project and document from the path
    case(preg_match('/^\/(?<company>[a-zA-Z0-9-_]+)\/?(?<project>[a-zA-Z0-9-_]*)\/?(?<document>[a-zA-Z0-9-_]*)\/?$/', $_SERVER['DOCUMENT_URI'], $matches) ? true : false):
              
        $company_slug = $matches['company'] ?? null;
        $project_slug = $matches['project'] ?? null;
        $document_slug = $matches['document'] ?? null;
       
        // get company info
        $q = $db->prepare('SELECT * FROM user WHERE slug = :company');
        $q->bindValue(':company', $company_slug);
        $result = $q->execute();
        $company = $result->fetchArray(SQLITE3_ASSOC);

        if($company_slug && $project_slug && $document_slug) {
            $page = 'company_project_and_document';
    
        } elseif($company_slug && $project_slug) {
            $page = 'company_and_project';
    
        }else {
            $page = 'company';
        }
        break;
    

    default:
        $page = 'home';
        break;
}



?>

<?php if($page == 'home'){ ?>
<div>
    <h1>home page</h1>
</div>
<?php }; ?>

<?= $page; ?>

<!-- 
<div>company page</div>

<div>company document</div> -->