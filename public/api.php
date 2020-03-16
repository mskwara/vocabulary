<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require __DIR__ . '/../vendor/autoload.php';

$app = new \Slim\App;

$app->get('/api/dictionaries/{userId}',
    function (Request $request, Response $response, array $args) {
        $servername = "serwer2001916.home.pl";
        $username = "32213694_vocabulary";
        $password = "FellDell2000!";
        $dbname = "32213694_vocabulary";
        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $userId = $args['userId'];
        $sql = "SELECT * FROM dictionaries WHERE userId = $userId";
        $result = $conn->query($sql);
        $array = [];

        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $array[] = $row;
            }
        } else {
            echo "0 results";
        }
        $conn->close();
        return $response->withJson($array);
    }
);

$app->get('/api/lists/{dictId}',
    function (Request $request, Response $response, array $args) {
        $servername = "serwer2001916.home.pl";
        $username = "32213694_vocabulary";
        $password = "FellDell2000!";
        $dbname = "32213694_vocabulary";
        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $dictId = $args['dictId'];
        $sql = "SELECT * FROM lists WHERE dictId = $dictId";
        $result = $conn->query($sql);
        $array = [];

        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $array[] = $row;
            }
        } else {
            echo "0 results";
        }
        $conn->close();
        return $response->withJson($array);
    }
);

$app->get('/api/words/{listId}',
    function (Request $request, Response $response, array $args) {
        $servername = "serwer2001916.home.pl";
        $username = "32213694_vocabulary";
        $password = "FellDell2000!";
        $dbname = "32213694_vocabulary";
        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $listId = $args['listId'];
        $sql = "SELECT * FROM words WHERE listId = $listId ORDER BY id DESC";
        $result = $conn->query($sql);
        $array = [];

        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $array[] = $row;
            }
        } else {
            echo "0 results";
        }
        $conn->close();
        return $response->withJson($array);
    }
);

$app->post('/api/dictionary/add',
     function (Request $request, Response $response, array $args) {

        $servername = "serwer2001916.home.pl";
        $username = "32213694_vocabulary";
        $password = "FellDell2000!";
        $dbname = "32213694_vocabulary";

      $requestData = $request->getParsedBody();
      // Create connection
      $conn = new mysqli($servername, $username, $password, $dbname);
      // Check connection
      if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
      }

      $sql = "INSERT INTO dictionaries (userId, lang1, lang2)
       VALUES('$requestData[userId]', ?, ?)";


        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $requestData['lang1'], $requestData['lang2']); // 's' specifies the variable type => 'string'


      if ($stmt->execute() === TRUE) {
          echo "New record created successfully";
      } else {
          echo "Error: " . $sql . "<br>" . $conn->error;
      }

      $conn->close();
      return $requestData;
  }

);

$app->post('/api/list/add',
     function (Request $request, Response $response, array $args) {

        $servername = "serwer2001916.home.pl";
        $username = "32213694_vocabulary";
        $password = "FellDell2000!";
        $dbname = "32213694_vocabulary";

      $requestData = $request->getParsedBody();
      // Create connection
      $conn = new mysqli($servername, $username, $password, $dbname);
      // Check connection
      if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
      }

      $sql = "INSERT INTO lists (dictId, title)
       VALUES('$requestData[dictId]', ?)";


        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $requestData['title']); // 's' specifies the variable type => 'string'


      if ($stmt->execute() === TRUE) {
          echo "New record created successfully";
      } else {
          echo "Error: " . $sql . "<br>" . $conn->error;
      }

      $conn->close();
      return $requestData;
  }

);

$app->post('/api/word/add',
     function (Request $request, Response $response, array $args) {

        $servername = "serwer2001916.home.pl";
        $username = "32213694_vocabulary";
        $password = "FellDell2000!";
        $dbname = "32213694_vocabulary";

      $requestData = $request->getParsedBody();
      // Create connection
      $conn = new mysqli($servername, $username, $password, $dbname);
      // Check connection
      if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
      }

      $sql = "INSERT INTO words (listId, lang1, lang2)
       VALUES('$requestData[listId]', ?, ?)";


        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $requestData['lang1'], $requestData['lang2']); // 's' specifies the variable type => 'string'


      if ($stmt->execute() === TRUE) {
          echo "New record created successfully";
      } else {
          echo "Error: " . $sql . "<br>" . $conn->error;
      }

      $conn->close();
      return $requestData;
  }

);

$app->post('/api/word/delete',
     function (Request $request, Response $response, array $args) {

        $servername = "serwer2001916.home.pl";
        $username = "32213694_vocabulary";
        $password = "FellDell2000!";
        $dbname = "32213694_vocabulary";

      $requestData = $request->getParsedBody();
      // Create connection
      $conn = new mysqli($servername, $username, $password, $dbname);
      // Check connection
      if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
      }
      $sql = "DELETE FROM words WHERE id = ?";


        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $requestData['id']);


      if ($stmt->execute() === TRUE) {
          echo "Record deleted successfully";
      } else {
          echo "Error: " . $sql . "<br>" . $conn->error;
      }

      $conn->close();
      return $requestData;
  }

);

$app->post('/api/words/setDifficulty',
     function (Request $request, Response $response, array $args) {

        $servername = "serwer2001916.home.pl";
        $username = "32213694_vocabulary";
        $password = "FellDell2000!";
        $dbname = "32213694_vocabulary";

      $requestData = $request->getParsedBody();
      // Create connection
      $conn = new mysqli($servername, $username, $password, $dbname);
      // Check connection
      if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
      }
      for($i = 0 ; $i < count($requestData) ; $i = $i+1){
          $obj = $requestData[$i];
        $sql = "UPDATE words SET difficulty = difficulty + '$obj[count]' WHERE id = '$obj[id]'";

        if ($conn->query($sql)) {
            echo "Difficulty updated";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
      }

      $conn->close();
      return $requestData;
  }

);

$app->post('/api/stats/add',
     function (Request $request, Response $response, array $args) {

    $servername = "serwer2001916.home.pl";
    $username = "32213694_vocabulary";
    $password = "FellDell2000!";
    $dbname = "32213694_vocabulary";

    $requestData = $request->getParsedBody();
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO stats (userId, listId, correct, allCount)
    VALUES(?, ?, ?, ?)";


    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iiii',$requestData['userId'], $requestData['listId'], $requestData['correct'], $requestData['all']);


    if ($stmt->execute() === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $sql1 = "UPDATE words SET testsCount = testsCount+1 WHERE listId = ?";

    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param('i', $requestData['listId']);


      if ($stmt1->execute() === TRUE) {
          echo "TestCount updated successfully";
      } else {
          echo "Error: " . $sql1 . "<br>" . $conn->error;
      }

      $conn->close();
      return $requestData;
  }

);

$app->get('/api/stats/{listId}',
    function (Request $request, Response $response, array $args) {
        $servername = "serwer2001916.home.pl";
        $username = "32213694_vocabulary";
        $password = "FellDell2000!";
        $dbname = "32213694_vocabulary";
        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $listId = $args['listId'];
        $sql = "SELECT * FROM stats WHERE listId = $listId";
        $result = $conn->query($sql);
        $array = [];

        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $percent = round($row['correct']/$row['allCount'], 2)*100;
                $array[] = $percent;
            }
        } else {
            echo "0 results";
        }
        $conn->close();
        return $response->withJson($array);
    }
);

$app->get('/api/stats/allLists/{dictId}',
    function (Request $request, Response $response, array $args) {
        $servername = "serwer2001916.home.pl";
        $username = "32213694_vocabulary";
        $password = "FellDell2000!";
        $dbname = "32213694_vocabulary";
        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $dictId = $args['dictId'];
        $sql = "SELECT id, title FROM lists WHERE dictId = $dictId";
        $result = $conn->query($sql);
        $listsIds = [];
        $listsTitles = [];
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $listsIds[] = $row['id'];
                $listsTitles[] = $row['title'];
            }
        }
        $allValues = [];
        for($i = 0 ; $i < count($listsIds) ; $i = $i + 1){
            $listId = $listsIds[$i];
            $sql1 = "SELECT * FROM stats WHERE listId = $listId";
            $result = $conn->query($sql1);

            $values = [];
            if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
                    $percent = round($row['correct']/$row['allCount'], 2)*100;
                    $values[] = $percent;
                }
            }
            $allValues[] = $values;
        }
        $array = [
            ['values' => $allValues, 'listsTitles' => $listsTitles]
         ];
        $conn->close();
        return $response->withJson($array);
    }
);

$app->run();
