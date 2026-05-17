<!DOCTYPE html>
<html lang="de">

<head>

    <meta charset="UTF-8">

    <title>Smart Task Manager</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <style>

        body{
            background:#f5f6fa;
            transition:.3s;
        }

        .sidebar{
            width:250px;
            height:100vh;
            background:#212529;
            position:fixed;
            left:0;
            top:0;
            padding-top:20px;
        }

        .sidebar a{
            display:block;
            color:white;
            padding:15px 20px;
            text-decoration:none;
        }

        .sidebar a:hover{
            background:#343a40;
        }

        .content{
            margin-left:250px;
            padding:30px;
        }

        /* Dark Mode */

        .dark-mode{
            background:#121212;
            color:white;
        }

        .dark-mode .card{
            background:#1f1f1f;
            color:white;
        }

        .dark-mode .text-muted{
            color:#bdbdbd !important;
        }

        .dark-mode .list-group-item{
            background:#1f1f1f;
            color:white;
            border-color:#333;
        }

        .dark-mode .form-control,
        .dark-mode .form-select{
            background:#2c2c2c;
            color:white;
            border-color:#444;
        }

        .dark-mode .navbar{
            background:black !important;
        }

    </style>

</head>

<body>

<nav class="navbar navbar-dark bg-dark px-4">

    <span class="navbar-brand mb-0 h1">
        Smart Task Manager 🚀
    </span>

    <div>

        <button
            class="btn btn-secondary me-2"
            onclick="toggleDarkMode()">

            🌙 Dark Mode

        </button>

        <a href="../logout.php"
           class="btn btn-danger">

            Logout

        </a>

    </div>

</nav>

<script>

if(localStorage.getItem("darkMode")==="enabled"){

    document.body.classList.add("dark-mode");

}

function toggleDarkMode(){

    document.body.classList.toggle("dark-mode");

    if(document.body.classList.contains("dark-mode")){

        localStorage.setItem(
            "darkMode",
            "enabled"
        );

    }else{

        localStorage.setItem(
            "darkMode",
            "disabled"
        );

    }
}

</script>