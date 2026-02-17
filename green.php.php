<?php
session_start();
// Basic Logic: Handle Login, Logout, and View Switching
if (isset($_GET['logout'])) { session_destroy(); header("Location: " . $_SERVER['PHP_SELF']); exit; }
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    if ($_POST['email'] == "student@jrcc.edu" && $_POST['password'] == "password123") {
        $_SESSION['user'] = "Irwin Williams";
    } else { $error = "Invalid credentials!"; }
}
$isLoggedIn = isset($_SESSION['user']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JRCC Mobile App</title>
    <style>
        :root { --jrcc-green: #7ED957; --bg: #f8f9fa; --dark: #333; }
        body { font-family: 'Arial', sans-serif; background: #e0e0e0; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        /* Phone Frame */
        .app-container { width: 360px; height: 740px; background: white; border-radius: 40px; border: 10px solid #222; position: relative; overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.3); display: flex; flex-direction: column; }
        .status-bar { height: 30px; padding: 5px 20px; display: flex; justify-content: space-between; font-size: 12px; font-weight: bold; background: white; }
        /* Header & Blobs */
        .header-blob { background: var(--jrcc-green); height: 180px; border-radius: 0 0 80px 80px; padding: 25px; color: white; position: relative; z-index: 1; }
        .card-content { background: white; margin: -60px 20px 20px; padding: 25px; border-radius: 25px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); z-index: 2; text-align: center; }
        /* Forms */
        input { width: 100%; padding: 12px; margin: 8px 0; border: 1px solid #eee; border-radius: 10px; background: #fdfdfd; box-sizing: border-box; }
        .btn { background: var(--jrcc-green); color: white; border: none; width: 100%; padding: 15px; border-radius: 12px; font-weight: bold; cursor: pointer; margin-top: 10px; }
        /* Dashboard Specific */
        .feed { flex: 1; overflow-y: auto; padding: 20px; background: #fff; }
        .ad-card { background: #004d40; color: white; border-radius: 20px; padding: 20px; margin-bottom: 20px; position: relative; overflow: hidden; }
        .nav-bar { height: 70px; border-top: 1px solid #eee; display: flex; justify-content: space-around; align-items: center; background: #fff; font-size: 20px; }
        .nav-bar a { text-decoration: none; color: #ccc; }
        .nav-bar a.active { color: var(--jrcc-green); }
    </style>
</head>
<body>

<div class="app-container">
    <div class="status-bar"><span>11:18</span><span>📶 🔋</span></div>

    <?php if (!$isLoggedIn): ?>
        <div class="header-blob">
            <h1 style="margin:0">Welcome Onboard!</h1>
            <p>Please Register or <span style="color: #222;">Login</span></p>
        </div>
        <div class="card-content">
            <form method="POST">
                <input type="email" name="email" placeholder="Enter your email" required>
                <input type="password" name="password" placeholder="Password" required>
                <?php if(isset($error)) echo "<p style='color:red; font-size:12px;'>$error</p>"; ?>
                <button type="submit" name="login" class="btn">Sign In</button>
            </form>
            <p style="font-size: 12px; color: #888; margin-top: 20px;">Forgot Password? | <a href="#" style="color:var(--jrcc-green)">Register</a></p>
        </div>
        <div style="text-align:center; padding-top: 50px;">
            <div style="font-size: 50px; color: var(--jrcc-green);">✚</div>
            <p>JRCC Application</p>
        </div>

    <?php else: ?>
        <div class="header-blob" style="height: 140px;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <h2 style="margin:0">JRCC 👋</h2>
                    <small>Faith Reigns Christian College</small>
                </div>
                <a href="?logout=1" style="text-decoration:none; background:white; color:black; padding:5px 10px; border-radius:5px; font-size:10px;">Logout</a>
            </div>
            <input type="text" placeholder="Search places..." style="margin-top:20px; border:none; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
        </div>

        <div class="feed">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 15px;">
                <b style="font-size: 18px;">Popular</b>
                <span style="color:var(--jrcc-green); font-size:12px;">View all</span>
            </div>
            
            <div class="ad-card">
                <h3 style="margin:0">SCHOOL ADMISSION</h3>
                <p style="font-size:12px; opacity:0.8;">CLASS OF 2026 NOW OPEN</p>
                <button style="background:orange; border:none; padding:5px 10px; border-radius:5px; color:white; font-weight:bold; cursor:pointer;">Enroll Now</button>
            </div>

            <div style="background:#f0f0f0; height:100px; border-radius:20px; display:flex; align-items:center; justify-content:center; color:#888;">
                Latest School News Feed
            </div>
        </div>

        <div class="nav-bar">
            <a href="#" class="active">🏠</a>
            <a href="#">📅</a>
            <a href="#">🔔</a>
            <a href="#">👤</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
