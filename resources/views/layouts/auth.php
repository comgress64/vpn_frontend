<!doctype html>
<html lang="en">
<head>

    <!-- ----- Meta ----- -->
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <meta id="token" name="token" value="<?php echo csrf_token() ?>">

    <title>VPN</title>
    <!-- ----- End of Meta ----- -->

    <!-- ----- Styles ----- -->
    <link rel="stylesheet" href="<?php echo App\Helpers\Assets::path('common.css') ?>">
    <!-- ----- End of Styles ----- -->

</head>
<body id="app">

<!-- ----- Scripts ----- -->
<script src="<?php echo App\Helpers\Assets::path('common.js') ?>"></script>
<script src="<?php echo App\Helpers\Assets::path('auth.js') ?>"></script>
<!-- ----- End of Scripts ----- -->

</body>
</html>
