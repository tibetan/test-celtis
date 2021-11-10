<!doctype html>
<html lang="en">
<head>
    <?= processTemplate('template/shared/head.html.php') ?>
</head>

<body>
<div class="picker-block">
    <label for="picker-date">Моля, изберете дата:</label>
    <input type="date" id="picker-date" name="picker-date" value="" required>
    <input type="button" value="Изпращане" onClick="handleClick()">
</div>

<div id="dataBlock"></div>

<script src='js/index.js'></script>
</body>
</html>
