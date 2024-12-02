<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
require '../src/vendor/autoload.php';
$app = new \Slim\App;

$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "library_zafra";

// Generate Token
function generateToken($userid) {
    $key = 'server_hack';
    $iat = time();
    $payload = [
        'iss' => 'http://library_zafra.org',
        'aud' => 'http://library_zafra.com',
        'iat' => $iat,
        "data" => array("userid" => $userid)
    ];

    return JWT::encode($payload, $key, 'HS256');
}

$app->post('/user/register', function (Request $request, Response $response) use ($servername, $dbusername, $dbpassword, $dbname) {
    $data = json_decode($request->getBody());
    $usr = $data->username;
    $pass = $data->password;

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbusername, $dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "INSERT INTO users (username, password) VALUES (:usr, :pass)";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['usr' => $usr, 'pass' => password_hash($pass, PASSWORD_DEFAULT)]);
        
        $response->getBody()->write(json_encode(["status" => "success"]));
    } catch (PDOException $e) {
        $response->getBody()->write(json_encode(["status" => "fail", "error" => $e->getMessage()]));
    }
    $conn = null;

    return $response;
});

$app->post('/user/authenticate', function (Request $request, Response $response) use ($servername, $dbusername, $dbpassword, $dbname) {
    $data = json_decode($request->getBody());
    $usr = $data->username;
    $pass = $data->password;

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbusername, $dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT * FROM users WHERE username=:usr";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['usr' => $usr]);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $user = $stmt->fetch();

        if ($user && password_verify($pass, $user['password'])) {
            $jwt = generateToken($user['userid']);
            
            $sqlToken = "INSERT INTO user_tokens (userid, token) VALUES (:userid, :token)";
            $stmtToken = $conn->prepare($sqlToken);
            $stmtToken->execute(['userid' => $user['userid'], 'token' => $jwt]);
            
            $response->getBody()->write(json_encode(["status" => "success", "token" => $jwt]));
        } else {
            $response->getBody()->write(json_encode(["status" => "fail", "error" => "Authentication Failed"]));
        }
    } catch (PDOException $e) {
        $response->getBody()->write(json_encode(["status" => "fail", "error" => $e->getMessage()]));
    }

    return $response;
});

$app->post('/book-author/insert', function (Request $request, Response $response) use ($servername, $dbusername, $dbpassword, $dbname) {
    $data = json_decode($request->getBody());
    $bookTitle = $data->bookTitle;
    $authorName = $data->authorName;
    $token = $request->getHeader('Authorization')[0] ?? '';
    $token = str_replace('Bearer ', '', $token);

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbusername, $dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        //Validate token and get user ID
        $sqlCheckToken = "SELECT userid FROM user_tokens WHERE token = :token";
        $stmtCheckToken = $conn->prepare($sqlCheckToken);
        $stmtCheckToken->execute(['token' => $token]);

        if ($stmtCheckToken->rowCount() === 0) {
            return $response->withStatus(401)
                ->write(json_encode(["status" => "fail", "data" => ["title" => "Token is invalid or has been used."]]));
        }

        $decoded = JWT::decode($token, new Key('server_hack', 'HS256'));
        $userid = $decoded->data->userid;

        //Insert book and author
        $sqlBook = "INSERT INTO books (title) VALUES (:title)";
        $stmtBook = $conn->prepare($sqlBook);
        $stmtBook->execute(['title' => $bookTitle]);

        $sqlAuthor = "INSERT INTO authors (name, book_id) VALUES (:name, LAST_INSERT_ID())";
        $stmtAuthor = $conn->prepare($sqlAuthor);
        $stmtAuthor->execute(['name' => $authorName]);

        //Invalidate the used token
        $sqlDeleteToken = "DELETE FROM user_tokens WHERE token = :token";
        $stmtDeleteToken = $conn->prepare($sqlDeleteToken);
        $stmtDeleteToken->execute(['token' => $token]);

        //Generate a new token
        $newToken = generateToken($userid);
        $sqlInsertNewToken = "INSERT INTO user_tokens (userid, token) VALUES (:userid, :token)";
        $stmtInsertNewToken = $conn->prepare($sqlInsertNewToken);
        $stmtInsertNewToken->execute(['userid' => $userid, 'token' => $newToken]);

        $response->getBody()->write(json_encode(["status" => "success", "token" => $newToken]));
    } catch (Exception $e) {
        $response->getBody()->write(json_encode(["status" => "fail", "data" => ["title" => $e->getMessage()]]));
    }

    return $response;
});

$app->get('/book-author/retrieve', function (Request $request, Response $response) use ($servername, $dbusername, $dbpassword, $dbname) {
    $token = $request->getHeader('Authorization')[0] ?? '';
    $token = str_replace('Bearer ', '', $token);

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbusername, $dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sqlCheckToken = "SELECT userid FROM user_tokens WHERE token = :token";
        $stmtCheckToken = $conn->prepare($sqlCheckToken);
        $stmtCheckToken->execute(['token' => $token]);

        if ($stmtCheckToken->rowCount() === 0) {
            return $response->withStatus(401)
                ->write(json_encode(["status" => "fail", "data" => ["title" => "Token is invalid or has been used."]]));
        }

        $decoded = JWT::decode($token, new Key('server_hack', 'HS256'));
        $userid = $decoded->data->userid;

        $sql = "SELECT books.id AS book_id, books.title, authors.name AS author_name 
                FROM books 
                JOIN authors ON books.id = authors.book_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //Invalidate the used token and create a new one
        $sqlDeleteToken = "DELETE FROM user_tokens WHERE token = :token";
        $stmtDeleteToken = $conn->prepare($sqlDeleteToken);
        $stmtDeleteToken->execute(['token' => $token]);

        $newToken = generateToken($userid);
        $sqlInsertNewToken = "INSERT INTO user_tokens (userid, token) VALUES (:userid, :token)";
        $stmtInsertNewToken = $conn->prepare($sqlInsertNewToken);
        $stmtInsertNewToken->execute(['userid' => $userid, 'token' => $newToken]);

        $response->getBody()->write(json_encode(["status" => "success", "data" => $books, "token" => $newToken]));
    } catch (Exception $e) {
        $response->getBody()->write(json_encode(["status" => "fail", "data" => ["title" => $e->getMessage()]]));
    }

    return $response;
});

$app->put('/book-author/update/{id}', function (Request $request, Response $response, $args) use ($servername, $dbusername, $dbpassword, $dbname) {
    $data = json_decode($request->getBody());
    $bookTitle = $data->bookTitle;
    $authorName = $data->authorName;
    $bookId = $args['id'];
    $token = $request->getHeader('Authorization')[0] ?? '';
    $token = str_replace('Bearer ', '', $token);

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbusername, $dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        //Validate token
        $sqlCheckToken = "SELECT userid FROM user_tokens WHERE token = :token";
        $stmtCheckToken = $conn->prepare($sqlCheckToken);
        $stmtCheckToken->execute(['token' => $token]);

        if ($stmtCheckToken->rowCount() === 0) {
            return $response->withStatus(401)
                ->write(json_encode(["status" => "fail", "data" => ["title" => "Token is invalid or has been used."]]));
        }

        $decoded = JWT::decode($token, new Key('server_hack', 'HS256'));
        $userid = $decoded->data->userid;

        //Update book and author
        $sqlUpdateBook = "UPDATE books SET title = :title WHERE id = :id";
        $stmtUpdateBook = $conn->prepare($sqlUpdateBook);
        $stmtUpdateBook->execute(['title' => $bookTitle, 'id' => $bookId]);

        $sqlUpdateAuthor = "UPDATE authors SET name = :name WHERE book_id = :id";
        $stmtUpdateAuthor = $conn->prepare($sqlUpdateAuthor);
        $stmtUpdateAuthor->execute(['name' => $authorName, 'id' => $bookId]);

        //Invalidate token and create a new one
        $sqlDeleteToken = "DELETE FROM user_tokens WHERE token = :token";
        $stmtDeleteToken = $conn->prepare($sqlDeleteToken);
        $stmtDeleteToken->execute(['token' => $token]);

        $newToken = generateToken($userid);
        $sqlInsertNewToken = "INSERT INTO user_tokens (userid, token) VALUES (:userid, :token)";
        $stmtInsertNewToken = $conn->prepare($sqlInsertNewToken);
        $stmtInsertNewToken->execute(['userid' => $userid, 'token' => $newToken]);

        $response->getBody()->write(json_encode([
            "status" => "success",
            "message" => "Book and author updated successfully.",
            "token" => $newToken
        ], JSON_PRETTY_PRINT));
    } catch (Exception $e) {
        $response->getBody()->write(json_encode(["status" => "fail", "data" => ["title" => $e->getMessage()]]));
    }

    return $response;
});

$app->delete('/book-author/delete/{id}', function (Request $request, Response $response, $args) use ($servername, $dbusername, $dbpassword, $dbname) {
    $bookId = $args['id'];
    $token = $request->getHeader('Authorization')[0] ?? '';
    $token = str_replace('Bearer ', '', $token);

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbusername, $dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        //Validate token
        $sqlCheckToken = "SELECT userid FROM user_tokens WHERE token = :token";
        $stmtCheckToken = $conn->prepare($sqlCheckToken);
        $stmtCheckToken->execute(['token' => $token]);

        if ($stmtCheckToken->rowCount() === 0) {
            return $response->withStatus(401)
                ->write(json_encode(["status" => "fail", "data" => ["title" => "Token is invalid or has been used."]]));
        }

        $decoded = JWT::decode($token, new Key('server_hack', 'HS256'));
        $userid = $decoded->data->userid;

        //Delete book and author
        $sqlDeleteAuthor = "DELETE FROM authors WHERE book_id = :id";
        $stmtDeleteAuthor = $conn->prepare($sqlDeleteAuthor);
        $stmtDeleteAuthor->execute(['id' => $bookId]);

        $sqlDeleteBook = "DELETE FROM books WHERE id = :id";
        $stmtDeleteBook = $conn->prepare($sqlDeleteBook);
        $stmtDeleteBook->execute(['id' => $bookId]);

        //Invalidate token and create a new one
        $sqlDeleteToken = "DELETE FROM user_tokens WHERE token = :token";
        $stmtDeleteToken = $conn->prepare($sqlDeleteToken);
        $stmtDeleteToken->execute(['token' => $token]);

        $newToken = generateToken($userid);
        $sqlInsertNewToken = "INSERT INTO user_tokens (userid, token) VALUES (:userid, :token)";
        $stmtInsertNewToken = $conn->prepare($sqlInsertNewToken);
        $stmtInsertNewToken->execute(['userid' => $userid, 'token' => $newToken]);

        $response->getBody()->write(json_encode([
            "status" => "success",
            "message" => "Book and associated author deleted successfully.",
            "token" => $newToken
        ], JSON_PRETTY_PRINT));
    } catch (Exception $e) {
        $response->getBody()->write(json_encode(["status" => "fail", "data" => ["title" => $e->getMessage()]]));
    }

    return $response;
});

$app->run();

