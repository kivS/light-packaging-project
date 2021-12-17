<?php

?>


<!-- pdf viewer -->
<div>
    <?php if(!empty($document['file_path'])){ ?>
    <object data="<?= $document['file_path']; ?>" type="application/pdf" width="80%" height="100%">
        <p>It appears you don't have a PDF plugin for this browser.
            No biggie... you can <a href="<?= $document['file_path']; ?>">click here to
                download the PDF file.</a>
        </p>
    </object>
    <?php }else{ ?>
    <div class="alert alert-danger">
        <strong>Error!</strong> No document found.
    </div>
    <?php } ?>
</div>
