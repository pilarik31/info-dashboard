<?php
require 'vendor/autoload.php';

use Pila\Dashboard\WeatherApi;

$city = filter_input(INPUT_GET, 'city', FILTER_SANITIZE_STRING);
if ($city === null) {
    $city = 'Prague';
}
$selected = !empty($city) ? $city : '';

$config = json_decode(
    file_get_contents('config.json'),
    true,
);
$weather = new WeatherApi($config['weatherApiKey'], $city);

?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="60">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/app.css">
</head>
<body>
    <div class="wrapper">
        <h1 class="text-center time"> Aktuálně je - <span id="time-now"></span></h1>

        <div class="text-center select-center">
            <select id="city" name="city" class="select" onchange="location = this.value;">
                <option value="/?city=Prague" <?php echo $selected == 'Prague' ? 'selected' : '' ?> >Praha</option>
                <option value="/?city=Kolin" <?php echo $selected == 'Kolin' ? 'selected' : '' ?> >Kolín</option>
                <option 
                    value="/?city=Kutna Hora"
                    <?php echo $selected == 'Kutna Hora' ? 'selected' : '' ?>
                >
                    Kutná Hora
                </option>
            </select>
        </div>

        <div class="col">
            <div class="weather">
                <h1>Počasí</h1>
                <h2>
                    v oblasti 
                    <?= $weather->city() ?>, 
                    <?= $weather->region() ?>, 
                    <?= $weather->country() ?>
                </h2>
                <div class="col">
                    <p>
                        Teplota: <?= $weather->temp() ?> &deg;
                    </p>
                    <p>
                        <?= $weather->condition() ?>
                    </p>
                    <img src="<?= $weather->conditionIcon() ?>">
                    
                </div>
                <sub>Naposledy aktualizováno: <?= $weather->lastUpdated() ?>; TTL: 1 hodina</sub>
            </div>
        </div>
    </div>


    <script src="js/Time.js"></script>

</body>
</html>