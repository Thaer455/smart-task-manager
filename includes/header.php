<!DOCTYPE html>
<html lang="de">

<head>

    <meta charset="UTF-8">

    <title>Smart Task Manager</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <style>

        body {
            background: #f5f6fa;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            background: #212529;
            position: fixed;
            left: 0;
            top: 0;
            padding-top: 20px;
        }

        .sidebar a {
            display: block;
            color: #fff;
            padding: 15px 20px;
            text-decoration: none;
        }

        .sidebar a:hover {
            background: #343a40;
        }

        .content {
            margin-left: 250px;
            padding: 30px;
        }

    </style>

</head>

<body>

<nav class="navbar navbar-dark bg-dark px-4">

    <span class="navbar-brand mb-0 h1">
        Smart Task Manager 🚀
    </span>

    <a href="../logout.php" class="btn btn-danger">
        Logout
    </a>

</nav>