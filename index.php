<?php
require_once 'functions.php';
$errors = [];
$mess = [];
if (!empty($_FILES)) {
    if (empty($_FILES["price-upload"]) || $_FILES["price-upload"]['type'] != 'text/xml') {
        $errors[] = 'Выбран некорректный файл!';
    }
    $file = $_FILES["price-upload"];
    if (count($errors) == 0) {
        if ($res = parseXML($file["tmp_name"])) {
            $mess[] = 'Обработано элементов: ' . $res;
        } else {
            $errors[] = 'Ошибка обработки файла!';
        }
    }
}
include_once 'templ/header.php';
?>
    <form action="/" method="post" enctype="multipart/form-data">
        <div>
            <input type="file" name="price-upload" accept="text/xml">
            <input type="submit" value="Отправить">
        </div>

        <div style="color: green">
            <?php foreach ($mess as $item): ?>
                <p><?= $item ?></p>
            <?php endforeach; ?>
        </div>

        <div style="color: red">
            <?php foreach ($errors as $err): ?>
                <p><?= $err ?></p>
            <?php endforeach; ?>
        </div>

        <?php if ($res): ?>
            <a href="view.php">Прайс</a>
        <?php endif; ?>
    </form>
<?php
include_once 'templ/footer.php';
die;