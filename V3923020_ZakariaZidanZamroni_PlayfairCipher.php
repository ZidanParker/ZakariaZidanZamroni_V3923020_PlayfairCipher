<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Playfair Cipher</title>
</head>
<body>
    <h1>Playfair Cipher</h1>

    <form method="POST" action="">
        <label for="plaintext">Plaintext / Ciphertext:</label><br>
        <input type="text" id="plaintext" name="plaintext" required><br><br>

        <label for="keyword">Keyword:</label><br>
        <input type="text" id="keyword" name="keyword" required><br><br>

        <button type="submit" name="action" value="encrypt">Enkripsi</button>
        <button type="submit" name="action" value="decrypt">Dekripsi</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Fungsi untuk memformat teks
        function prepareText($text) {
            $text = strtoupper(str_replace(' ', '', $text));
            $text = str_replace('J', 'I', $text); // 'J' digantikan oleh 'I'
            $preparedText = '';
            for ($i = 0; $i < strlen($text); $i += 2) {
                $first = $text[$i];
                $second = ($i + 1 < strlen($text)) ? $text[$i + 1] : 'X';

                if ($first == $second) {
                    $second = 'X';
                    $i--;
                }

                $preparedText .= $first . $second;
            }

            if (strlen($preparedText) % 2 != 0) {
                $preparedText .= 'X';
            }

            return $preparedText;
        }

        // Fungsi untuk membangun matriks Playfair
        function buildMatrix($keyword) {
            $alphabet = 'ABCDEFGHIKLMNOPQRSTUVWXYZ';
            $matrix = [];
            $usedChars = [];

            $keyword = strtoupper(str_replace('J', 'I', $keyword));
            foreach (str_split($keyword) as $char) {
                if (!in_array($char, $usedChars)) {
                    $matrix[] = $char;
                    $usedChars[] = $char;
                }
            }

            foreach (str_split($alphabet) as $char) {
                if (!in_array($char, $usedChars)) {
                    $matrix[] = $char;
                    $usedChars[] = $char;
                }
            }

            return array_chunk($matrix, 5);
        }

        // Fungsi untuk mencari posisi karakter di dalam matriks
        function getPosition($matrix, $char) {
            for ($i = 0; $i < 5; $i++) {
                for ($j = 0; $j < 5; $j++) {
                    if ($matrix[$i][$j] == $char) {
                        return [$i, $j];
                    }
                }
            }
            return null;
        }

        // Fungsi enkripsi Playfair
        function encrypt($text, $matrix) {
            $text = prepareText($text);
            $cipherText = '';

            for ($i = 0; $i < strlen($text); $i += 2) {
                $firstChar = $text[$i];
                $secondChar = $text[$i + 1];

                list($row1, $col1) = getPosition($matrix, $firstChar);
                list($row2, $col2) = getPosition($matrix, $secondChar);

                if ($row1 == $row2) {
                    $cipherText .= $matrix[$row1][($col1 + 1) % 5] . $matrix[$row2][($col2 + 1) % 5];
                } elseif ($col1 == $col2) {
                    $cipherText .= $matrix[($row1 + 1) % 5][$col1] . $matrix[($row2 + 1) % 5][$col2];
                } else {
                    $cipherText .= $matrix[$row1][$col2] . $matrix[$row2][$col1];
                }
            }

            return $cipherText;
        }

        // Fungsi dekripsi Playfair
        function decrypt($cipherText, $matrix) {
            $plainText = '';

            for ($i = 0; $i < strlen($cipherText); $i += 2) {
                $firstChar = $cipherText[$i];
                $secondChar = $cipherText[$i + 1];

                list($row1, $col1) = getPosition($matrix, $firstChar);
                list($row2, $col2) = getPosition($matrix, $secondChar);

                if ($row1 == $row2) {
                    $plainText .= $matrix[$row1][($col1 + 4) % 5] . $matrix[$row2][($col2 + 4) % 5];
                } elseif ($col1 == $col2) {
                    $plainText .= $matrix[($row1 + 4) % 5][$col1] . $matrix[($row2 + 4) % 5][$col2];
                } else {
                    $plainText .= $matrix[$row1][$col2] . $matrix[$row2][$col1];
                }
            }

            return $plainText;
        }

        // Mengambil input dari form
        $plaintext = $_POST['plaintext'];
        $keyword = $_POST['keyword'];
        $action = $_POST['action']; // Mendapatkan nilai dari tombol yang diklik (enkripsi atau dekripsi)

        echo "<h2>Results:</h2>";
        echo "Input Text: $plaintext<br>";
        echo "Keyword: $keyword<br>";

        // Siapkan matriks Playfair
        $matrix = buildMatrix($keyword);

        // Proses sesuai dengan tombol yang ditekan
        if ($action == 'encrypt') {
            $ciphertext = encrypt($plaintext, $matrix);
            echo "Ciphertext: $ciphertext<br>";
        } elseif ($action == 'decrypt') {
            $decryptedText = decrypt($plaintext, $matrix);
            echo "Decrypted Text: $decryptedText<br>";
        }
    }
    ?>
</body>
</html>
