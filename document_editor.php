<?php
$db = new SQLite3(__DIR__ . '/db.sqlite3');

// get the document from document_uid passed by GET
$q = $db->prepare('
    SELECT document.*, project.name AS project_name, project.uid AS project_uid 
    FROM document JOIN project ON project.uid = document.project_uid 
    WHERE document.uid = :document_uid
');
$q->bindValue(':document_uid', $_GET['document_uid']);
$result = $q->execute();
$document = $result->fetchArray(SQLITE3_ASSOC);


if(!$document) {
    die('<div class="text-center p-4"> Document not found </div>');
}


?>
<div class="container mx-auto px-4 py-4 sm:px-6 lg:px-8">

    <div>
        <nav class="sm:hidden" aria-label="Back">
            <a href="#" class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700">
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

            <!-- <a href="http://project-packing.local/company-x/iphone-22" class="font-medium text-blue-600 hover:text-blue-500">
                View Project
            </a> -->

            <form action="" @submit.prevent="saveText">
                <textarea name="text" id="text" cols="30" rows="10" placeholder="your text here..."><?= $document['text']; ?></textarea>
                <button type="submit" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">save</button>
            </form>
        </div>

    </div>

    <script>
        async function saveText(e) {

            let text = e.target.querySelector('#text').value

            let r = await fetch('/api/save-document-text', {
                method: 'post',
                body: JSON.stringify({
                    'text': text,
                    'document_uid': <?= json_encode($document['uid']) ?>
                }),

                headers: {
                    'Content-Type': 'application/json',
                    // 'Authorization': auth
                },

            }).catch(err => {
                console.error(err)
            })

            r.json().then(data => {
                console.log(data)
                if (data.status == 'ok') {
                    // window.location.href = `/editor?document_id=${data.document_id}`
                }
            })


        }
    </script>
</div>