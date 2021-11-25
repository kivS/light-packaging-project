<?php
require('.env.php');
session_start();


if (!isset($_SESSION[SESSION_USER_UID_KEY])) {
    header('HTTP/1.0 403 Forbidden'); 
    exit;
}

require_once('functions.php');

$db = new SQLite3(__DIR__ . '/db.sqlite3');


switch ($_SERVER['DOCUMENT_URI']) {
    case '/api/new-project':

        $data = json_decode(
            file_get_contents('php://input'),
            true
        );

        $q = $db->prepare(
            'INSERT INTO project (uid, name, description, slug, user_uid, created_at) VALUES (:uid, :name, :description, :slug, :user_uid, :created_at)'
        );
        
        $project_uid = bin2hex(random_bytes(36));

        

        $q->bindValue(':uid', $project_uid);
        $q->bindValue(':name', $data['project_name']);
        $q->bindValue(':description', $data['project_description']);
        $q->bindValue(':slug', slug($data['project_name']));
        $q->bindValue(':user_uid', $data['user_uid']);
        $q->bindValue(':created_at', date('Y-m-d H:i:s'));
        $q->execute();
            
        echo json_encode(
            [
                'status' => 'ok',
                'project_uid' => $project_uid
            ]
        );

        break;

    case '/api/new-document':
        $data = json_decode(
            file_get_contents('php://input'),
            true
        );
        
        $document_uid = bin2hex(random_bytes(36));

        $q = $db->prepare('INSERT INTO document(uid, project_uid, name, slug, created_at) VALUES (:uid, :project_uid, :name, :slug, :created_at);');
        $q->bindValue(':uid', $document_uid);
        $q->bindValue(':project_uid', $data['project_uid']);
        $q->bindValue(':name', $data['name']);
        $q->bindValue(':slug', slug($data['name']));
        $q->bindValue(':created_at', date('Y-m-d H:i:s'));
        $q->execute();

        echo json_encode(
            [
                'status' => 'ok',
                'document_uid' => $document_uid
            ]
        );

        break;

    case '/api/save-document-text':
        $data = json_decode(
            file_get_contents('php://input'),
            true
        );

        $q = $db->prepare('UPDATE document SET text = :text WHERE uid = :uid');
        $q->bindValue(':uid', $data['document_uid']);
        $q->bindValue(':text', $data['text']);
        $q->execute(); 

        echo json_encode(
            [
                'status' => 'ok'
            ]
        );

        break;
        
    default:
        header('HTTP/1.1 404 Not Found');
        break;

    
}





?>