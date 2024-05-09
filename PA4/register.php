<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
</head>
<body>
    <h2>Registration Form</h2>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") 
    {
        $name = $_POST['name'];
        $surname = $_POST['surname'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        $api_url = 'https://wheatley.cs.up.ac.za/u04948123/api.php';
        $data = array(
            'type' => 'Register',
            'name' => $name,
            'surname' => $surname,
            'email' => $email,
            'password' => $password
        );
        
        $curl = curl_init();

        $username = 'u04948123';
        $password = '3918Milo';

        curl_setopt($curl, CURLOPT_URL, $api_url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
        ));
        curl_setopt($curl, CURLOPT_USERPWD, $username . ':' . $password);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);

        if ($response === false) 
        {
            echo 'Error: ' . curl_error($curl);
        } else 
        {
            $responseData = json_decode($response, true);
            
            if (isset($responseData['status']) && $responseData['status'] === 'success' && isset($responseData['data']['apikey'])) 
            {
                $apiKey = $responseData['data']['apikey'];
                echo 'API Key: ' . $apiKey;
                echo "<script>";
                echo "localStorage.setItem('apiKey', '" . $apiKey . "');";
                echo "window.location.href = 'listings.php';";
                echo "</script>";
            } 
            else if(isset($responseData) && isset($responseData['message']))
            {
                echo $responseData['message'];
            }
        }
        
        
    }
    ?>
    <form method="post">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required><br><br>

        <label for="surname">Surname:</label>
        <input type="text" id="surname" name="surname" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>

        <input type="submit" value="Register">
    </form>
    <a href="login.php"><button>Login</button></a>
</body>
</html>