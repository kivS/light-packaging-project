<?php

// get the document from document_uid passed by GET
$q = $db->prepare('
    SELECT document.*, project.name AS project_name, project.uid AS project_uid 
    FROM document JOIN project ON project.uid = document.project_uid 
    WHERE document.uid = :document_uid
');
$q->bindValue(':document_uid', $_GET['document_uid']);
$result = $q->execute();
$document = $result->fetchArray(SQLITE3_ASSOC);


if (!$document) {
    die('<div class="text-center p-4"> Document not found </div>');
}


?>
<div class="container mx-auto px-4 py-4 sm:px-6 lg:px-8">

    <div>
        <nav class="sm:hidden" aria-label="Back">
            <a href="#" onclick="history.go(-1)" class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700">
                <!-- Heroicon name: solid/chevron-left -->
                <svg class="flex-shrink-0 -ml-1 mr-1 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                Back
            </a>
        </nav>
        <nav class="hidden sm:flex" aria-label="Breadcrumb">
            <ol role="list" class="flex items-center space-x-4">
                <li>
                    <div class="flex">
                        <a href="/" class="text-sm font-medium text-gray-500 hover:text-gray-700">Home</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <!-- Heroicon name: solid/chevron-right -->
                        <svg class="flex-shrink-0 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        <a href="/projects" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">Projects</a>
                    </div>
                </li>

                <li>
                    <div class="flex items-center">
                        <!-- Heroicon name: solid/chevron-right -->
                        <svg class="flex-shrink-0 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        <a href="/projects?project_uid=<?= $document['project_uid'] ?>" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700"><?= $document['project_name'] ?></a>
                    </div>
                </li>

            </ol>
        </nav>
    </div>
    <div class="mt-2 md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                <?php echo $document['name']; ?>
            </h2>

            <div class="max-w-lg">
                <div class="mt-8 bg-gray-200 p-4 rounded-md">
                    <form action="" method="post" @submit.prevent="uploadFile" enctype="multipart/form-data">
                        <input type="file" name="document-file" id="document-file" accept=".pdf" />
                        <button type="submit" class="mt-2 inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Upload</button>
                    </form>
                </div>
    
                <?php if (isset($document['file_path']) and !empty($document['file_path'])) { ?>
                    <div class="mt-2 bg-green-100 p-4 rounded-md text-center">
                        <p>File loaded: <?= $document['file_original_name']; ?></p>
                    </div>
                <?php }; ?>
            </div>    


        </div>

    </div>

    <script>
        async function uploadFile(e) {


            let file = document.querySelector('input#document-file').files[0];

            if (!file) {
                return;
            }

            let formData = new FormData();
            formData.append('document-file', file);
            formData.append('document_uid', '<?= $document['uid'] ?>');

            document.querySelector('form').parentElement.classList.toggle('animate-pulse');


            let response = await fetch('/api/upload-document-file', {
                method: 'POST',
                body: formData
            });

            let json_resp = await response.json();

            if (json_resp.status == 'ok') {
                window.location.reload();
            } else {
                alert(json_resp.message);
            }

            document.querySelector('form').parentElement.classList.toggle('animate-pulse');

        }
    </script>
</div>