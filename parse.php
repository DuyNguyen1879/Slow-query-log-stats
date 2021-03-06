<?php
use Gumbercules\MysqlSlow\LogParser;

ini_set("memory_limit", "4G");

require_once("vendor/autoload.php");

if (!isset($argv[1])) {
	exit("Please specify a log file to parse");
}

if (!file_exists($argv[1])) {
	exit("Specified log file {$argv[1]} does not exist");
}

if (isset($argv[2])) {
    $user = $argv[2];
}

$parser = new LogParser(file_get_contents($argv[1]));

$entries = array_filter($parser->parseEntries(), function ($entry) use ($user) {
	if (!$user) {
		return true;
	}
	return $entry->getUser() == $user;
});
unset($parser);

$sortedByDate = [];

$totalTime = 0;
$numQueries = count($entries);

foreach ($entries as $entry) {

	$date = $entry->getDatetime()->format("D jS M Y");

	if (!isset($sortedByDate[$date])) {
		$sortedByDate[$date]["numQueries"] = 0;
        $sortedByDate[$date]["totalTime"] = 0;
	}

	$sortedByDate[$date]["numQueries"]++;

    $queryTime = $entry->getQueryTime();
    $sortedByDate[$date]["totalTime"] += $queryTime;
    $totalTime += $queryTime;
}

echo "\n";

echo number_format($numQueries) . " queries\n";

if ($numQueries > 0) {
    echo "Average query time: " . round($totalTime / $numQueries, 4) . "ms\n";
}

echo "\n";

foreach ($sortedByDate as $date => $stats) {
	$stats["numQueries"] = number_format($stats["numQueries"]);
	echo "{$date}: {$stats["numQueries"]} slow queries\n";

    if ($stats["numQueries"] > 0) {
        echo "Average query time: " . round($stats["totalTime"] / $stats["numQueries"], 4) . "ms\n";
    }

    echo "\n";
}
