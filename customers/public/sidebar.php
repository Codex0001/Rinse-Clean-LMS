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
                <img src="../../assets/images/icons/laundry-machine.png" alt="logo" style="width: 50px; height: 50px;">
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
                    <a href="../../customers/dashboard.php"> 
                        <i class='bx bx-home icon'></i> <!-- Updated icon -->
                        <span class="text nav-text">Dashboard</span>
                    </a>
                </li>

                <li class="nav-link">
                    <a href="../../customers/reports.php"> 
                        <i class='bx bx-file icon'></i> <!-- Updated icon -->
                        <span class="text nav-text">Reports</span>
                    </a>
                </li>

                <li class="nav-link">
                    <a href="../../customers/profile.php"> 
                        <i class='bx bx-user icon'></i> <!-- Kept the same icon for profile -->
                        <span class="text nav-text">My Profile</span>
                    </a>
                </li>

                <li class="nav-link">
                    <a href="../../customers/feedback.php"> 
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
        modeText = body.querySelector(".mode-text"),
        homeSection = body.querySelector('.home'); // Select the home section

    toggle.addEventListener("click", () => {
        sidebar.classList.toggle("close");
        
        // Toggle the 'collapsed' class on the home section
        if (sidebar.classList.contains("close")) {
            homeSection.classList.add('collapsed'); // Add class for collapsed state
        } else {
            homeSection.classList.remove('collapsed'); // Remove class for open state
        }
    });

    searchBtn.addEventListener("click", () => {
        sidebar.classList.remove("close");
        homeSection.classList.remove('collapsed'); // Ensure home section adjusts if sidebar is opened
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
  
</script>

</body>
</html>
