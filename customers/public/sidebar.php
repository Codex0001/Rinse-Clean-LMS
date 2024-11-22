<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!----======== CSS ======== -->
    <link rel="stylesheet" href="../../admin/css/style.css">
    
    <!----===== Boxicons CSS ===== -->
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
 

    
<!-- Sidebar  -->
<nav class="sidebar close">
    <header>
        <div class="image-text">
            <span class="image">
            <i class='bx bx-loader icon' alt="logo" style="width: 50px; height: 50px;"></i>
            </span>


            <div class="text logo-text">
                <span class="name">Rinse Clean</span>
                <span class="profession">Customer | Panel</span>
            </div>
        </div>

        <i class='bx bx-chevron-right toggle'></i>
    </header>

    <div class="menu-bar">
        <div class="menu">
            <li class="search-box">
                <i class='bx bx-search icon'></i>
                <input type="text" placeholder="search">
            </li>

            <ul class="menu-links">
                <li class="nav-link active">
                    <a href="dashboard.php"> 
                        <i class='bx bx-home icon'></i> <!-- Updated icon -->
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
                    <a href="reports.php"> 
                        <i class='bx bx-file icon'></i> <!-- Updated icon -->
                        <span class="text nav-text">Reports</span>
                    </a>
                </li>

                <li class="nav-link">
                    <a href="my_profile.php"> 
                        <i class='bx bx-user icon'></i> <!-- Kept the same icon for profile -->
                        <span class="text nav-text">My Profile</span>
                    </a>
                </li>

                <li class="nav-link">
                    <a href="feeback.php"> 
                        <i class='bx bx-chat icon'></i> <!-- Updated icon -->
                        <span class="text nav-text">Feedback</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Time Feature -->
        <div class="time-display">
            <span id="current-time"></span>
        </div>

        <div class="bottom-content">
            <li class="">
                <a href="../public/login/logout.php" class="logout-button">
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
            modeText = body.querySelector(".mode-text"),
            homeSection = body.querySelector('.home');

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
                homeSection.classList.remove('collapsed'); // Adjust home section margin
            } else {
                sidebar.classList.add("close"); // Ensure sidebar is closed on small screens
                homeSection.classList.add('collapsed');
            }
        }

        // Initial check for screen size on load
        checkScreenSize();

        // Load theme from localStorage
        loadTheme();

        // Resize event listener to adjust sidebar on window resize
        window.addEventListener('resize', checkScreenSize);

        toggle.addEventListener("click", () => {
            const isClosed = sidebar.classList.toggle("close");
            homeSection.classList.toggle('collapsed', isClosed);
        });

        searchBtn.addEventListener("click", () => {
            sidebar.classList.remove("close");
            homeSection.classList.remove('collapsed');
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
