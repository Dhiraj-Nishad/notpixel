<?php
// Clear screen function
function clearScreen() {
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        system('cls');
    } else {
        system('clear');
    }
}

// Print colored message
function printGreen($message) {
    echo "\033[1;32m$message\033[0m\n";
}

// Extract ID from referral link
function extractReferralId($link) {
    if (preg_match('/startapp=f(\d+)/', $link, $matches)) {
        return $matches[1];
    }
    return false;
}

// Function to generate random user agent
function generateUserAgent() {
    $os = ['Windows', 'Linux', 'iOS', 'Android'];
    $versions = ['8', '9', '10', '11', '12', '13', '14'];
    $devices = ['Samsung', 'Motorola', 'Xiaomi', 'Huawei', 'OnePlus'];
    
    $selectedOs = $os[array_rand($os)];
    
    if ($selectedOs === 'Android') {
        $version = $versions[array_rand($versions)];
        $device = $devices[array_rand($devices)];
        $userAgent = "Mozilla/5.0 (Linux; Android $version; $device) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.90 Mobile Safari/537.36";
    } else {
        $userAgent = "Mozilla/5.0 ($selectedOs NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.90 Safari/537.36";
    }
    
    return $userAgent . rand(1000000, 9999999);
}

// Function to print banner
function printBanner() {
    $banner = "
-------------------------------------------------
██ ███████ ███████ ████████
██ ██   ██ ██   ██ ██    ██
██ ███████ ███████ ██    ██
██ ██   ██ ██   ██ ██    ██
██ ███████ ██   ██ ████████
-------------------------------------------------

     - NOT PIXEL AD WATCH - 
     - VERSION 2.0 -
    
- MADE BY: God of Hell
- Telegram: @GodofHell 
- channel: https://t.me/NotPixelApp 

- Note: If you encounter the issue \"URL not found\" kindly ignore it.
-------------------------------------------------

";
    echo printGreen($banner);
}

// Check for users.json file
$usersFile = 'users.json';
$users = file_exists($usersFile) ? json_decode(file_get_contents($usersFile), true) : [];

clearScreen();

// Print welcome messages
printGreen("Welcome to the Not Pixel AD Watcher!");
printGreen(". Copy your Not Pixel referral link.");
printGreen(". Multiple accounts supported.");

// Save referral link and user ID
while (true) {
    printGreen("Please paste your Not Pixel referral link:");
    $referralLink = trim(fgets(STDIN));
    
    $userId = extractReferralId($referralLink);
    
    if (!$userId) {
        printGreen("Error: Invalid Not Pixel referral link! Please try again.");
        continue;
    }
    
    if (isset($users[$userId])) {
        printGreen("Error: ID already saved!");
        $userData = $users[$userId];
        printGreen("User ID: {$userId}\nSaved At: {$userData['saved_at']}");
        continue;
    }
    
    $users[$userId] = [
        'tg_id' => $userId,
        'points' => 0,
        'saved_at' => date('Y-m-d H:i:s')
    ];
    
    file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
    printGreen("Success: ID saved!");
    
    printGreen("Do you want to save more referral links? (y/n):");
    $continue = strtolower(trim(fgets(STDIN)));
    
    if ($continue !== 'y') {
        break;
    }
}

printGreen("\nSaved IDs:");
echo json_encode($users, JSON_PRETTY_PRINT) . "\n";

// Function to generate chat instance
function generateChatInstance() {
    return strval(rand(10000000000000, 99999999999999));
}

// Function to make API request
function makeApiRequest($userId, $tgId) {
    $url = "https://api.adsgram.ai/adv?blockId=4853&tg_id=$tgId&tg_platform=android&platform=Linux%20aarch64&language=en&chat_type=sender&chat_instance=" . generateChatInstance() . "&top_domain=app.notpx.app";
    
    $userAgent = generateUserAgent();
    $baseUrl = "https://app.notpx.app/";
    
    $headers = [
        'Host: api.adsgram.ai',
        'Connection: keep-alive', 
        'Cache-Control: max-age=0',
        'sec-ch-ua-platform: "Android"',
        "User-Agent: $userAgent",
        'sec-ch-ua: "Android WebView";v="131", "Chromium";v="131", "Not_A Brand";v="24"',
        'sec-ch-ua-mobile: ?1',
        'Accept: */*',
        'Origin: https://app.notpx.app',
        'X-Requested-With: org.telegram.messenger',
        'Sec-Fetch-Site: cross-site',
        'Sec-Fetch-Mode: cors',
        'Sec-Fetch-Dest: empty',
        "Referer: $baseUrl",
        'Accept-Encoding: gzip, deflate, br, zstd',
        'Accept-Language: en,en-US;q=0.9'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [$response, $httpCode, $headers];
}

// Function to extract reward value
function extractReward($response) {
    $data = json_decode($response, true);
    if ($data && isset($data['banner']['trackings'])) {
        foreach ($data['banner']['trackings'] as $tracking) {
            if ($tracking['name'] === 'reward') {
                return $tracking['value'];
            }
        }
    }
    return null;
}

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

    for ($i = 20; $i > 0; $i--) {
        echo "\r-----> Cooldown $i seconds left...";
        sleep(1);
    }
    echo "\n";

    foreach ($rewards as $userId => $reward) {
        printGreen("[ PROCESS ] Injecting V2 ---> $userId ]\n");
        
        $reqHeaders = $headers[$userId];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $reward);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $reqHeaders);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            $totalPoints += 16;
            $users[$userId]['points'] += 16;
            printGreen("[ SUCCESS ] ++ $userId +16 PX\n");
        } else {
            printGreen("[ ERROR ] Failed to inject for $userId. HTTP Code: $httpCode\n");
        }
    }

    file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
    $firstRun = false;
}
?>
