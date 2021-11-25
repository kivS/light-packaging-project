<?php
$db = new SQLite3(__DIR__ . '/db.sqlite3');


// if(isset($_GET['document_translation_uid'])){
//     // get the document translation from document_translation_uid passed by GET
//     $q = $db->prepare('
//         SELECT document_translation.*, project.name AS project_name, project.uid AS project_uid 
//         FROM document_translation JOIN project ON project.uid = document_translation.project_uid 
//         WHERE document_translation.uid = :document_translation_uid
//     ');
//     $q->bindValue(':document_translation_uid', $_GET['document_translation_uid']);
//     $result = $q->execute();
//     $document = $result->fetchArray(SQLITE3_ASSOC);
// }

if(isset($_GET['document_uid'])){
    // get the document from document_uid passed by GET
    $q = $db->prepare('
        SELECT document.*, project.name AS project_name, project.uid AS project_uid 
        FROM document JOIN project ON project.uid = document.project_uid 
        WHERE document.uid = :document_uid
    ');
    $q->bindValue(':document_uid', $_GET['document_uid']);
    $result = $q->execute();
    $document = $result->fetchArray(SQLITE3_ASSOC);
}

if (!$document) {
    die('<div class="text-center p-4"> Document not found </div>');
}

// get translations for this document
$q = $db->prepare('SELECT * FROM document_translation WHERE document_uid = :document_uid;');
$q->bindValue(':document_uid', $document['uid']);
$result = $q->execute();
$translations = [];
while ($translation = $result->fetchArray(SQLITE3_ASSOC)) {
    $translation['language_name'] = Locale::getDisplayLanguage($translation['language_code']);
    $translations[] = $translation;
}

?>
<div x-data="{addTranslationModalShow: false}" class="container mx-auto px-4 py-4 sm:px-6 lg:px-8">

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
            
            <?php if(isset($translations) && !empty($translations)){ ?>
            <div>
                <form action="" method="GET">
                    <select name="translation_language" id="translation_language">
                        <option value="">Select a translation</option>
                        <?php foreach ($translations as $translation) : ?>
                            <option value="<?= $translation['language_code'] ?>"><?= $translation['language_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
            <?php }; ?>

            <a @click="addTranslationModalShow = true" href="#">Add translation</a>
            <!-- add translation modal -->
            <div x-cloak x-show="addTranslationModalShow" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <!--
      Background overlay, show/hide based on modal state.

      Entering: "ease-out duration-300"
        From: "opacity-0"
        To: "opacity-100"
      Leaving: "ease-in duration-200"
        From: "opacity-100"
        To: "opacity-0"
    -->
                    <div x-show="addTranslationModalShow" @keydown.window.escape="addTranslationModalShow = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                    <!-- This element is to trick the browser into centering the modal contents. -->
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <!--
      Modal panel, show/hide based on modal state.

      Entering: "ease-out duration-300"
        From: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        To: "opacity-100 translate-y-0 sm:scale-100"
      Leaving: "ease-in duration-200"
        From: "opacity-100 translate-y-0 sm:scale-100"
        To: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    -->
                    <div x-show="addTranslationModalShow" class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                        <form action="" @submit.prevent="createDocumentTranslation" method="POST">
                            <div>
                                <?php /* ?><div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                        <!-- Heroicon name: outline/check -->
                        <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    */ ?>
                                <div class="mt-3 text-center sm:mt-5">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                        Translation language
                                    </h3>
                                    <div class="mt-2">
                                        <div>
                                            <label for="language_code" class="sr-only">Language</label>
                                            <input type="text" list="languages_list" name="language_code" id="language_code" required placeholder="eg: Italian" autocomplete="off" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                            <datalist id="languages_list">
                                                <option value="en">English</option>
                                                <option value="es">Spanish</option>
                                                <option value="it">Italian</option>
                                                <option value="pt">Portuguese</option>
                                            </datalist>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:col-start-2 sm:text-sm">
                                    Continue
                                </button>
                                <button @click.prevent="addTranslationModalShow = false" type=" button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

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
                    // 
                }
            })


        }

        async function createDocumentTranslation(e) {

            let language_code = e.target.querySelector('#language_code').value

            let r = await fetch('/api/create-document-translation', {
                method: 'post',
                body: JSON.stringify({
                    'document_uid': <?= json_encode($document['uid']) ?>,
                    'project_uid': <?= json_encode($document['project_uid']) ?>,
                    'language_code': language_code,
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
                    // reload page
                    location.reload()
                    // window.location.href = `/editor?document_translation_uid=${data.language_code}`
                }
            })


        }
    </script>
</div>