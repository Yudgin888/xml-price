<?php
require_once 'config.php';
require_once 'DB_MySql.php';

global $db;
$db = new DB_MySql(DB_HOST, DB_USER, DB_PASS, DB_NAME);

function parseXML($filename)
{
    $xml = new DOMDocument();
    if (@$xml->load($filename)) {
        $root = $xml->documentElement;
        $items = $root->getElementsByTagName('item');
        $i = 0;
        foreach ($items as $item) {
            $vendor = $item->getElementsByTagName('vendor')->item(0)->nodeValue;
            $model = $item->getElementsByTagName('model')->item(0)->nodeValue;
            $price = floatval($item->getElementsByTagName('price')->item(0)->nodeValue);
            $currency = $item->getElementsByTagName('currency')->item(0)->nodeValue;
            if(add($vendor, $model, $price, $currency)) {
                $i++;
            }
        }
        return $i;
    } else {
        return false;
    }
}

function add($vendor, $model, $price, $currency){
    global $db;
    $res = $db->prepare_execute("SELECT id FROM vendor WHERE `name` = ? LIMIT 1;", 's', [$vendor]);
    $id_vendor = null;
    if($res[0]['id']){
        $id_vendor = $res[0]['id'];
    } else {
        if($db->prepare_execute("INSERT INTO vendor (`name`) VALUES (?);", 's', [$vendor])){
            $id_vendor = $db->get_insert_id();
        }
    }
    if(!$id_vendor) {
        return false;
    }

    $res = $db->prepare_execute("SELECT id FROM product WHERE `id_vendor` = ? AND `name` = ? LIMIT 1;", 'ss', [$id_vendor, $model]);
    $id_model = null;
    if($res[0]['id']){
        $id_model = $res[0]['id'];
    } else {
        if($db->prepare_execute("INSERT INTO product (`id_vendor`, `name`) VALUES (?, ?);", 'ss', [$id_vendor, $model])){
            $id_model = $db->get_insert_id();
        }
    }
    if(!$id_model) {
        return false;
    }

    $res = $db->prepare_execute("SELECT id FROM price WHERE `id_product` = ? LIMIT 1;", 's', [$id_model]);
    $id_price = null;
    if($res[0]['id']){
        $id_price = $res[0]['id'];
        return $db->prepare_execute("UPDATE price SET `price` = ?, `currency` = ? WHERE `id` = ?;", 'dss', [$price, $currency, $id_price]);
    } else {
        return $db->prepare_execute("INSERT INTO price (`id_product`, `price`, `currency`) VALUES (?, ?, ?);", 'sds', [$id_model, $price, $currency]);
    }
}

function getPrice() {
    global $db;
    $sql = "SELECT v.name AS vendor, pr.name AS product, pc.price AS price, pc.currency AS currency 
            FROM vendor v LEFT JOIN product pr ON v.id = pr.id_vendor 
						  LEFT JOIN price pc ON pr.id = pc.id_product;";
    return $db->execute($sql);
}