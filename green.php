<?php
session_start();
$message = '';
$messageType = '';

// --------------------------
// AUTHENTICATION FUNCTIONALITY
// --------------------------
// Handle Registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $fullName = trim($_POST['fullName']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    if (empty($fullName) || empty($email) || empty($password) || empty($confirmPassword)) {
        $message = 'All fields are required!';
        $messageType = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Invalid email format!';
        $messageType = 'error';
    } elseif ($password !== $confirmPassword) {
        $message = 'Passwords do not match!';
        $messageType = 'error';
    } else {
        // Store users in session (replace with DB in production)
        if (!isset($_SESSION['users'])) $_SESSION['users'] = [];
        if (isset($_SESSION['users'][$email])) {
            $message = 'Email already registered!';
            $messageType = 'error';
        } else {
            $_SESSION['users'][$email] = [
                'fullName' => $fullName,
                'password' => password_hash($password, PASSWORD_DEFAULT)
            ];
            $message = 'Registration successful! Please login.';
            $messageType = 'success';
        }
    }
}

// Handle Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['loginEmail']);
    $password = $_POST['loginPassword'];

    if (empty($email) || empty($password)) {
        $message = 'Email and password are required!';
        $messageType = 'error';
    } elseif (!isset($_SESSION['users'][$email]) || !password_verify($password, $_SESSION['users'][$email]['password'])) {
        $message = 'Invalid email or password!';
        $messageType = 'error';
    } else {
        $_SESSION['loggedInUser'] = [
            'name' => $_SESSION['users'][$email]['fullName'],
            'email' => $email
        ];
        $message = '';
        // Auto-redirect to home after login
        echo "<script>setTimeout(() => showPage('home-page'), 500);</script>";
    }
}

// Handle Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// --------------------------
// MAIN APP MOCK DATA
// --------------------------
$popularItems = [
    [
        "title" => "SCHOOL ADMISSION",
        "subtitle" => "CLASS OF 2021 NOW OPEN",
        "desc" => "WE OFFER HOMEWORK HELP",
        "image" => "https://via.placeholder.com/150x250.png?text=Admission+Banner"
    ],
    [
        "title" => "ADMISSION OPEN",
        "subtitle" => "ENROLL NOW",
        "desc" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit",
        "image" => "https://via.placeholder.com/150x250.png?text=Open+Enrollment"
    ]
];

$notifications = [
    [
        "sender" => "Addison Lee",
        "type" => "call",
        "time" => "",
        "icon" => "📞"
    ],
    [
        "title" => "Notification Title",
        "text" => "Here's notification text",
        "time" => "34m ago",
        "icon" => "🔔"
    ],
    [
        "title" => "Notification Title",
        "text" => "Here's notification text",
        "time" => "34m ago",
        "icon" => "🔔"
    ],
    [
        "title" => "Notification Title",
        "text" => "Here's notification text",
        "time" => "34m ago",
        "icon" => "🔔"
    ],
    [
        "title" => "Shipping",
        "text" => "Arrives 5-7 days",
        "time" => "$0.00",
        "icon" => "📦"
    ]
];

// Calendar data
$currentDay = date("j");
$currentMonth = date("F Y");
$calendarDays = ["SUN", "MON", "TUE", "WED", "THU", "FRI", "SAT"];
$calendarDates = range(1, 30); // Simplified for demo
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JRCC School App</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #fff;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* GLOBAL STYLES */
        .bg-shape {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 25vh;
            background-color: #80CBC4;
            border-bottom-right-radius: 50%;
            z-index: -1;
        }

        .time {
            position: absolute;
            top: 0.5rem;
            left: 1.2rem;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .status-bar {
            position: absolute;
            top: 0.5rem;
            right: 1.2rem;
            display: flex;
            gap: 0.3rem;
        }

        .status-bar img {
            width: 1.2rem;
            height: auto;
        }

        .page {
            display: none;
            min-height: 100vh;
            padding-top: 2.5rem;
        }

        .page.active {
            display: flex;
            flex-direction: column;
        }

        button {
            padding: 1rem;
            border: none;
            border-radius: 8px;
            background-color: #80CBC4;
            color: #fff;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background-color: #64D8CB;
        }

        input {
            padding: 1rem;
            border: none;
            border-radius: 8px;
            background-color: #80CBC4;
            color: #fff;
            font-size: 1rem;
            width: 100%;
        }

        input::placeholder {
            color: #fff;
            opacity: 0.8;
        }

        .link-text {
            font-size: 0.9rem;
            margin-top: 1rem;
        }

        .link-text a {
            color: #80CBC4;
            text-decoration: none;
            font-weight: 600;
        }

        .message {
            padding: 1rem;
            margin: 1rem auto;
            max-width: 300px;
            border-radius: 8px;
            text-align: center;
            z-index: 10;
            position: relative;
        }

        .success {
            background-color: #C8E6C9;
            color: #2E7D32;
        }

        .error {
            background-color: #FFCDD2;
            color: #B71C1C;
        }

        /* --------------------------
        AUTHENTICATION PAGES STYLES
        -------------------------- */
        .auth-page {
            align-items: center;
            text-align: center;
            padding: 2rem 1.5rem;
        }

        .app-logo {
            width: 8rem;
            height: auto;
            margin: 2rem 0;
        }

        .school-logo {
            width: 7rem;
            height: auto;
            margin: 2rem 0;
            border-radius: 50%;
            border: 2px solid #333;
        }

        .auth-page h2 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .auth-page p {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 2rem;
            max-width: 250px;
        }

        .auth-form {
            width: 100%;
            max-width: 300px;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .menu-icon {
            width: 1.5rem;
            cursor: pointer;
            align-self: flex-start;
            margin-bottom: 1rem;
        }

        /* --------------------------
        MAIN APP PAGES STYLES
        -------------------------- */
        .main-page {
            display: none;
            flex-direction: column;
            min-height: 100vh;
            padding-top: 2.5rem;
        }

        .main-page.active {
            display: flex;
        }

        /* Header */
        .main-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.2rem;
            position: relative;
        }

        .header-left {
            display: flex;
            align-items: center;
        }

        .header-left span {
            font-weight: 600;
            color: #000;
            margin-right: 0.3rem;
        }

        .header-left p {
            font-size: 0.8rem;
            color: #555;
        }

        .header-right img {
            width: 1.5rem;
            height: auto;
        }

        /* Search Bar */
        .search-container {
            padding: 0 1.2rem;
            margin-bottom: 1rem;
        }

        .search-bar {
            display: flex;
            align-items: center;
            background-color: #fff;
            border-radius: 25px;
            padding: 0.5rem 1rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .search-bar input {
            flex: 1;
            border: none;
            outline: none;
            font-size: 0.9rem;
            padding: 0.3rem;
            background: transparent;
            color: #000;
        }

        .search-bar input::placeholder {
            color: #888;
        }

        .search-bar button {
            border: none;
            background: transparent;
            cursor: pointer;
            padding: 0;
        }

        .search-bar img {
            width: 1.2rem;
            height: auto;
        }

        /* Section Headers */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 1.2rem;
            margin-bottom: 1rem;
        }

        .section-header h3 {
            font-size: 1rem;
            font-weight: 600;
        }

        .section-header a {
            font-size: 0.8rem;
            color: #80CBC4;
            text-decoration: none;
        }

        /* Tabs */
        .tabs {
            display: flex;
            gap: 1.5rem;
            padding: 0 1.2rem;
            margin-bottom: 1rem;
        }

        .tab {
            font-size: 0.9rem;
            padding-bottom: 0.3rem;
            cursor: pointer;
        }

        .tab.active {
            border-bottom: 2px solid #000;
            font-weight: 600;
        }

        /* Popular Items Grid */
        .popular-grid {
            display: flex;
            gap: 1rem;
            padding: 0 1.2rem;
            overflow-x: auto;
            margin-bottom: 2rem;
        }

        .popular-card {
            min-width: 150px;
            border-radius: 10px;
            overflow: hidden;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .popular-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .popular-card .content {
            padding: 0.5rem;
        }

        .popular-card h4 {
            font-size: 0.9rem;
            margin-bottom: 0.2rem;
        }

        .popular-card p {
            font-size: 0.7rem;
            color: #555;
        }

        /* Loading Screen */
        .loading-screen {
            justify-content: center;
            align-items: center;
            background-color: #64D8CB;
        }

        /* Calendar Section */
        .calendar-container {
            padding: 0 1.2rem;
            margin-bottom: 2rem;
        }

        .calendar {
            background-color: #424242;
            color: #fff;
            border-radius: 10px;
            padding: 1rem;
        }

        .calendar-header {
            text-align: center;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .calendar-days, .calendar-dates {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 0.3rem;
            text-align: center;
        }

        .calendar-days div {
            font-size: 0.7rem;
            margin-bottom: 0.5rem;
            color: #ccc;
        }

        .calendar-dates div {
            font-size: 0.8rem;
            padding: 0.5rem;
            border-radius: 5px;
        }

        .calendar-dates .current-day {
            background-color: #80CBC4;
            font-weight: 600;
        }

        /* Notifications List */
        .notifications-list {
            padding: 0 1.2rem;
            margin-bottom: 5rem;
        }

        .notification-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.8rem;
            background-color: #eee;
            border-radius: 10px;
            margin-bottom: 0.8rem;
        }

        .notification-icon {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            background-color: #ccc;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.2rem;
        }

        .notification-icon.call {
            background-color: #ef9a9a;
            color: #b71c1c;
        }

        .notification-content {
            flex: 1;
        }

        .notification-content h4 {
            font-size: 0.9rem;
            margin-bottom: 0.2rem;
        }

        .notification-content p {
            font-size: 0.7rem;
            color: #555;
        }

        .notification-time {
            font-size: 0.7rem;
            color: #555;
        }

        /* Bottom Navigation */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: #fff;
            box-shadow: 0 -2px 5px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-around;
            padding:
