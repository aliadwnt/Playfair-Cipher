<?php

function generateKeySquare($key)
{
    $key = str_replace(' ', '', $key);
    $key = strtoupper($key);
    $keySquare = array();
    $alphabet = 'ABCDEFGHIKLMNOPQRSTUVWXYZ';

    $keyLength = strlen($key);
    $keyChars = str_split($key);

    $index = 0;

    // Fill in the key
    for ($row = 0; $row < 5; $row++) {
        for ($col = 0; $col < 5; $col++) {
            if ($index < $keyLength) {
                $currentChar = $keyChars[$index];
                $index++;
            } else {
                // Fill in the remaining alphabet
                while (in_array($alphabet[$index], $keyChars)) {
                    $index++;
                }
                $currentChar = $alphabet[$index];
                $index++;
            }
            $keySquare[$row][$col] = $currentChar;
        }
    }

    return $keySquare;
}

function getIndex($char, $keySquare)
{
    foreach ($keySquare as $row => $colArray) {
        foreach ($colArray as $col => $value) {
            if ($value == $char) {
                return array($row, $col);
            }
        }
    }
}

function playfairEncrypt($plaintext, $keySquare)
{
    $ciphertext = '';
    $plaintext = strtoupper(str_replace(' ', '', $plaintext));

    $pairs = str_split($plaintext, 2);

    foreach ($pairs as $pair) {
        $char1 = $pair[0];
        $char2 = $pair[1];

        $char1Index = getIndex($char1, $keySquare);
        $char2Index = getIndex($char2, $keySquare);

        if ($char1Index[0] == $char2Index[0]) {
            // Same row
            $ciphertext .= $keySquare[$char1Index[0]][($char1Index[1] + 1) % 5];
            $ciphertext .= $keySquare[$char2Index[0]][($char2Index[1] + 1) % 5];
        } elseif ($char1Index[1] == $char2Index[1]) {
            // Same column
            $ciphertext .= $keySquare[($char1Index[0] + 1) % 5][$char1Index[1]];
            $ciphertext .= $keySquare[($char2Index[0] + 1) % 5][$char2Index[1]];
        } else {
            // Different row and column
            $ciphertext .= $keySquare[$char1Index[0]][$char2Index[1]];
            $ciphertext .= $keySquare[$char2Index[0]][$char1Index[1]];
        }
    }

    return $ciphertext;
}

function playfairDecrypt($ciphertext, $keySquare)
{
    $plaintext = '';
    $ciphertext = strtoupper(str_replace(' ', '', $ciphertext));

    $pairs = str_split($ciphertext, 2);

    foreach ($pairs as $pair) {
        $char1 = $pair[0];
        $char2 = $pair[1];

        $char1Index = getIndex($char1, $keySquare);
        $char2Index = getIndex($char2, $keySquare);

        if ($char1Index[0] == $char2Index[0]) {
            // Same row
            $plaintext .= $keySquare[$char1Index[0]][($char1Index[1] - 1 + 5) % 5];
            $plaintext .= $keySquare[$char2Index[0]][($char2Index[1] - 1 + 5) % 5];
        } elseif ($char1Index[1] == $char2Index[1]) {
            // Same column
            $plaintext .= $keySquare[($char1Index[0] - 1 + 5) % 5][$char1Index[1]];
            $plaintext .= $keySquare[($char2Index[0] - 1 + 5) % 5][$char2Index[1]];
        } else {
            // Different row and column
            $plaintext .= $keySquare[$char1Index[0]][$char2Index[1]];
            $plaintext .= $keySquare[$char2Index[0]][$char1Index[1]];
        }
    }

    return $plaintext;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $key = "alia dewanto";
    $plaintext = $_POST['plaintext'];
    $action = $_POST['action'];

    $keySquare = generateKeySquare($key);

    if ($action == 'encrypt') {
        $ciphertext = playfairEncrypt($plaintext, $keySquare);
        echo "Encrypted Text: " . $ciphertext;
    } elseif ($action == 'decrypt') {
        $decryptedText = playfairDecrypt($plaintext, $keySquare);
        echo "Decrypted Text: " . $decryptedText;
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Playfair Cipher</title>
</head>

<body>
    <h2>Playfair Cipher</h2>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="plaintext">Text:</label>
        <input type="text" name="plaintext" required>
        <br>
        <label for="action">Action:</label>
        <select name="action" required>
            <option value="encrypt">Encrypt</option>
            <option value="decrypt">Decrypt</option>
        </select>
        <br>
        <input type="submit" value="Submit">
    </form>
</body>

</html>
