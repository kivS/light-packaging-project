<?php

?>

<!-- divider with document name -->
<div class="relative">
    <div class="absolute inset-0 flex items-center" aria-hidden="true">
        <div class="w-full border-t border-gray-300"></div>
    </div>
    <div class="relative flex justify-center">
        <span class="px-2 bg-white text-sm text-gray-500 mb-8">
            <?= $document['name']; ?>
        </span>
    </div>
</div>

<!-- pdf viewer -->
<div class="flex justify-center w-full h-[80vh]">
    <?php if (!empty($document['file_path'])) { ?>
        <object data="<?= $document['file_path']; ?>" type="application/pdf" width="80%" height="100%">
            <p>It appears you don't have a PDF plugin for this browser.
                No biggie... you can <a href="<?= $document['file_path']; ?>">click here to
                    download the PDF file.</a>
            </p>
        </object>
    <?php } else { ?>
        <div class="alert alert-danger">
            <strong>Error!</strong> No document found.
        </div>
    <?php } ?>
</div>