<?php
$db = new SQLite3(__DIR__ . '/db.sqlite3');

// Get the project from project_id
$q = $db->prepare('SELECT * FROM project WHERE user_id = :user_id AND  uid = :uid LIMIT 1');
$q->bindValue(':user_id', 1);
$q->bindValue(':uid', $_GET['project_id']);
$result = $q->execute();
$project = $result->fetchArray(SQLITE3_ASSOC);

if(!$project) {
    die('<div class="text-center p-4"> Project not found </div>');
}


// Get the documents of this project
$q = $db->prepare('SELECT * FROM document WHERE project_id = :project_id');
$q->bindValue(':project_id', $project['id'], SQLITE3_TEXT);
$results = $q->execute();

$documents = [];

while ($row = $results->fetchArray()) {
    $documents[] = [
        'id' => $row['id'],
        'name' => $row['name'],
        'project_id' => $row['project_id'],
        'uid' => $row['uid'],
        'created_at' => $row['created_at'],
        'url' => '/editor?document_id=' . $row['uid']
    ];
};


?>
<div x-data="{newDocumentModalShow: false}" class="container mx-auto px-4 py-4 sm:px-6 lg:px-8">

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
            <!-- <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Edit
            </button> -->
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
                    <a href="<?php echo $document['url'] ?>" class="focus:outline-none">
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
                'project_id': "<?php echo $project['id']; ?>"
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
                window.location.href = `/editor?document_id=${data.document_id}`
            }
        })


    }
</script>