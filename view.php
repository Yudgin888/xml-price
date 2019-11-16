<?php
include_once 'templ/header.php';
require_once 'functions.php';
$price = getPrice();

if (count($price) > 0):?>
    <h2>Прайс (записей: <?= count($price) ?>)</h2>
    <table class="table table-striped">
        <tr>
            <th>#</th>
            <th>Производитель</th>
            <th>Модель</th>
            <th>Цена</th>
        </tr>
        <?php $i = 1;
        foreach ($price as $item): ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= $item['vendor'] ?></td>
                <td><?= $item['product'] ?></td>
                <td><?= $item['price'] ?> <?= $item['currency'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <h2>Прайс пуст!</h2>
    <a href="index.php">Загрузить прайс</a>
<?php endif;
include_once 'templ/footer.php';