<?php

/**
 * Dashboard page for a expecific  page.
 * 
 */

$db = new SQLite3(__DIR__ . '/db.sqlite3');


// Get the project from project_uid
$q = $db->prepare('SELECT * FROM project WHERE uid = :uid LIMIT 1');
$q->bindValue(':uid', $_GET['project_uid']);
$result = $q->execute();
$project = $result->fetchArray(SQLITE3_ASSOC);

if (!$project) {
    die('<div class="text-center p-4"> Project not found </div>');
}

$company_public_project_url = "http://project-light-packaging.local/company-x/{$project['slug']}";


// Get the documents of this project
$q = $db->prepare('SELECT * FROM document WHERE project_uid = :project_uid');
$q->bindValue(':project_uid', $project['uid'], SQLITE3_TEXT);
$results = $q->execute();

$documents = [];

while ($document = $results->fetchArray(SQLITE3_ASSOC)) {
    $document['url'] =  '/editor?document_uid=' . $document['uid'];
    $documents[] = $document;
};

?>
<div x-data="{newDocumentModalShow: false, qrCodeSlideOverOpen: false}" class="container mx-auto px-4 py-4 sm:px-6 lg:px-8">

    <!-- slide-over for project creation -->
    <div x-show="qrCodeSlideOverOpen" x-cloak class="z-10 fixed inset-0 overflow-hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
        <div class="absolute inset-0 overflow-hidden">
            <!-- Background overlay, show/hide based on slide-over state. -->
            <div x-show="qrCodeSlideOverOpen" class="absolute inset-0" aria-hidden="true">
                <div class="fixed inset-y-0 pl-16 max-w-full right-0 flex">
                    <!-- Slide-over panel, show/hide based on slide-over state. -->
                    <div x-show="qrCodeSlideOverOpen" x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="w-screen max-w-md">
                        <form action="" method="POST" @submit.prevent="" class="h-full divide-y divide-gray-200 flex flex-col bg-white shadow-xl">
                            <div class="flex-1 h-0 overflow-y-auto">
                                <div class="py-6 px-4 bg-indigo-700 sm:px-6">
                                    <div class="flex items-center justify-between">
                                        <h2 class="text-lg font-medium text-white" id="slide-over-title">
                                            QR Code
                                        </h2>
                                        <div class="ml-3 h-7 flex items-center">
                                            <button @click="qrCodeSlideOverOpen = false" type="button" class="bg-indigo-700 rounded-md text-indigo-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white">
                                                <span class="sr-only">Close panel</span>
                                                <!-- Heroicon name: outline/x -->
                                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="mt-1">
                                        <p class="text-sm text-indigo-300">
                                            Manage project QR code.
                                        </p>
                                    </div>
                                </div>
                                <div class="flex-1 flex flex-col justify-between">
                                    <div class="px-4 divide-y divide-gray-200 sm:px-6">
                                        <div class="space-y-6 pt-6 pb-5">

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
                                            </script>

                                            <a href="<?= $company_public_project_url; ?>" class="group inline-flex items-center text-gray-500 hover:text-gray-900">
                                                <!-- Heroicon name: solid/question-mark-circle -->
                                                <svg class="h-5 w-5 text-gray-400 group-hover:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                                </svg>
                                                <span class="ml-2">
                                                    <?= $company_public_project_url; ?>
                                                </span>
                                            </a>

                                        </div>
                                        <div class="pt-4 pb-6">
                                            <div class="mt-4 flex text-sm">
                                                <a href="#" class="group inline-flex items-center text-gray-500 hover:text-gray-900">
                                                    <!-- Heroicon name: solid/question-mark-circle -->
                                                    <svg class="h-5 w-5 text-gray-400 group-hover:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                                    </svg>
                                                    <span class="ml-2">
                                                        Learn more about sharing
                                                    </span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-shrink-0 px-4 py-4 flex justify-end">
                                <button @click="qrCodeSlideOverOpen = false" type="button" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Close
                                </button>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- document name modal -->
    <div x-cloak x-show="newDocumentModalShow" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
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
            <div x-show="newDocumentModalShow" @keydown.window.escape="newDocumentModalShow = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

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
            <div x-show="newDocumentModalShow" class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <form action="" @submit.prevent="createDocument" method="POST">
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
                                Document name
                            </h3>
                            <div class="mt-2">
                                <div>
                                    <label for="email" class="sr-only">Document name</label>
                                    <input type="text" name="name" id="name" required class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="eg: Instruction manual">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:col-start-2 sm:text-sm">
                            Continue
                        </button>
                        <button @click.prevent="newDocumentModalShow = false" type=" button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


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

            </ol>
        </nav>
    </div>
    <div class="mt-2 md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                <?php echo $project['name']; ?>
            </h2>
        </div>
        <div class="mt-4 flex-shrink-0 flex md:mt-0 md:ml-4">

            <button @click="qrCodeSlideOverOpen = true" type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                QR Code
            </button>
            <button @click="newDocumentModalShow = true" type="button" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                New Document
            </button>
        </div>
    </div>

    <!-- Projects grid -->
    <div class="pt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
        <?php foreach ($documents as $document) { ?>
            <div class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                <div class="flex-1 min-w-0">
                    <a href="<?= $document['url'] ?>" class="focus:outline-none">
                        <span class="absolute inset-0" aria-hidden="true"></span>
                        <p class="text-sm font-medium text-gray-900">
                            <?php echo $document['name']; ?>
                        </p>
                        <!-- <p class="text-sm text-gray-500 truncate">
                            <?php echo $document['name']; ?>
                        </p> -->
                    </a>
                </div>
            </div>
        <?php  }; ?>
    </div>

</div>
<script>
    async function createDocument(e) {

        let document_name = e.target.querySelector('#name').value

        let r = await fetch('/api/new-document', {
            method: 'post',
            body: JSON.stringify({
                'name': document_name,
                'project_uid': "<?= $project['uid']; ?>"
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
                window.location.href = `/editor?document_uid=${data.document_uid}`
            }
        })


    }
</script>