<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Calculator</title>
</head>
<body onload="calculator_mort()">
    <div class="container" >
        <div class="sidebar">
            <h2>Pages</h2>
            <?php
            require_once 'navbar.php'
            ?>
        </div>
        <div class="main-panel">
            <div class="top-panel">
            <a href="login.php" class="button-main" onclick="logout()"><i class ="fa fa-home"></i></a>
                <h2>Calculator</h2>
                <button class="button-main" onclick="setTheme(true)">Theme</button>
            </div>
            <div>
                <button class="mortgage" id="mortgage" onclick="calculator_mort()">Mortgage Payments</button>
                <button class="aff" id="aff" onclick="calculator_aff()">Affordability</button>
            </div>
        <div class="form-content" id="form-content">
            <form>
                <div>Purchase Price</div>
                <span>R <input type="text" value="1000000"></span>
                <div>Deposit</div>
                <span>R <input type="text" value="0"></span>
                <div>Interest Rate</div>
                <span><input type="text" value="10"> %</span>
                <div>Loan Term</div>
                <span><input type="text" value="24"> months</span>
                <div class="submit-form"><input type="submit" value="Submit"></div>
            </form>
            <div class="results-form-content">
                <div><span>Monthly Payment:</span>R 10,000</div>
                <div><span>Total:</span>R 450,000</div>
                <div><span>Income Required:</span>R 69,420</div>
            </div>
        </div>
        <?php
            require_once 'footer.php'
        ?>
        </div>
    </div>
    <div id="loading-container">
        <img src="loading.gif" alt="Loading" />
    </div>
    <script src="index-calculator.js" charset="UTF-8"></script>
</body>
</html>
