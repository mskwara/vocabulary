<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require __DIR__ . '/../vendor/autoload.php';

$app = new \Slim\App;

$app->get('/api/dictionaries/{userId}',
    function (Request $request, Response $response, array $args) {
        $servername = "serwer2001916.home.pl";
        $username = "32213694_vocabulary";
        $password = "vocpassword123";
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
        $password = "vocpassword123";
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
        $password = "vocpassword123";
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
        $password = "vocpassword123";
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
        $password = "vocpassword123";
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
        $password = "vocpassword123";
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
    }
);

$app->post('/api/word/update',
     function (Request $request, Response $response, array $args) {

        $servername = "serwer2001916.home.pl";
        $username = "32213694_vocabulary";
        $password = "vocpassword123";
        $dbname = "32213694_vocabulary";

        $requestData = $request->getParsedBody();
        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $sql = "UPDATE words SET lang1 = ?, lang2 = ? WHERE id = ?";


        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssi', $requestData['lang1'], $requestData['lang2'], $requestData['id']); // 's' specifies the variable type => 'string'


      if ($stmt->execute() === TRUE) {
          echo "Record updated successfully";
      } else {
          echo "Error: " . $sql . "<br>" . $conn->error;
      }

      $conn->close();
    }
);

$app->post('/api/word/delete',
     function (Request $request, Response $response, array $args) {

        $servername = "serwer2001916.home.pl";
        $username = "32213694_vocabulary";
        $password = "vocpassword123";
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
        $password = "vocpassword123";
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
    $password = "vocpassword123";
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
        $password = "vocpassword123";
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
        $password = "vocpassword123";
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

$app->post('/api/users/add',
    function (Request $request, Response $response, array $args) {
        $servername = "serwer2001916.home.pl";
        $username = "32213694_vocabulary";
        $password = "vocpassword123";
        $dbname = "32213694_vocabulary";

        $requestData = $request->getParsedBody();
        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }


        $encryptedPass = password_hash($requestData['password'], PASSWORD_DEFAULT);
        $nick = strtolower($requestData['nick']);

        $sql = "INSERT INTO users (nick, password) VALUES(?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $nick, $encryptedPass); // 's' specifies the variable type => 'string'


        if ($stmt->execute() === TRUE) {
            echo "New user created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        $conn->close();
        return $requestData;
  }

);

$app->post('/api/validateLogin',
    function (Request $request, Response $response, array $args) {
        $servername = "serwer2001916.home.pl";
        $username = "32213694_vocabulary";
        $password = "vocpassword123";
        $dbname = "32213694_vocabulary";

        $requestData = $request->getParsedBody();
        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $nick = $requestData['nick'];
        $sql = "SELECT nick, password FROM users WHERE nick = \"$nick\"";
        $result = $conn->query($sql);
        $array = [];

        $finish = false;
        if ($result->num_rows > 0) {
            // output data of each row
            $row = $result->fetch_assoc();
            $hash = $row['password'];
            $finish = password_verify($requestData['password'], $hash);

        } else {
            echo "0 results";
        }
        
        if($finish){

            $conn->close();
            $wynik = "true";
            return $wynik;
        }
        else {
            $conn->close();
            $wynik = "false";
            return $wynik;
        }
        //return $response->withJson($array);
    }
);

$app->get('/api/users/{nick}',
    function (Request $request, Response $response, array $args) {
        $servername = "serwer2001916.home.pl";
        $username = "32213694_vocabulary";
        $password = "vocpassword123";
        $dbname = "32213694_vocabulary";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $nick = $args['nick'];
        $sql = "SELECT id, nick FROM users WHERE nick = \"$nick\"";
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

$app->post('/api/validateUniqueNick',
    function (Request $request, Response $response, array $args) {
        $servername = "serwer2001916.home.pl";
        $username = "32213694_vocabulary";
        $password = "vocpassword123";
        $dbname = "32213694_vocabulary";

        $requestData = $request->getParsedBody();
        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $nick = $requestData['nick'];
        $sql = "SELECT nick FROM users";
        $result = $conn->query($sql);
        $array = [];

        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                if(strtolower($row['nick']) == strtolower($nick)) {
                  return "false";
                }
            }

        }
        $conn->close();

        return "true";
    }
);

$app->post('/api/dictionary/delete',
    function (Request $request, Response $response, array $args) {
        $servername = "serwer2001916.home.pl";
        $username = "32213694_vocabulary";
        $password = "vocpassword123";
        $dbname = "32213694_vocabulary";

        $requestData = $request->getParsedBody();
        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $dictId = $requestData['id'];
        $sql = "DELETE FROM dictionaries WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $dictId);


        if ($stmt->execute() === TRUE) {
            echo "Record deleted successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        $sql1 = "SELECT id FROM lists WHERE dictId = $dictId";
        $result = $conn->query($sql1);
        $listsIds = [];
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $listsIds[] = $row['id'];
            }
        }

        $sql2 = "DELETE FROM lists WHERE dictId = ?";
        $stmt = $conn->prepare($sql2);
        $stmt->bind_param('i', $dictId);


        if ($stmt->execute() === TRUE) {
            echo "Record deleted successfully";
        } else {
            echo "Error: " . $sql2 . "<br>" . $conn->error;
        }

        $sql3 = 'DELETE FROM stats WHERE listId IN (' . implode(',', array_map('intval', $listsIds)) . ')';
        if ($conn->query($sql3) === TRUE) { 
            echo "Stats deleted successfully";
        } else {
            echo "Error: " . $sql3 . "<br>" . $conn->error;
        }

        $sql4 = 'DELETE FROM words WHERE listId IN (' . implode(',', array_map('intval', $listsIds)) . ')';
        if ($conn->query($sql4) === TRUE) {  
            echo "Words deleted successfully";
        } else {
            echo "Error: " . $sql4 . "<br>" . $conn->error;
        }

        $sql5 = 'DELETE FROM sharedLists WHERE listId IN (' . implode(',', array_map('intval', $listsIds)) . ')';
        if ($conn->query($sql5) === TRUE) {  
            echo "SharedLists deleted successfully";
        } else {
            echo "Error: " . $sql5 . "<br>" . $conn->error;
        }

        $sql6 = 'DELETE FROM sharedStats WHERE sharedListId IN (' . implode(',', array_map('intval', $listsIds)) . ')';
        if ($conn->query($sql6) === TRUE) {  
            echo "SharedLists stats deleted successfully";
        } else {
            echo "Error: " . $sql6 . "<br>" . $conn->error;
        }

        $conn->close();
    }
);

$app->post('/api/list/delete',
    function (Request $request, Response $response, array $args) {
        $servername = "serwer2001916.home.pl";
        $username = "32213694_vocabulary";
        $password = "vocpassword123";
        $dbname = "32213694_vocabulary";

        $requestData = $request->getParsedBody();
        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $listId = $requestData['id'];

        $sql2 = "DELETE FROM lists WHERE id = ?";
        $stmt = $conn->prepare($sql2);
        $stmt->bind_param('i', $listId);


        if ($stmt->execute() === TRUE) {
            echo "Record deleted successfully";
        } else {
            echo "Error: " . $sql2 . "<br>" . $conn->error;
        }

        $sql3 = "DELETE FROM stats WHERE listId = ?";
        $stmt = $conn->prepare($sql3);
        $stmt->bind_param('i', $listId);


        if ($stmt->execute() === TRUE) {
            echo "Record deleted successfully";
        } else {
            echo "Error: " . $sql3 . "<br>" . $conn->error;
        }

        $sql4 = "DELETE FROM words WHERE listId = ?";
        $stmt = $conn->prepare($sql4);
        $stmt->bind_param('i', $listId);


        if ($stmt->execute() === TRUE) {
            echo "Record deleted successfully";
        } else {
            echo "Error: " . $sql4 . "<br>" . $conn->error;
        }

        $sql5 = "DELETE FROM sharedLists WHERE listId = ?";
        $stmt = $conn->prepare($sql5);
        $stmt->bind_param('i', $listId);


        if ($stmt->execute() === TRUE) {
            echo "Deleted shared lists successfully";
        } else {
            echo "Error: " . $sql5 . "<br>" . $conn->error;
        }

        $sql6 = "DELETE FROM sharedStats WHERE sharedListId = ?";
        $stmt = $conn->prepare($sql6);
        $stmt->bind_param('i', $listId);


        if ($stmt->execute() === TRUE) {
            echo "Shared stats deleted successfully";
        } else {
            echo "Error: " . $sql6 . "<br>" . $conn->error;
        }

        $conn->close();
    }
);

$app->post('/api/list/share',
     function (Request $request, Response $response, array $args) {

        $servername = "serwer2001916.home.pl";
        $username = "32213694_vocabulary";
        $password = "vocpassword123";
        $dbname = "32213694_vocabulary";

        $requestData = $request->getParsedBody();
        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $sql = "INSERT INTO sharedLists (listId, lang1, lang2, title, author)
        VALUES(?, ?, ?, ?, ?)";


        $stmt = $conn->prepare($sql);
        $stmt->bind_param('issss', $requestData['listId'], $requestData['lang1'],
         $requestData['lang2'], $requestData['title'], $requestData['author']);

      if ($stmt->execute() === TRUE) {
          echo "New record created successfully";
      } else {
          echo "Error: " . $sql . "<br>" . $conn->error;
      }

      $sql1 = "UPDATE lists SET shared = 1 WHERE id = ?";

        $stmt = $conn->prepare($sql1);
        $stmt->bind_param('i', $requestData['listId']);


      if ($stmt->execute() === TRUE) {
          echo "List updated successfully";
      } else {
          echo "Error: " . $sql1 . "<br>" . $conn->error;
      }

      $conn->close();
    }
);

$app->post('/api/list/unshare',
     function (Request $request, Response $response, array $args) {

        $servername = "serwer2001916.home.pl";
        $username = "32213694_vocabulary";
        $password = "vocpassword123";
        $dbname = "32213694_vocabulary";

        $requestData = $request->getParsedBody();
        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $sql = "DELETE FROM sharedLists WHERE listId = ?";


        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $requestData['listId']);

      if ($stmt->execute() === TRUE) {
          echo "Shared list removed successfully";
      } else {
          echo "Error: " . $sql . "<br>" . $conn->error;
      }

      $sql1 = "UPDATE lists SET shared = 0 WHERE id = ?";

        $stmt = $conn->prepare($sql1);
        $stmt->bind_param('i', $requestData['listId']);


      if ($stmt->execute() === TRUE) {
          echo "List updated successfully";
      } else {
          echo "Error: " . $sql1 . "<br>" . $conn->error;
      }

      $conn->close();
    }
);

$app->get('/api/sharedLists',
    function (Request $request, Response $response, array $args) {
        $servername = "serwer2001916.home.pl";
        $username = "32213694_vocabulary";
        $password = "vocpassword123";
        $dbname = "32213694_vocabulary";
        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $sql = "SELECT * FROM sharedLists";
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

$app->post('/api/sharedStats/add',
     function (Request $request, Response $response, array $args) {

    $servername = "serwer2001916.home.pl";
    $username = "32213694_vocabulary";
    $password = "vocpassword123";
    $dbname = "32213694_vocabulary";

    $requestData = $request->getParsedBody();
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $nick = $requestData['nick'];
    $listId = $requestData['listId'];
    $sqlget = "SELECT id FROM sharedStats WHERE nick = \"$nick\" AND sharedListId = \"$listId\"";

    $result = $conn->query($sqlget);
    $isAlready = 0;

    if ($result->num_rows > 0) {
        $isAlready = 1;
    } else {
        $isAlready = 0;
    }
    if($isAlready == 0){
        $sql = "INSERT INTO sharedStats (nick, sharedListId, correct, allCount)
        VALUES(?, ?, ?, ?)";


        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sidi',$requestData['nick'], $requestData['listId'], $requestData['correct'], $requestData['all']);


        if ($stmt->execute() === TRUE) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
    else {
        $sql = "UPDATE sharedStats SET correct = ?, allCount = ?, attempts = attempts + 1 WHERE nick = ? AND sharedListId = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iisi',$requestData['correct'], $requestData['all'], $requestData['nick'], $requestData['listId']);


        if ($stmt->execute() === TRUE) {
            echo "Record updated successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    

      $conn->close();
      return $requestData;
  }

);

$app->get('/api/scoreboard/{listId}',
    function (Request $request, Response $response, array $args) {
        $servername = "serwer2001916.home.pl";
        $username = "32213694_vocabulary";
        $password = "vocpassword123";
        $dbname = "32213694_vocabulary";
        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $listId = $args['listId'];
        $sql = "SELECT * FROM sharedStats WHERE sharedListId = $listId";
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

$app->run();
