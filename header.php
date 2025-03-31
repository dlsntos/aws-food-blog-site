<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yum Buds</title>
    <style>
        /* Reset styles */
        /* Header */
        header {
            background-color: white;
            padding: 15px 0;
            /* Removed side padding */
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #ddd;
            width: 100%;
        }

        /* Logo inside header */
        .logo img {
            height: 60px;
            margin-left: 20px;
            /* Added slight margin for alignment */
        }

        /* Navigation */
        nav {
            margin-right: 20px;
            /* Adjusted for better spacing */
        }

        nav ul {
            list-style-type: none;
            display: flex;
            gap: 20px;
        }

        nav ul li {
            display: inline;
        }

        nav ul li a {
            text-decoration: none;
            color: black;
            font-weight: bold;
            padding: 8px 12px;
            transition: color 0.3s ease-in-out;
        }

        nav ul li a:hover {
            color: #f4a261;
        }

        /* Hamburger Menu */
        .menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
        }

        /* Mobile Responsiveness */
        @media screen and (max-width: 768px) {
            .menu-btn {
                background-color: black;
            }

            nav ul {
                display: none;
                flex-direction: column;
                background: white;
                position: absolute;
                top: 60px;
                right: 10px;
                width: 200px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                border-radius: 8px;
                padding: 10px;
            }

            nav ul.active {
                display: flex;
            }

            nav ul li {
                text-align: center;
                margin: 10px 0;
            }

            .menu-btn {
                display: block;
            }
        }
    </style>
    <script>
        function toggleMenu() {
            document.getElementById("nav-links").classList.toggle("active");
        }
    </script>
</head>

<body>
    <header>
        <div class="logo">
            <a href="../public/all_blogs.php">
                <img src="../assets/yumbuds_logo.png" alt="Yum Buds Logo"> <!-- Replace with your logo -->
            </a>
        </div>
        <nav>
            <ul id="nav-links">
                <li><a href="../public/create_blog.php">New Blog</a></li>
                <li><a href="../public/all_blogs.php">All Blogs</a></li>
                <li><a href="../public/my_blogs.php">My Blogs</a></li>
                <li><a href="../includes/logout.php">Log Out</a></li>
            </ul>
            <button class="menu-btn" onclick="toggleMenu()">☰</button>
        </nav>
    </header>
</body>

</html>