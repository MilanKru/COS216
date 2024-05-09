<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Agents</title>
</head>
<body onload="load_agents()">
    <div class="container">
        <div class="sidebar">
            <h2>Pages</h2>
            <?php
            require_once 'navbar.php'
            ?>
        </div>
        <div class="main-panel">
            <div class="top-panel">
                <a href="../index.html" class="button-main"><i class ="fa fa-home"></i></a>
                <h2>Agents</h2>
                <button class="button-main" onclick="setTheme(true)">Theme</button>
            </div>
            <div class="content">
                <h1>Agents per page</h1>
                <input type="number" placeholder="30" class="upper-bound-price-input" id="limits" oninput="load_agents_limit()" min="1" max="1000">
                <ul id="agent-list">
                </ul>
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
