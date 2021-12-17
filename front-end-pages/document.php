 <div>
     <h1><?= $page ?></h1>
     <div>
         <h2><?= $document['name'] ?></h2>
         <h3><?= $document['slug'] ?></h3>
         <h4><?= $document['created_at'] ?></h4>
         <?php echo json_encode($document); ?>
     </div>
 </div>