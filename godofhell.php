<?php
// Process rewards
$totalPoints = 0;
$firstRun = true;

while (true) {
    clearScreen();
    printBanner();

    if (!$firstRun) {
        foreach ($users as $userId => $userData) {
            echo "\n";
            printGreen("---> $userId +{$userData['points']} PX\n");
        }
        echo "\n";
        printGreen("Total PX Earned [ +$totalPoints ]\n\n");
    }

    foreach ($users as $userId => $userData) {
        $tgId = $userData['tg_id'];

        printGreen("[ INFO ] Starting NOT PIXEL Engine\n");
        printGreen("[ PROCESS ] Injecting V1 ---> TG ID | $userId ...\n");

        sleep(3);

        // Make API request for the user
        list($response, $httpCode, $reqHeaders) = makeApiRequest($userId, $tgId);

        if ($httpCode === 200) {
            $reward = extractReward($response);
            if ($reward) {
                $users[$userId]['points'] += $reward;
                $totalPoints += $reward;
                printGreen("[ SUCCESS ] ++ $userId +$reward PX\n");
            } else {
                printGreen("[ ERROR ] Ads watching limit reached.\n");
                printGreen("[ SOLUTION ] Try VPN or wait for 24 hours.\n");
                continue;
            }
        } elseif ($httpCode === 403) {
            printGreen("[ ERROR ] Seems like your IP address is banned\n");
            printGreen("[ SOLUTION ] Use VPN.\n");
            exit;
        } else {
            if ($httpCode === 400 && strpos($response, 'block_error') !== false) {
                printGreen("[ ERROR ] Ads Block error - Ignore it.\n");
                continue;
            }
            printGreen("[ ERROR ] HTTP Error: $httpCode\n");
            continue;
        }
    }

    // Inject reward points for each user (now using a valid API request)
    foreach ($users as $userId => $userData) {
        $reward = 16;  // Fixed reward value

        printGreen("[ PROCESS ] Injecting V2 ---> $userId\n");

        // Make API request for reward injection
        list($response, $httpCode, $reqHeaders) = makeApiRequest($userId, $userData['tg_id']);

        $url = "https://api.adsgram.ai/inject/$userId"; // Replace with actual API URL for injecting rewards

        // Perform the request to inject the reward
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);  // Correct API endpoint for injection
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $reqHeaders); // Use the correct headers
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            $totalPoints += $reward;
            $users[$userId]['points'] += $reward;
            printGreen("[ SUCCESS ] ++ $userId +$reward PX\n");
        } else {
            printGreen("[ ERROR ] Failed to inject for $userId. HTTP Code: $httpCode\n");
        }
    }

    file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
    $firstRun = false;
}
?>
