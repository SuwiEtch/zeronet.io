<?php
if (isset($_GET["url"])) {
  $locale = preg_replace("#[^a-z-]#", "", $_GET["url"]);
  if (!is_file("languages/$locale.json") and $locale != "en") {
    header("HTTP/1.0 404 Not Found");
    echo("<h1>Not found</h1>");
    exit();
  }
} else {
  $locale = $_SERVER["HTTP_ACCEPT_LANGUAGE"];
}
$locale = strtolower($locale);
$locale_array = explode(",",$locale);
foreach ($locale_array as $locale) {
  $locale = preg_replace("#;.*#", "", $locale);
  $locale = preg_replace("#[^a-z-]#", "", $locale);
  $lang = preg_replace("#-.*#", "", $locale);
  $lang_file = "languages/$locale.json";
  if ($lang == "en" or is_file($lang_file)) break;
}
if (!$locale || !is_file($lang_file)) $locale = "en";

if ($_SERVER["HTTP_HOST"] == "go.zeronet.io") {
  $body = file_get_contents("go.html");
} else {
  $body = file_get_contents("index.html");
}

$body = str_replace("{locale}", $locale, $body);
$body = str_replace('<link rel="stylesheet" href="media/main.css" />', "<style>\n" . file_get_contents("media/main.css") . "\n</style>", $body);

if ($lang != "en" && $lang != "") {
  $translates = json_decode(file_get_contents($lang_file));
  foreach ($translates as $original => $translated) {
    $body = str_replace(">$original<", ">$translated<", $body);
  }
}
echo($body);
?>
