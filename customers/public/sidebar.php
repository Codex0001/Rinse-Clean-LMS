<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!----======== CSS ======== -->
    <link rel="stylesheet" href="../../customers/css/style.css">
    
    <!----===== Boxicons CSS ===== -->
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
<nav class="sidebar close">
        <header>
            <div class="image-text">
                <span class="image">
                    <img src="../../assets/images/icons/laundry-machine.png" alt="logo" style="width: 50px; height: 50px;">
                </span>

                <div class="text logo-text">
                    <span class="name">Rinse Clean</span>
                    <span class="profession">customer Panel</span>
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
                        <a href="../../admin/dashboard.php">
                            <i class='bx bx-home-alt icon'></i>
                            <span class="text nav-text">Dashboard</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="#" class="submenu-toggle">
                            <i class='bx bx-bar-chart-alt-2 icon'></i>
                            <span class="text nav-text">Reports</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="#">
                            <i class='bx bx-user icon'></i>
                            <span class="text nav-text">Customers</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="#" class="submenu-toggle">
                            <i class='bx bx-cabinet icon'></i>
                            <span class="text nav-text">Inventory</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="#">
                            <i class='bx bx-group icon'></i>
                            <span class="text nav-text">Staff</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="#">
                            <i class='bx bx-credit-card icon'></i>
                            <span class="text nav-text">Payments</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="#">
                            <i class='bx bx-user icon'></i>
                            <span class="text nav-text">Access Control</span>
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
                    <a href="../../public/login/logout.php">
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
        const body = document.querySelector('body'),
            sidebar = body.querySelector('nav'),
            toggle = body.querySelector(".toggle"),
            searchBtn = body.querySelector(".search-box"),
            modeSwitch = body.querySelector(".toggle-switch"),
            modeText = body.querySelector(".mode-text");

        toggle.addEventListener("click", () => {
            sidebar.classList.toggle("close");
        });

        searchBtn.addEventListener("click", () => {
            sidebar.classList.remove("close");
        });

        modeSwitch.addEventListener("click", () => {
            body.classList.toggle("dark");

            if (body.classList.contains("dark")) {
                modeText.innerText = "Light mode";
            } else {
                modeText.innerText = "Dark mode";
            }
        });

        // Active link highlight
        const navLinks = document.querySelectorAll('.nav-link a');

        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                navLinks.forEach(nav => nav.parentElement.classList.remove('active')); // Remove active class from all links
                link.parentElement.classList.add('active'); // Add active class to clicked link
            });
        });

        // Submenu toggle
        const submenuToggles = document.querySelectorAll('.submenu-toggle');

        submenuToggles.forEach(toggle => {
            toggle.addEventListener('click', (e) => {
                e.preventDefault(); // Prevent default anchor click behavior
                const submenu = toggle.nextElementSibling; // Get the corresponding submenu

                // Toggle the submenu display
                if (submenu.style.display === "block") {
                    submenu.style.display = "none"; // Hide the submenu
                } else {
                    submenu.style.display = "block"; // Show the submenu
                }
            });
        });

        function updateTime() {
            const now = new Date();
            
            // Get the day of the week
            const days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
            const day = days[now.getDay()];

            // Format time (24-hour format)
            const options = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
            const timeString = now.toLocaleTimeString([], options);

            // Update the inner text to include both day and time
            document.getElementById('current-time').innerText = `${day}, ${timeString}`;
        }

        // Update the time every second
        setInterval(updateTime, 1000);

        // Initial call to set the time immediately on page load
        updateTime();

        <!-- JavaScript -->
            toggle.addEventListener("click", () => {
            sidebar.classList.toggle("close");
            adjustHeader(); // Call adjustHeader on toggle
        });

        // Adjust header padding based on sidebar state
        function adjustHeader() {
            const headerStrip = document.querySelector(".header-strip");
            if (sidebar.classList.contains("close")) {
                headerStrip.style.paddingLeft = "50px"; // Adjust when sidebar is closed
            } else {
                headerStrip.style.paddingLeft = "30px"; // Default padding when sidebar is open
            }
        }

        // Ensure correct padding on page load
        window.addEventListener('load', adjustHeader);
    </script>    

</body>
</html>
