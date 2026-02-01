<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard - Laravel + Vue</title>
    @vite(['laravel/resources/css/app.css', 'laravel/resources/js/app.js'])

    <!-- StarAdmin2 theme (served from public/admin-theme) -->
    <link rel="stylesheet" href="/admin-theme/assets/vendors/feather/feather.css">
    <link rel="stylesheet" href="/admin-theme/assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="/admin-theme/assets/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="/admin-theme/assets/vendors/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="/admin-theme/assets/vendors/typicons/typicons.css">
    <link rel="stylesheet" href="/admin-theme/assets/vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="/admin-theme/assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="/admin-theme/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="/admin-theme/assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css">
    <link rel="stylesheet" type="text/css" href="/admin-theme/assets/js/select.dataTables.min.css">
    <link rel="stylesheet" href="/admin-theme/assets/css/style.css">
    <link rel="shortcut icon" href="/admin-theme/assets/images/favicon.png" />

    <!-- StarAdmin2 scripts (defer so Vue mounts first) -->
    <script defer src="/admin-theme/assets/vendors/js/vendor.bundle.base.js"></script>
    <script defer src="/admin-theme/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <script defer src="/admin-theme/assets/vendors/chart.js/chart.umd.js"></script>
    <script defer src="/admin-theme/assets/vendors/progressbar.js/progressbar.min.js"></script>
    <script defer src="/admin-theme/assets/js/off-canvas.js"></script>
    <script defer src="/admin-theme/assets/js/template.js"></script>
    <script defer src="/admin-theme/assets/js/settings.js"></script>
    <script defer src="/admin-theme/assets/js/hoverable-collapse.js"></script>
    <script defer src="/admin-theme/assets/js/todolist.js"></script>
    <script defer src="/admin-theme/assets/js/jquery.cookie.js" type="text/javascript"></script>
</head>
<body class="with-welcome-text">
    <div id="app"></div>
</body>
</html>
