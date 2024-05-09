<?php
require_once 'config.php';

class API 
{
    private $conn;

    public function __construct() 
    {
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function registerUser($name, $surname, $email, $password) 
    {
        if(empty($name))
        {
            return $this->generateErrorResponse("Missing Name");
        }
        if(empty($surname))
        {
            return $this->generateErrorResponse("Missing Surname");
        }

        if(empty($email))
        {
            return $this->generateErrorResponse("Missing Email");
        }

        if(empty($password))
        {
            return $this->generateErrorResponse("Missing Password");
        }

        if(!preg_match('/[a-z]/', $password))
        {
            return $this->generateErrorResponse("Password must contain atleast one lowercase");
        }
        if(!preg_match('/[A-Z]/', $password))
        {
            return $this->generateErrorResponse("Password must contain atleast one uppercase");
        }
        if (!preg_match('/\d/', $password)) 
        {
            return $this->generateErrorResponse("Password must contain atleast one digit");
        }
        if (!preg_match('/[^\w\d\s]/', $password)) 
        {
            return $this->generateErrorResponse("Password must contain atleast one symbol");
        }
        if (strlen($password) < 8) 
        {
            return $this->generateErrorResponse("Password must be at least 8 characters long");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
        {
            return $this->generateErrorResponse("Invalid email format");
        }

        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) 
        {
            return $this->generateErrorResponse("User already exists");
        }

        $sql = "INSERT INTO `users` (`name`, `surname`, `email`, `password` , `api` , `theme`) VALUES (?, ?, ?, ? ,?,'light')";
        $stmt = $this->conn->prepare($sql);
        $apiKey = $this->generateApiKey();

        $salt = $this->generateSalt($email);
        $salt_pass = $password.$salt;
        $hashedPassword = password_hash($salt_pass, PASSWORD_DEFAULT);
        $stmt->bind_param("sssss", $name, $surname, $email, $hashedPassword , $apiKey);
        $stmt->execute();

        return $this->generateSuccessResponse($apiKey);
    }

    private function generateSuccessResponse($apiKey) 
    {
        $response = array(
            "status" => "success",
            "timestamp" => time(),
            "data" => array(
                "apikey" => $apiKey
            )
        );
        return json_encode($response);
    }

    private function generateSalt($input) {
        $hash = hash('sha256', $input);
        
        $salt = substr($hash, 0, 16);
        
        return $salt;
    }

    private function generateSuccessListingsResponse($array)
    {
        $response = array(
            "status" => "success",
            "timestamp" => time(),
            "data" => $array
        );
        return json_encode($response);
    }

    public function generateErrorResponse($message) 
    {
        $response = array(
            "status" => "error",
            "message" => $message
        );
        return json_encode($response);
    }

    private function generateApiKey() 
    {
        return bin2hex(random_bytes(16));
    }

    public function getListings($api ,$type, $limit , $sort, $order , $fuzzy , $search , $return)
    {
        if (empty($api) || empty($type)) 
        {
            return $this->generateErrorResponse("Post parameters are missing");
        }

        if (!in_array(strtoupper($order), array('ASC', 'DESC'))) 
        {
            return $this->generateErrorResponse("Invalid order parameter. Order must be 'ASC' or 'DESC'");
        }
        if(!empty($limit))
        {
            if($limit <= 1)
            {
                $limit = 1;
            }
            if($limit >= 500)
            {   
                $limit = 500;
            }
        }
        $fetchImages = false;
        if ($return === "*" || (is_array($return) && in_array("images", $return))) 
        {
            $fetchImages = true;
            if ($return === "*") 
            {
                $return = ['id', 'title', 'location', 'price', 'bedrooms', 'bathrooms', 'url', 'parking_spaces', 'amenities', 'description', 'type', 'images'];
            }
        }
        
        $sql = "SELECT ";
        if (!in_array('id', $return)) 
        {
            $return[] = 'id';
        }
        if (empty($return)) 
        {
            $sql .= "*";
        } 
        else if (is_array($return)) 
        {
            $sql .= implode(", ", array_map(function($column) 
            {
                return "`$column`";
            }, $return));
        } 
        else
        {
            $sql .= $return;
        }

        $sql .= " FROM listings";
        if (!empty($search) && is_array($search)) 
        {
            $conditions = [];
            foreach ($search as $column => $term) 
            {
                if (!empty($term) && in_array($column, ['id', 'title', 'location', 'bedrooms', 'bathrooms', 'parking_spaces', 'amenities', 'type' , 'price_min' , 'price_max']))
                {
                    if ($column === 'location' || $column === 'type') 
                    {
                        $term = addslashes($term);
                    }
                    if ($column === 'bedrooms' || $column === 'bathrooms' || $column === 'parking_spaces') 
                    {
                        $conditions[] = "$column = '$term'";
                    } 
                    else if ($column === 'price_min') 
                    {
                        $conditions[] = "price >= $term";
                    } 
                    else if ($column === 'price_max') 
                    {
                        $conditions[] = "price <= $term";
                    } 
                    else if ($column === 'type') 
                    {
                        $conditions[] = "type = '$term'";
                    } 
                    else 
                    {
                        if ($fuzzy) 
                        {
    
                            $term = '%' . addslashes($term) . '%';
                            $conditions[] = "$column LIKE '$term'";
                        } 
                        else 
                        {
                            $conditions[] = "$column = '$term'";
                        }
                    }
                }
            }
            if (!empty($conditions)) {
                $sql .= " WHERE " . implode(" AND ", $conditions);
            }
        }
    

        if(isset($sort) && isset($order)) 
        {
            $sql .= " ORDER BY $sort $order";
        }
        if(isset($limit)) {
            $sql .= " LIMIT " . (int)$limit;
        }
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        return $this->generateSuccessListingsResponse($data);
    }

    public function getAgents($limit=10)
    {
        $sql = "SELECT * FROM agencies LIMIT ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);

        return $this->generateSuccessListingsResponse($data);
    }

    public function loginUser($email , $password)
    {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s",$email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) 
        {
            while ($row = $result->fetch_assoc()) 
            {
                $salt = $this->generateSalt($email);
                $saltedPassword = $password.$salt;

                if(password_verify($saltedPassword , $row['password']))
                {
                    return $this->generateSuccessResponse($row['api']);
                }
            }
        }

        return $this->generateErrorResponse("Email not in database or password incorrect");

    }

    public function addFavourite($api , $item_id)
    {
        $sql = "INSERT IGNORE INTO `favourites` (`fav_id`, `listings_id`, `user_api`) VALUES (NULL, ?, ?);";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $item_id, $api);
        $stmt->execute();

        return $this->generateSuccessResponse($api);
    }

    public function removeFavourite($item_id, $api)
    {
        $sql = "DELETE FROM favourites WHERE `listings_id` = ? AND `user_api` = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $item_id, $api);
        $stmt->execute();
        if ($stmt->error) {
            return $this->generateErrorResponse("Failed to execute statement: " . $stmt->error);
        }
        return $this->generateSuccessResponse($api);
    }

    public function getFavourites($api , $limit , $sort, $order , $fuzzy ,  $return , $search )
    {
        if (empty($api)) 
        {
            return $this->generateErrorResponse("Post parameters are missing");
        }

        if (!in_array(strtoupper($order), array('ASC', 'DESC'))) 
        {
            return $this->generateErrorResponse("Invalid order parameter. Order must be 'ASC' or 'DESC'");
        }
        if(!empty($limit))
        {
            if($limit <= 1)
            {
                $limit = 1;
            }
            if($limit >= 500)
            {   
                $limit = 500;
            }
        }
        $fetchImages = false;
        if ($return === "*" || (is_array($return) && in_array("images", $return))) 
        {
            $fetchImages = true;
            if ($return === "*") 
            {
                $return = ['id', 'title', 'location', 'price', 'bedrooms', 'bathrooms', 'url', 'parking_spaces', 'amenities', 'description', 'type', 'images'];
            }
        }
        
        $sql = "SELECT ";
        if (!in_array('id', $return)) 
        {
            $return[] = 'id';
        }
        if (empty($return)) 
        {
            $sql .= "*";
        } 
        else if (is_array($return)) 
        {
            $sql .= implode(", ", array_map(function($column) 
            {
                return "`$column`";
            }, $return));
        } 
        else
        {
            $sql .= $return;
        }

        $sql .= " FROM favourites JOIN listings ON `listings_id` = `id` WHERE `user_api` = '{$api}'";
        if (!empty($search) && is_array($search)) 
        {
            $conditions = [];
            foreach ($search as $column => $term) 
            {
                if (!empty($term) && in_array($column, ['id', 'title', 'location', 'bedrooms', 'bathrooms', 'parking_spaces', 'amenities', 'type' , 'price_min' , 'price_max']))
                {
                    if ($column === 'location' || $column === 'type') 
                    {
                        $term = addslashes($term);
                    }
                    if ($column === 'bedrooms' || $column === 'bathrooms' || $column === 'parking_spaces') 
                    {
                        $conditions[] = "$column = '$term'";
                    } 
                    else if ($column === 'price_min') 
                    {
                        $conditions[] = "price >= $term";
                    } 
                    else if ($column === 'price_max') 
                    {
                        $conditions[] = "price <= $term";
                    } 
                    else if ($column === 'type') 
                    {
                        $conditions[] = "type = '$term'";
                    } 
                    else 
                    {
                        if ($fuzzy) 
                        {
    
                            $term = '%' . addslashes($term) . '%';
                            $conditions[] = "$column LIKE '$term'";
                        } 
                        else 
                        {
                            $conditions[] = "$column = '$term'";
                        }
                    }
                }
            }
            if (!empty($conditions)) {
                $sql .= " AND " . implode(" AND ", $conditions);
            }
        }
    

        if(isset($sort) && isset($order)) 
        {
            $sql .= " ORDER BY $sort $order";
        }
        if(isset($limit)) {
            $sql .= " LIMIT " . (int)$limit;
        }
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        return $this->generateSuccessListingsResponse($data);
    }

    public function getTheme($api)
    {
        $sql = "SELECT `theme` FROM users WHERE `api` = ?;";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $api);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);

        return $this->generateSuccessListingsResponse($data);
    }

    public function setTheme($api, $theme)
    {
        $sql = "UPDATE `users` SET `theme` = ? WHERE `api` = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss",$theme ,$api);
        $stmt->execute();

        return $this->generateSuccessListingsResponse($api);
    }

    public function getPreferences($api)
    {
        $sql = "SELECT preferences FROM users WHERE `api` = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $api);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);

        
        $preferences = $data[0]["preferences"];
        preg_match_all('/\[[^\]]*\]|[^,]+/', $preferences, $matches);

        $limit = trim($matches[0][0], ',');
        $sort = trim($matches[0][1], ',');
        $order = trim($matches[0][2], ',');
        $return = $matches[0][3];
        $search = $matches[0][4];
        
        $return_list = explode(',', trim($return, '[]'));
        $return = $return_list;
        $filters = array(
            "limit" => $limit,
            "sort" => $sort,
            "order" => $order,
            "return" => $return,
            "search" => $search
        );
        

        return $this->generateSuccessListingsResponse($filters);
    }
    public function setPreferences($api , $limit , $sort, $order , $return, $search)
    {
        $sql = "UPDATE `users` SET `preferences` = ? WHERE `api` = ?";
        $stmt = $this->conn->prepare($sql);
        $search = "[".implode(',', $search)."]";
        if($search == "[]")
        {
            $search = "[,,,,,,,,]";
        }
        if ($return == "*" || $return == "[*]") 
        {
            $return = ['id', 'title', 'location', 'price', 'bedrooms', 'bathrooms', 'url', 'parking_spaces', 'amenities', 'description', 'type', 'images'];
        }
        $return = "[".implode(',' , $return)."]";
        $string = $limit.",".$sort.",".$order.",".$return.",".$search;
        $stmt->bind_param("ss", $string,$api);
        $stmt->execute();
        return $this->generateSuccessResponse($api);
    }
}


$api = new API();
    if ($_SERVER["REQUEST_METHOD"] == "POST") 
    {
        $postData = json_decode(file_get_contents("php://input"), true);
        if (isset($postData["type"]) && $postData["type"] == "Register" && isset($postData["name"]) && isset($postData["surname"]) && isset($postData["email"]) && isset($postData["password"])) 
        {
            $response = $api->registerUser($postData["name"], $postData["surname"], $postData["email"], $postData["password"]);

            echo($response);
        }
        else if(isset($postData["type"]) && $postData["type"] == "Login")
        {
            $response = $api->loginUser($postData["email"] , $postData["password"]);

            echo($response);
        }
        else if(isset($postData["type"]) && $postData["type"] == "GetAllAgents")
        {
            $response = $api->getAgents($postData["limit"]);

            echo($response);
        }
        else if(isset($postData["type"]) && $postData["type"] == "GetAllListings")
        {
            $limit = isset($postData["limit"]) ? $postData["limit"] : 10;
            $sort = isset($postData["sort"]) ? $postData["sort"] : 'id';
            $order = isset($postData["order"]) ? $postData["order"] : 'ASC';
            $fuzzy = isset($postData["fuzzy"]) ? $postData["fuzzy"] : true;
            $search = isset($postData["search"]) ? $postData["search"] : '';
            $return = isset($postData["return"]) ? $postData["return"] : '*';
            $response = $api->getListings($postData["apikey"], $postData["type"], $limit, $sort, $order, $fuzzy, $search, $return);

            echo($response);
        }
        else if(isset($postData["type"]) && $postData["type"] == "GetFavourites")
        {
            $limit = isset($postData["limit"]) ? $postData["limit"] : 10;
            $sort = isset($postData["sort"]) ? $postData["sort"] : 'id';
            $order = isset($postData["order"]) ? $postData["order"] : 'ASC';
            $fuzzy = isset($postData["fuzzy"]) ? $postData["fuzzy"] : true;
            $search = isset($postData["search"]) ? $postData["search"] : '';
            $return = isset($postData["return"]) ? $postData["return"] : '*';
            $response = $api->getFavourites($postData["apikey"], $limit, $sort, $order, $fuzzy, $return, $search);

            echo($response);
        }
        else if(isset($postData["type"]) && $postData["type"] == "AddFavourites" && isset($postData["apikey"]) && isset($postData["listings_id"]))
        {
            $response = $api->addFavourite($postData["apikey"], $postData["listings_id"]);
            echo($response);
        }
        else if(isset($postData["type"]) && $postData["type"] == "RemoveFavourites" && isset($postData["apikey"]) && isset($postData["listings_id"]))
        {
            $response = $api->removeFavourite($postData["listings_id"], $postData["apikey"]);
            echo($response);
        }
        else if(isset($postData["type"]) && $postData["type"] == "GetTheme" && isset($postData["apikey"]))
        {
            $response = $api->getTheme($postData["apikey"]);
            echo($response);
        }
        else if(isset($postData["type"]) && $postData["type"] == "SetTheme" && isset($postData["theme"]))
        {
            $response = $api->setTheme($postData["apikey"] , $postData["theme"]);
            echo($response);
        } 
        else if(isset($postData["type"]) && $postData["type"] == "GetPreferences" && isset($postData["apikey"]))
        {
            $response = $api->getPreferences($postData["apikey"]);
            echo($response);
        }
        else if(isset($postData["type"]) && $postData["type"] == "SetPreferences" && isset($postData["apikey"]))
        {
            $limit = isset($postData["limit"]) ? $postData["limit"] : 10;
            $sort = isset($postData["sort"]) ? $postData["sort"] : 'id';
            $order = isset($postData["order"]) ? $postData["order"] : 'ASC';
            $search = isset($postData["search"]) ? $postData["search"] : '';
            $return = isset($postData["return"]) ? $postData["return"] : '*';
            $fuzzy = isset($postData["fuzzy"]) ? $postData["fuzzy"] : true;
            $response = $api->SetPreferences($postData["apikey"],$limit,$sort,$order,$return,$search);

            echo($response);
        }
        else 
        {
            echo $api->generateErrorResponse("Invalid request");
        }
    }
    else 
    {
        echo $api->generateErrorResponse("Invalid request method");
    }


?>
