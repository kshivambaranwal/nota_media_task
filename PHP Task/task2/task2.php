<?php
require '../connection.php';

// function for fetch url dom
function fechcontentfromurl($url) {
    $html = file_get_contents($url);
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    return $dom;
}

// function for prepaire connection and query
function preparequerystatement($connection, $sql) {
    return $connection->prepare($sql);
}

// check dublicate entry function
function checkDuplicateEntry($exists, $url, $picture, $abstract) {
    $exists->bind_param("sss", $url, $picture, $abstract);
    $exists->execute();
    $exists->store_result();
    return $exists->num_rows > 0;
}

// function for insert recorde
function insertRecord($insertStatement, $title, $url, $picture, $abstract) {
    $insertStatement->bind_param("ssss", $title, $url, $picture, $abstract);
    return $insertStatement->execute();
}

$url = 'https://en.wikipedia.org/wiki/Merchant%27s_House_Museum';
$dom = fechcontentfromurl($url);

$xpath = new DOMXPath($dom);

$data = [
    'headings' => $xpath->query('//h2/span'),
    'abstracts' => $xpath->query('//p[@class="description"]'),
    'pictures' => $xpath->query('//img/@src'),
    'links' => $xpath->query('//a/@href'),
];

$insertStatement = preparequerystatement($connection, "INSERT INTO wiki_sections (title, url, picture, abstract, date_created) VALUES (?, ?, ?, ?, NOW())");
$exists = preparequerystatement($connection, "SELECT id FROM wiki_sections WHERE url = ? OR picture = ? OR abstract = ?");

foreach ($data['headings'] as $index => $heading) {
    $title = $heading->nodeValue;
    $url = isset($data['links'][$index]) ? $data['links'][$index]->nodeValue : '';
    $picture = isset($data['pictures'][$index]) ? $data['pictures'][$index]->nodeValue : '';
    $abstract = isset($data['abstracts'][$index]) ? $data['abstracts'][$index]->nodeValue : md5(uniqid());

    // Check if the abstract is empty before inserting
    if (!empty($abstract)) {
        if (checkDuplicateEntry($exists, $url, $picture, $abstract)) {
            echo "Skipped existing record.<br>";
        } else {
            if (insertRecord($insertStatement, $title, $url, $picture, $abstract)) {
                echo "Data inserted successfully.<br>";
            } else {
                echo "Error: " . $insertStatement->error . "<br>";
            }
        }
    } else {
        echo "Skipped empty abstract.<br>";
    }
}

$exists->close();
$insertStatement->close();
$connection->close();
?>
