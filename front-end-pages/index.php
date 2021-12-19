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

            // get document info
            $q = $db->prepare('SELECT * FROM document WHERE slug = :document_slug LIMIT 1');
            $q->bindValue(':document_slug', $document_slug);
            $result = $q->execute();
            $document = $result->fetchArray(SQLITE3_ASSOC);

            $title = "{$document['name']}";

        } elseif ($company_slug && $project_slug) {
            $page = 'project';

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

            $title = "Project - {$project['name']}";

        } else {
            $page = 'company';
           

            // get company projects
            $q = $db->prepare('SELECT * FROM project WHERE user_uid = :user_uid');
            $q->bindValue(':user_uid', $company['uid']);
            $result = $q->execute();
            $projects = [];
            while ($project = $result->fetchArray(SQLITE3_ASSOC)) {
                $project['url'] = "/{$company['slug']}/{$project['slug']}";
                $projects[] = $project;
            }

            $title = "Company - {$company['name']}";
        }
        break;


    default:
        $page = 'home';
        break;
}

?>

<?php if($page == 'home'){ 
    // 404
    header('HTTP/1.1 404 Not Found');
    exit();
}; 
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?></title>
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <link rel="stylesheet" href="/assets/app.css">
</head>

<body class="bg-gray-50">
    <!-- container -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- card -->
        <div class="bg-white overflow-hidden shadow rounded-lg mt-4">
            <!-- header -->
            <div class="px-4 py-5 sm:px-6">
                <!--menu breadcrumb -->
                <?php /*; ?>
                <nav class="flex" aria-label="Breadcrumb">
                    <ol role="list" class="bg-white rounded-md shadow px-6 flex space-x-4">
                        <li class="flex">
                            <div class="flex items-center">
                                <a href="/<?= $company['slug']; ?>" class="text-gray-400 hover:text-gray-500">
                                    <!-- Heroicon name: solid/home -->
                                    <svg class="flex-shrink-0 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                                    </svg>
                                    <span class="sr-only">Home</span>
                                </a>
                            </div>
                        </li>

                        <li class="flex">
                            <div class="flex items-center">
                                <svg class="flex-shrink-0 w-6 h-full text-gray-200" viewBox="0 0 24 44" preserveAspectRatio="none" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M.293 0l22 22-22 22h1.414l22-22-22-22H.293z" />
                                </svg>
                                <a href="#" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">Projects</a>
                            </div>
                        </li>

                        <li class="flex">
                            <div class="flex items-center">
                                <svg class="flex-shrink-0 w-6 h-full text-gray-200" viewBox="0 0 24 44" preserveAspectRatio="none" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M.293 0l22 22-22 22h1.414l22-22-22-22H.293z" />
                                </svg>
                                <a href="#" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700" aria-current="page">Project Nero</a>
                            </div>
                        </li>
                    </ol>
                </nav>
                  <?php */; ?>
                <h1 class="capitalize">
                    <a href="/<?= $company['slug']; ?>"><?= $company['name']; ?></a>
                </h1>
            </div>

            <!-- content -->
            <div class="bg-gray-50 px-4 py-5 sm:p-6">

                <?php
                if ($page == 'document') {
                    require_once('document.php');
                };
                ?>

                <?php if ($page == 'project') { ?>
                    <!-- divider  -->
                    <div class="relative mb-4">
                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center">
                            <span class="px-3 bg-white text-lg font-medium text-gray-900">
                                Documents
                            </span>
                        </div>
                    </div>

                    <ul role="list" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <?php foreach ($documents as  $document) { ?>
                            <li class="col-span-1 bg-white rounded-lg shadow divide-y divide-gray-200">
                                <a href="<?= $document['url']; ?>">

                                    <div class="w-full flex items-center justify-between p-6 space-x-6">
                                        <div class="flex-1 truncate">
                                            <div class="flex items-center space-x-3">
                                                <h3 class="text-gray-900 text-sm font-medium truncate"><?= $document['name']; ?></h3>
                                            </div>
                                            <!-- <p class="mt-1 text-gray-500 text-sm truncate">Regional Paradigm Technician</p> -->
                                        </div>
                                    </div>

                                </a>
                            </li>
                        <?php }; ?>
                    </ul>

                <?php }; ?>

                <?php if ($page == 'company') { ?>
                    <!-- divider  -->
                    <div class="relative mb-4">
                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center">
                            <span class="px-3 bg-white text-lg font-medium text-gray-900">
                                Projects
                            </span>
                        </div>
                    </div>

                    <!-- list of projects -->
                    <ul role="list" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <?php foreach ($projects as  $project) { ?>
                            <li class="col-span-1 bg-white rounded-lg shadow divide-y divide-gray-200">
                                <a href="<?= $project['url'] ?>">

                                    <div class="w-full flex items-center justify-between p-6 space-x-6">
                                        <div class="flex-1 truncate">
                                            <div class="flex items-center space-x-3">
                                                <h3 class="text-gray-900 text-sm font-medium truncate"><?= $project['name'] ?></h3>
                                            </div>
                                            <p class="mt-1 text-gray-500 text-sm truncate"><?= $project['description'] ?></p>
                                        </div>
                                    </div>

                                </a>
                            </li>
                        <?php }; ?>
                    </ul>
                <?php }; ?>

            </div>
        </div>


    </div>

</body>

</html>