<!doctype html>
<html lang="en">
<head>

    <!-- ----- Meta ----- -->
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <meta id="token" name="token" value="<?php echo csrf_token() ?>">
    <meta id="api_token" name="api_token" value="<?php echo \Auth::user()->api_token ?>">
    <meta id="git_revision" name="git_revision" value="<?php echo config('app.git_rev') ?>">
    <meta id="version" name="version" value="<?php echo config('app.version') ?>">
    <meta id="env" name="env" value="<?php echo \App::environment() ?>">
    <meta id="project_name" name="project_name" value="VPN">

    <title>VPN</title>
    <!-- ----- End of Meta ----- -->

    <!-- ----- Styles ----- -->
    <link rel="stylesheet" href="<?php echo App\Helpers\Assets::path('common.css') ?>">
    <!-- ----- End of Styles ----- -->

</head>
<body id="app">

<!-- ----- Scripts ----- -->
<script src="<?php echo App\Helpers\Assets::path('common.js') ?>"></script>
<script src="<?php echo App\Helpers\Assets::path("$role.js") ?>"></script>
<!-- ----- End of Scripts ----- -->

</body>
</html>
