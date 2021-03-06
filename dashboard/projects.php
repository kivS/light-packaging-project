<?php

/**
 * Dashboard page for creating new projects. Projects house documents 
 */


// Get the projects
$q = $db->prepare('SELECT * FROM project WHERE user_uid = :user_uid');
$q->bindValue(':user_uid', $user['uid'], SQLITE3_TEXT);
$results = $q->execute();
$projects = [];
while ($project = $results->fetchArray(SQLITE3_ASSOC)) {
    $project['url'] = "/projects?project_uid={$project['uid']}";
    $projects[] = $project;
};

?>


<div x-data="{ newProjectSlideOverOpen: false }" class="py-6">

    <!-- slide-over for project creation -->
    <div x-show="newProjectSlideOverOpen" x-cloak class="z-10 fixed inset-0 overflow-hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
        <div class="absolute inset-0 overflow-hidden">
            <!-- Background overlay, show/hide based on slide-over state. -->
            <div x-show="newProjectSlideOverOpen" class="absolute inset-0" aria-hidden="true">
                <div class="fixed inset-y-0 pl-16 max-w-full right-0 flex">
                    <!-- Slide-over panel, show/hide based on slide-over state. -->
                    <div x-show="newProjectSlideOverOpen" @click.outside="newProjectSlideOverOpen = false" x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="w-screen max-w-md">
                        <form action="" method="POST" @submit.prevent="createProject" class="h-full divide-y divide-gray-200 flex flex-col bg-white shadow-xl">
                            <div class="flex-1 h-0 overflow-y-auto">
                                <div class="py-6 px-4 bg-indigo-700 sm:px-6">
                                    <div class="flex items-center justify-between">
                                        <h2 class="text-lg font-medium text-white" id="slide-over-title">
                                            New Project
                                        </h2>
                                        <div class="ml-3 h-7 flex items-center">
                                            <button @click="newProjectSlideOverOpen = false" type="button" class="bg-indigo-700 rounded-md text-indigo-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white">
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
                                            Get started by filling in the information below to create your new project.
                                        </p>
                                    </div>
                                </div>
                                <div class="flex-1 flex flex-col justify-between">
                                    <div class="px-4 divide-y divide-gray-200 sm:px-6">
                                        <div class="space-y-6 pt-6 pb-5">
                                            <div>
                                                <label for="project-name" class="block text-sm font-medium text-gray-900">
                                                    Project name
                                                </label>
                                                <div class="mt-1">
                                                    <input type="text" name="project-name" id="project-name" required class="block w-full shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md">
                                                </div>
                                            </div>
                                            <div>
                                                <label for="description" class="block text-sm font-medium text-gray-900">
                                                    Description
                                                </label>
                                                <div class="mt-1">
                                                    <textarea id="project-description" name="project-description" rows="4" required class="block w-full shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border border-gray-300 rounded-md"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                      
                                    </div>
                                </div>
                            </div>
                            <div class="flex-shrink-0 px-4 py-4 flex justify-end">
                                <button @click="newProjectSlideOverOpen = false" type="button" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Cancel
                                </button>
                                <button type="submit" class="ml-4 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Save
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- title  -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold text-gray-900">Projects</h1>


        <button @click="newProjectSlideOverOpen = true" type="button" class="mt-2 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            New Project
        </button>

    </div>


    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">

        <!-- Empty project block  -->
        <?php if (!$projects) { ?>
            <div class="bg-white rounded-lg px-4 mt-6 py-5 border-b border-gray-200 sm:px-6">
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No projects</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Get started by creating a new project.
                    </p>
                    <div class="mt-6">
                        <button @click="newProjectSlideOverOpen = true" type="button" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <!-- Heroicon name: solid/plus -->
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            New Project
                        </button>
                    </div>
                </div>
            </div>
        <?php }; ?>

        <!-- Projects grid -->
        <div class="pt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
            <?php foreach ($projects as $project) { ?>
                <div class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                    <div class="flex-1 min-w-0">
                        <a href="<?php echo $project['url'] ?>" class="focus:outline-none">
                            <span class="absolute inset-0" aria-hidden="true"></span>
                            <p class="text-sm font-medium text-gray-900">
                                <?php echo $project['name']; ?>
                            </p>
                            <p class="text-sm text-gray-500 truncate">
                                <?php echo $project['description']; ?>
                            </p>
                        </a>
                    </div>
                </div>
            <?php  }; ?>
        </div>

    </div>
</div>
<script>
    async function createProject(e) {

        let project_name = e.target.querySelector('#project-name').value
        let project_description = e.target.querySelector('#project-description').value

        let r = await fetch('/api/new-project', {
            method: 'post',
            body: JSON.stringify({
                'project_name': project_name,
                'project_description': project_description,
                'user_uid': <?= json_encode($user['uid']); ?>
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
                window.location.href = '/projects?project_uid=' + data.project_uid
            }
        })


    }
</script>