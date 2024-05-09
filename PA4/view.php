<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>View</title>
</head>
<body onload="load_view()">
    <div class="container">
        <div class="sidebar">
            <h2>Pages</h2>
            <?php
            require_once 'navbar.php'
            ?>
        </div>
        <div class="main-panel">
            <div class="top-panel">
            <a href="login.php" class="button-main" onclick="logout()"><i class ="fa fa-home"></i></a>
            <h2>View</h2>
            <button class="button-main" onclick="setTheme(true)">Theme</button>
            </div>
            <div class="content">
                <div class="listing-details">
                    <h1>5 Bedroom House in Wapadrand</h1>
                    <div class ="carousel">
                        <div class="slides">
                        </div>
                    </div>
                    <div class="listing-info">
                    </div>
                </div>
            </div>
            <?php require_once 'footer.php' ?>
        </div>
    </div>
    <div id="loading-container">
        <img src="loading.gif" alt="Loading" />
    </div>
    <script src="index.js" charset="UTF-8"></script>
</body>
</html>
