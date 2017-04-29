<?php
require '../vendor/autoload.php';
require '../libs/NotORM.php';
//membuat dan mengkonfigurasi slim app
$app = new \Slim\app;

$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = 'root';
$dbname = 'slimrest';
$dbmethod = 'mysql:dbname=';

$dsn = $dbmethod.$dbname;
$pdo = new PDO($dsn, $dbuser, $dbpass);
$db  = new NotORM($pdo);

//mendefinisikan route app di home
$app-> get('/', function(){
    echo "Hello World by slimteknorial";
});
$app ->get('/all', function() use($app, $db){
    foreach($db->students() as $data){
        $produk['all'][] = array(
            'student_id' => $data['student_id'],
            'score' => $data['score'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name']
            );
    }
    echo json_encode($produk);
});
// Mendapatkan salah satu data
$app ->get('/all/{id}', function($request, $response, $args) use($app, $db){
    $produk = $db->students()->where('student_id',$args['id']);
    if($data = $produk->fetch()){
        echo json_encode(array(
            'student_id' => $data['student_id'],
            'score' => $data['score'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name']
            ));
    }
    else{
        echo json_encode(array(
            'status' => false,
            'message' => "ID produk tidak ada"
            ));
    }
});
//tambah produk baru
$app->post('/add', function($request, $response, $args) use($app, $db){
    $produk = $request->getParams();
    $result = $db->students->insert($produk);
    echo json_encode(array(
        "status" => (bool)$result,
        ));

});
//update
$app->put('/all/{id}', function($request, $response, $args) use($app, $db){
    $produk = $db->students()->where("student_id", $args);
    if($produk->fetch()){
        $post=$request->getParams();
        $result= $produk->update($post);
        echo json_encode(array(
            "status" => (bool) $result,
            "message" => "Produk sudah sukses diupdate "));
    }
    else{
        echo json_encode(array(
            "status" => false,
            "message" => "Produk tidak ada"));
    }
});

//menghapus produk
$app->delete('/all/{id}', function($request, $response, $args) use($app, $db){
    $produk = $db->students()->where('student_id', $args);
    if($produk->fetch()){
        $result = $produk->delete();
        echo json_encode(array(
            "status" => true,
            "message" => "Produk berhasil dihapus"));
    }
    else{
        echo json_encode(array(
            "status" => false,
            "message" => "Produk id tersebut tidak ada"));
    }
});
//run App
$app->run();
