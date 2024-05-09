<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Listings</title>
</head>
<body onload="load_listings()">
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
                <h2>Listings</h2>
                <button class="button-main" onclick="setTheme(true)">Theme</button>
            </div>
            <div class="filters-top">
                <div class="dropdown">
                    <button class="sort-button">Sort</button>
                    <div class="dropdown-content">
                      <a href="#" onclick="load_listings()">Most Popular</a>
                      <a href="#" onclick="load_listings_title()">Title</a>
                      <a href="#" onclick="load_listings_loc()">Location</a>
                      <a href="#" onclick="load_listings_price()">Price</a>
                      <a href="#" onclick="load_listings_bed()">Bedrooms</a>
                      <a href="#" onclick="load_listings_bath()">Bathrooms</a>
                      <a href="#" onclick="load_listings_park()">Parking Space</a>
                    </div>
                  </div>
                <button class="buy-button" onclick="load_listings_buy()">Buy</button>
                <button class="rent-button" onclick="load_listings_rent()">Rent</button>
                <div class="search-container">
                    <input type="text" placeholder="Search..." class="search-input" id="search-input" oninput="load_listings_search()">
                    <button type="submit" class="search-button"  id="asc-desc" onclick="load_listings_asc_desc()"><i id="asc-desc-icon" class="fa fa-angle-down"></i></button>
                </div>
                <button id="reset-filters" onclick="load_listings_reset_filters()">Reset Filters</button>
            </div>
            <div class="filters-bottom">
                <div class="price-range">
                    <h1>Price</h1>
                    <input type="number" placeholder="0" class="lower-bound-price-input" id="price-min" oninput="load_listings_min()" min="0">
                    <h2>to</h2>
                    <input type="number" placeholder="1000000" class="upper-bound-price-input" id="price-max" oninput="load_listings_max()" min="0">
                    <h1>Bedrooms</h1>
                    <input type="number" placeholder="2" class="upper-bound-price-input" id="bedrooms" oninput="load_listings_bedrooms()" min="0" max="20">
                    <h1>Bathrooms</h1>
                    <input type="number" placeholder="2" class="upper-bound-price-input" id="bathrooms" oninput="load_listings_bathrooms()" min="0" max="20">
                    <h1>Parking Spaces</h1>
                    <input type="number" placeholder="2" class="upper-bound-price-input" id="parkingspaces" oninput="load_listings_parkingspaces()" min="0" max="20">
                    <h1>per page</h1>
                    <input type="number" placeholder="30" class="upper-bound-price-input" id="limits" oninput="load_listings_limit()" min="1" max="1000">
                </div>
            </div>
            <div class="content">
                <ul id="house-list">
                </ul>
            </div>
            <div>
                <?php
                    require_once 'footer.php'
                ?>
            </div>
        </div>

        <div id="loading-container">
            <img src="loading.gif" alt="Loading" />
        </div>
    </div>
    <script src="index.js" charset="UTF-8"></script>
</body>
</html>
