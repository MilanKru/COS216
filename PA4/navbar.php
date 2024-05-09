<ul>
    <li><a href="listings.php" class="<?php echo (strpos($_SERVER['REQUEST_URI'], '/listings.php') !== false) ? 'button-sidebar-active' : 'button-sidebar'; ?><?php echo isset($_COOKIE['dark-mode']) && $_COOKIE['dark-mode'] === 'true' ? ' dark-mode' : ''; ?>">Listings</a></li>
    <li><a href="agents.php" class="<?php echo (strpos($_SERVER['REQUEST_URI'], '/agents.php') !== false) ? 'button-sidebar-active' : 'button-sidebar'; ?><?php echo isset($_COOKIE['dark-mode']) && $_COOKIE['dark-mode'] === 'true' ? ' dark-mode' : ''; ?>">Agents</a></li>
    <li><a href="calculator.php" class="<?php echo (strpos($_SERVER['REQUEST_URI'], '/calculator.php') !== false) ? 'button-sidebar-active' : 'button-sidebar'; ?><?php echo isset($_COOKIE['dark-mode']) && $_COOKIE['dark-mode'] === 'true' ? ' dark-mode' : ''; ?>">Calculator</a></li>
    <li><a href="favourites.php?sort=fav" class="<?php echo (strpos($_SERVER['REQUEST_URI'], '/favourites.php') !== false) ? 'button-sidebar-active' : 'button-sidebar'; ?><?php echo isset($_COOKIE['dark-mode']) && $_COOKIE['dark-mode'] === 'true' ? ' dark-mode' : ''; ?>">Favourites</a></li>
</ul>
