<?php
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Ошибка</title>
</head>

<body>
    <div style="display:flex;flex-direction:column;align-items:center;justify-content:center">
        <h1>
            Ошибка 404!
        </h1>
        <p>
            <?php echo $_VARS['message'] ?>
        </p>
    </div>
</body>

</html>