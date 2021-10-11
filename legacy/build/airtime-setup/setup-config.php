<?php
?>

<html>
    <head>
        <script type="text/javascript" src="js/libs/jquery-1.8.3.min.js"></script>
        <script type="text/javascript" src="js/libs/jquery-ui-1.8.24.min.js"></script>
        <script type="text/javascript" src="js/setup/setup-config.js"></script>

        <link rel="stylesheet" type="text/css" href="css/bootstrap-3.3.1.min.css" />
        <link rel="stylesheet" type="text/css" href="css/setup/setup-config.css" />
    </head>
    <body>
        <div class="header">
            <h3 class="logo">
                <img src="css/images/libretime_logo_jp.png" id="LibreTimeLogo" /><br/>
                <strong>Setup</strong>
            </h3>
            <strong>Step <span id="stepCount">1</span> of 5</strong>
        </div>

        <div class="viewport">
            <div class="form-slider">
                <div id="databaseSettings" class="form-wrapper">
                    <?php
                        require_once SETUP_PATH . "forms/database-settings.php";
                    ?>
                </div>
                <div id="rabbitmqSettings" class="form-wrapper">
                    <?php
                        require_once SETUP_PATH . "forms/rabbitmq-settings.php";
                    ?>
                </div>
                <div id="generalSettings" class="form-wrapper">
                    <?php
                        require_once SETUP_PATH . "forms/general-settings.php";
                    ?>
                </div>
                <div id="mediaSettings" class="form-wrapper">
                    <?php
                        require_once SETUP_PATH . "forms/media-settings.php";
                    ?>
                </div>
                <div id="finishSettings" class="form-wrapper">
                    <?php
                        require_once SETUP_PATH . "forms/finish-settings.php";
                    ?>
                </div>
            </div>
        </div>

        <script>
            $(".btn-skip").click(nextSlide);
            $(".btn-back").click(prevSlide);
        </script>
    </body>
</html>