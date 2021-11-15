<?php

header('Content-Type: application/json; charset=utf-8');

$db = new SQLite3(__DIR__ . '/db.sqlite3');

function print_server($data)
{
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}


// print_server($_SERVER);
// print_server($_GET);
// print_server($_POST);
// echo file_get_contents('php://input');
// exit();
// phpinfo()

switch ($_SERVER['DOCUMENT_URI']) {
    case '/api/new-project':

        $data = json_decode(
            file_get_contents('php://input'),
            true
        );

    
        $q = $db->prepare(
            'INSERT INTO project (uid, name, description, slug, client_id, created_at) VALUES (:uid, :name, :description, :slug, :client_id, :created_at)'
        );
        
        $project_uid = uniqid(true); // TODO: replace with UUID

        $q->bindValue(':uid', $project_uid);
        $q->bindValue(':name', $data['project_name']);
        $q->bindValue(':description', $data['project_description']);
        $q->bindValue(':slug', $data['project_name']);
        $q->bindValue(':client_id', 1);
        $q->bindValue(':created_at', date('Y-m-d H:i:s'));
        $q->execute();
            
        echo json_encode(
            [
                'status' => 'ok',
                'project_id' => $project_uid
            ]
        );

        break;

    default:
        header('HTTP/1.1 404 Not Found');
        break;

    
}





?>