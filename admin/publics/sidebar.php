<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- CSS -->
    <link rel="stylesheet" href="../../admin/css/style.css">
    
    <!-- Boxicons CSS -->
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <nav class="sidebar close">
        <header>
            <div class="image-text">
                <span class="image">
                <i class='bx bx-loader icon' alt="logo" style="width: 50px; height: 50px;"></i>
                </span>
                <div class="text logo-text">
                    <span class="name">Rinse Clean</span>
                    <span class="profession">Admin | Panel</span>
                </div>
            </div>
            <i class='bx bx-chevron-right toggle'></i>
        </header>

        <div class="menu-bar">
            <div class="menu">
                <li class="search-box"></li>

                <ul class="menu-links">
                    <li class="nav-link active">
                        <a href="dashboard.php">
                            <i class='bx bx-home-alt icon'></i>
                            <span class="text nav-text">Dashboard</span>
                        </a>
                    </li>

                    <li class="nav-link active">
                    <a href="orders.php"> 
                        <i class='bx bx-shopping-bag icon'></i> <!-- Updated icon -->
                         <span class="text nav-text">Orders</span>
                    </a>
                    </li>
                    <li class="nav-link">
                        <a href="customers.php">
                            <i class='bx bx-user icon'></i>
                            <span class="text nav-text">Customers</span>
                        </a>
                    </li>
                    <li class="nav-link submenu-toggle">
                        <a href="inventory.php">
                            <i class='bx bx-cabinet icon'></i>
                            <span class="text nav-text">Inventory</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="staff.php">
                            <i class='bx bx-group icon'></i>
                            <span class="text nav-text">Staff</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="salary.php">
                            <i class='bx bx-credit-card icon'></i>
                            <span class="text nav-text">salary</span>
                        </a>
                    </li>
                    <li class="nav-link submenu-toggle">
                        <a href="reports.php">
                            <i class='bx bx-bar-chart-alt-2 icon'></i>
                            <span class="text nav-text">Reports</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="settings.php">
                            <i class='bx bx-user icon'></i>
                            <span class="text nav-text">Settings</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Time Display -->
            <div class="time-display">
                <span id="current-time"></span>
            </div>

            <div class="bottom-content">
                <li>
                    <a href="../public/login/logout.php">
                        <i class='bx bx-log-out icon'></i>
                        <span class="text nav-text">Logout</span>
                    </a>
                </li>
                <li class="mode">
                    <div class="sun-moon">
                        <i class='bx bx-moon icon moon'></i>
                        <i class='bx bx-sun icon sun'></i>
                    </div>
                    <span class="mode-text text">Dark Mode</span>
                    <div class="toggle-switch">
                        <span class="switch"></span>
                    </div>
                </li>
            </div>
        </div>
    </nav>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const body = document.querySelector('body'),
                sidebar = body.querySelector('nav'),
                toggle = body.querySelector(".toggle"),
                searchBtn = body.querySelector(".search-box"),
                modeSwitch = body.querySelector(".toggle-switch"),
                modeText = body.querySelector(".mode-text");

            // Function to load the theme on page load
            function loadTheme() {
                const storedTheme = localStorage.getItem('theme');
                if (storedTheme) {
                    body.classList.add(storedTheme);
                    modeText.innerText = storedTheme === 'dark' ? "Light mode" : "Dark mode";
                }
            }

            // Function to save the current theme to localStorage
            function saveTheme(theme) {
                localStorage.setItem('theme', theme);
            }

            // Automatically open sidebar on large screens
            function checkScreenSize() {
                if (window.innerWidth >= 768) { // Change this value based on your breakpoint
                    sidebar.classList.remove("close"); // Ensure sidebar is open on large screens
                } else {
                    sidebar.classList.add("close"); // Ensure sidebar is closed on small screens
                }
            }

            // Initial check for screen size on load
            checkScreenSize();

            // Load theme from localStorage
            loadTheme();

            // Resize event listener to adjust sidebar on window resize
            window.addEventListener('resize', checkScreenSize);

            toggle.addEventListener("click", () => {
                sidebar.classList.toggle("close");
            });

            searchBtn.addEventListener("click", () => {
                sidebar.classList.remove("close");
            });

            modeSwitch.addEventListener("click", () => {
                body.classList.toggle("dark");
                const currentTheme = body.classList.contains("dark") ? "dark" : "light";
                modeText.innerText = currentTheme === "dark" ? "Light mode" : "Dark mode";
                saveTheme(currentTheme); // Save current theme to localStorage
            });

            const navLinks = document.querySelectorAll('.nav-link a');
            navLinks.forEach(link => {
                link.addEventListener('click', () => {
                    navLinks.forEach(nav => nav.parentElement.classList.remove('active'));
                    link.parentElement.classList.add('active');
                });
            });

            function updateTime() {
                const now = new Date();
                const days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
                const day = days[now.getDay()];
                const options = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
                const timeString = now.toLocaleTimeString([], options);
                document.getElementById('current-time').innerText = `${day}, ${timeString}`;
            }

            setInterval(updateTime, 1000);
            updateTime();
        });
    </script>
</body>
</html>
