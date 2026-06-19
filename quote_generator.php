<?php
// quote_generator.php - Генератор случайных цитат на PHP (CLI + веб)
// CLI: php quote_generator.php --random
// Веб: откройте как HTML

const DATA_FILE = 'quotes.json';
const FAV_FILE = 'favorites.json';

$defaultQuotes = [
    ['text' => 'Жизнь — это то, что происходит с вами, пока вы строите планы.', 'author' => 'Джон Леннон', 'category' => 'мудрость'],
    ['text' => 'Будьте тем изменением, которое хотите видеть в мире.', 'author' => 'Махатма Ганди', 'category' => 'мотивация'],
    ['text' => 'Единственный способ делать отличную работу — любить то, что вы делаете.', 'author' => 'Стив Джобс', 'category' => 'успех'],
    ['text' => 'Счастье — это не цель, а способ путешествия.', 'author' => 'Зигмунд Фрейд', 'category' => 'жизнь'],
    ['text' => 'Тот, кто не рискует, тот не пьет шампанское.', 'author' => 'Петр I', 'category' => 'юмор'],
    ['text' => 'Лучший способ предсказать будущее — создать его.', 'author' => 'Питер Друкер', 'category' => 'успех'],
    ['text' => 'Тьма не может изгнать тьму: только свет может сделать это.', 'author' => 'Мартин Лютер Кинг', 'category' => 'мудрость'],
    ['text' => 'Не судите о книге по обложке.', 'author' => 'Мигель де Сервантес', 'category' => 'мудрость'],
    ['text' => 'Все, что мы есть, — это результат того, что мы думали.', 'author' => 'Будда', 'category' => 'мудрость'],
    ['text' => 'Самый трудный шаг — первый.', 'author' => 'Китайская пословица', 'category' => 'мотивация'],
    ['text' => 'Не бойтесь делать то, что не умеете. Помните, ковчег построил любитель, профессионалы построили «Титаник».', 'author' => 'Дейв Барри', 'category' => 'юмор'],
    ['text' => 'Всё, что вы можете вообразить, реально.', 'author' => 'Пабло Пикассо', 'category' => 'мотивация'],
    ['text' => 'Секрет успеха — в искренности. Как только вы сможете притвориться искренним, успех придёт.', 'author' => 'Оскар Уайльд', 'category' => 'юмор'],
    ['text' => 'Человек — это то, что он читает.', 'author' => 'Джозеф Бродский', 'category' => 'мудрость'],
    ['text' => 'Чтобы дойти до цели, надо прежде всего идти.', 'author' => 'Оноре де Бальзак', 'category' => 'мотивация'],
    ['text' => 'Учитесь правилам, чтобы нарушать их правильно.', 'author' => 'Пабло Пикассо', 'category' => 'мудрость'],
    ['text' => 'Судьба — это не вопрос случая, а вопрос выбора.', 'author' => 'Уильям Дженнингс Брайан', 'category' => 'жизнь'],
    ['text' => 'Никогда не сдавайтесь, потому что это просто место и время, когда течение повернётся в вашу пользу.', 'author' => 'Гарриет Бичер-Стоу', 'category' => 'мотивация'],
    ['text' => 'Счастье — это не отсутствие проблем, а умение с ними справляться.', 'author' => 'Стив Мараболи', 'category' => 'жизнь'],
    ['text' => 'Ваше время ограничено, не тратьте его на чужую жизнь.', 'author' => 'Стив Джобс', 'category' => 'успех'],
];

function loadQuotes() {
    if (file_exists(DATA_FILE)) {
        $json = file_get_contents(DATA_FILE);
        $data = json_decode($json, true);
        if ($data) return $data;
    }
    global $defaultQuotes;
    saveQuotes($defaultQuotes);
    return $defaultQuotes;
}

function saveQuotes($quotes) {
    file_put_contents(DATA_FILE, json_encode($quotes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function loadFavorites() {
    if (file_exists(FAV_FILE)) {
        $json = file_get_contents(FAV_FILE);
        $data = json_decode($json, true);
        if ($data) return $data;
    }
    return [];
}

function saveFavorites($favs) {
    file_put_contents(FAV_FILE, json_encode($favs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function getRandomQuote($quotes) {
    if (empty($quotes)) return null;
    return $quotes[array_rand($quotes)];
}

function searchQuotes($quotes, $keyword) {
    $keyword = strtolower($keyword);
    return array_filter($quotes, function($q) use ($keyword) {
        return strpos(strtolower($q['text']), $keyword) !== false || strpos(strtolower($q['author']), $keyword) !== false;
    });
}

function addQuote(&$quotes, $text, $author, $category = 'общее') {
    $q = ['text' => $text, 'author' => $author, 'category' => $category];
    $quotes[] = $q;
    saveQuotes($quotes);
    return $q;
}

function exportQuote($quote, $filename = null) {
    if (!$filename) {
        $filename = 'quote_' . date('Ymd_His') . '.txt';
    }
    file_put_contents($filename, '"' . $quote['text'] . "\"\n— " . $quote['author'] . "\nКатегория: " . $quote['category']);
    return $filename;
}

function getCategories($quotes) {
    $cats = array_unique(array_column($quotes, 'category'));
    sort($cats);
    return $cats;
}

function filterByCategory($quotes, $category) {
    return array_filter($quotes, function($q) use ($category) { return $q['category'] == $category; });
}

function contains($arr, $item) {
    return in_array($item, $arr);
}

function getInput($prompt) {
    echo $prompt;
    return trim(fgets(STDIN));
}

if (php_sapi_name() === 'cli') {
    // CLI режим
    $options = getopt("", ["random", "search:", "add:", "author:", "category:", "export", "favorites"]);
    $quotes = loadQuotes();
    if (isset($options['random'])) {
        $q = getRandomQuote($quotes);
        if ($q) echo '"' . $q['text'] . '" — ' . $q['author'] . "\n";
    } elseif (isset($options['search'])) {
        $results = searchQuotes($quotes, $options['search']);
        foreach ($results as $q) {
            echo '"' . $q['text'] . '" — ' . $q['author'] . "\n";
        }
    } elseif (isset($options['add']) && isset($options['author'])) {
        $cat = $options['category'] ?? 'общее';
        $q = addQuote($quotes, $options['add'], $options['author'], $cat);
        echo 'Добавлено: "' . $q['text'] . '" — ' . $q['author'] . "\n";
    } elseif (isset($options['export'])) {
        $q = getRandomQuote($quotes);
        if ($q) {
            $fname = exportQuote($q);
            echo "Экспортировано в $fname\n";
        }
    } elseif (isset($options['favorites'])) {
        $favs = loadFavorites();
        foreach ($favs as $q) {
            echo '"' . $q['text'] . '" — ' . $q['author'] . "\n";
        }
    } else {
        // Интерактивный режим
        echo "💬 ГЕНЕРАТОР СЛУЧАЙНЫХ ЦИТАТ\n";
        $quotes = loadQuotes();
        $favorites = loadFavorites();
        while (true) {
            echo "\nВыберите действие:\n";
            echo "1. Показать случайную цитату\n";
            echo "2. Поиск цитат\n";
            echo "3. Добавить цитату\n";
            echo "4. Избранное\n";
            echo "5. Экспорт цитаты\n";
            echo "6. Категории\n";
            echo "7. Случайная из категории\n";
            echo "0. Выход\n";
            $choice = getInput("Ваш выбор: ");
            if ($choice == '0') break;
            elseif ($choice == '1') {
                $q = getRandomQuote($quotes);
                if ($q) {
                    echo "\n\"" . $q['text'] . "\"\n— " . $q['author'] . " (Категория: " . $q['category'] . ")\n";
                    $ans = getInput("Добавить в избранное? (y/n): ");
                    if (strtolower($ans) == 'y') {
                        if (!in_array($q, $favorites, true)) {
                            $favorites[] = $q;
                            saveFavorites($favorites);
                            echo "✅ Добавлено в избранное!\n";
                        } else {
                            echo "Уже в избранном.\n";
                        }
                    }
                } else {
                    echo "Нет цитат.\n";
                }
            } elseif ($choice == '2') {
                $keyword = getInput("Введите ключевое слово или автора: ");
                if (empty($keyword)) { echo "Введите запрос.\n"; continue; }
                $results = searchQuotes($quotes, $keyword);
                if (empty($results)) {
                    echo "Ничего не найдено.\n";
                } else {
                    echo "\nНайдено " . count($results) . " цитат:\n";
                    $i = 1;
                    foreach ($results as $q) {
                        echo $i . ". \"" . $q['text'] . "\" — " . $q['author'] . "\n";
                        $i++;
                    }
                    $idx = getInput("Введите номер, чтобы сохранить в избранное (или Enter для пропуска): ");
                    if (is_numeric($idx) && $idx >= 1 && $idx <= count($results)) {
                        $q = $results[$idx-1];
                        if (!in_array($q, $favorites, true)) {
                            $favorites[] = $q;
                            saveFavorites($favorites);
                            echo "✅ Добавлено в избранное!\n";
                        } else {
                            echo "Уже в избранном.\n";
                        }
                    }
                }
            } elseif ($choice == '3') {
                $text = getInput("Введите текст цитаты: ");
                if (empty($text)) { echo "Текст обязателен.\n"; continue; }
                $author = getInput("Введите автора: ");
                if (empty($author)) $author = "Неизвестен";
                $cat = getInput("Введите категорию (по умолчанию общее): ");
                if (empty($cat)) $cat = "общее";
                $q = addQuote($quotes, $text, $author, $cat);
                echo "✅ Цитата добавлена: \"" . $q['text'] . "\" — " . $q['author'] . "\n";
            } elseif ($choice == '4') {
                if (empty($favorites)) {
                    echo "Избранное пусто.\n";
                } else {
                    echo "\n⭐ ИЗБРАННОЕ:\n";
                    $i = 1;
                    foreach ($favorites as $q) {
                        echo $i . ". \"" . $q['text'] . "\" — " . $q['author'] . "\n";
                        $i++;
                    }
                    $idx = getInput("Введите номер, чтобы удалить из избранного (или Enter для пропуска): ");
                    if (is_numeric($idx) && $idx >= 1 && $idx <= count($favorites)) {
                        $removed = array_splice($favorites, $idx-1, 1)[0];
                        saveFavorites($favorites);
                        echo "Удалено: \"" . $removed['text'] . "\"\n";
                    }
                }
            } elseif ($choice == '5') {
                $q = getRandomQuote($quotes);
                if (!$q) { echo "Нет цитат для экспорта.\n"; continue; }
                $filename = getInput("Имя файла (по умолчанию создастся автоматически): ");
                $fname = exportQuote($q, $filename ?: null);
                echo "Цитата экспортирована в $fname\n";
            } elseif ($choice == '6') {
                $cats = getCategories($quotes);
                if (empty($cats)) {
                    echo "Нет категорий.\n";
                } else {
                    echo "\n📂 ДОСТУПНЫЕ КАТЕГОРИИ:\n";
                    foreach ($cats as $cat) {
                        $count = count(array_filter($quotes, function($q) use ($cat) { return $q['category'] == $cat; }));
                        echo "  $cat ($count цитат)\n";
                    }
                    $catChoice = getInput("Введите категорию для просмотра цитат (или Enter для пропуска): ");
                    if (!empty($catChoice) && contains($cats, $catChoice)) {
                        $filtered = filterByCategory($quotes, $catChoice);
                        echo "\nЦитаты в категории '$catChoice':\n";
                        $i = 1;
                        foreach (array_slice($filtered, 0, 10) as $q) {
                            echo $i . ". \"" . $q['text'] . "\" — " . $q['author'] . "\n";
                            $i++;
                        }
                        if (count($filtered) > 10) {
                            echo "... и ещё " . (count($filtered)-10) . " цитат.\n";
                        }
                    }
                }
            } elseif ($choice == '7') {
                $cats = getCategories($quotes);
                if (empty($cats)) {
                    echo "Нет категорий.\n";
                    continue;
                }
                echo "Доступные категории: " . implode(", ", $cats) . "\n";
                $cat = getInput("Введите категорию: ");
                if (empty($cat) || !contains($cats, $cat)) {
                    echo "Категория не найдена.\n";
                    continue;
                }
                $filtered = filterByCategory($quotes, $cat);
                if (empty($filtered)) {
                    echo "В этой категории нет цитат.\n";
                } else {
                    $q = $filtered[array_rand($filtered)];
                    echo "\n\"" . $q['text'] . "\"\n— " . $q['author'] . " (Категория: $cat)\n";
                }
            } else {
                echo "Неверный выбор.\n";
            }
        }
    }
    exit;
}

// ========== ВЕБ-ИНТЕРФЕЙС ==========
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>💬 Генератор цитат (PHP)</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7fb; margin: 20px; }
        .container { max-width: 700px; margin: 0 auto; background: white; padding: 20px; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { text-align: center; }
        .form-group { margin-bottom: 15px; }
        label { display: inline-block; width: 100px; }
        input, select, button { padding: 6px; border-radius: 4px; border: 1px solid #ccc; }
        button { background: #3498db; color: white; border: none; cursor: pointer; padding: 6px 20px; }
        button:hover { background: #2980b9; }
        .quote { background: #ecf0f1; padding: 15px; border-radius: 8px; margin-top: 20px; font-style: italic; }
        .history { margin-top: 20px; background: #f8f9fa; padding: 10px; border-radius: 8px; max-height: 200px; overflow-y: auto; }
    </style>
</head>
<body>
<div class="container">
    <h1>💬 Генератор случайных цитат (PHP)</h1>
    <form method="GET">
        <div class="form-group">
            <label>Режим:</label>
            <select name="mode">
                <option value="random" <?= isset($_GET['mode']) && $_GET['mode'] == 'random' ? 'selected' : '' ?>>Случайная цитата</option>
                <option value="search" <?= isset($_GET['mode']) && $_GET['mode'] == 'search' ? 'selected' : '' ?>>Поиск</option>
                <option value="add" <?= isset($_GET['mode']) && $_GET['mode'] == 'add' ? 'selected' : '' ?>>Добавить</option>
                <option value="category" <?= isset($_GET['mode']) && $_GET['mode'] == 'category' ? 'selected' : '' ?>>Категория</option>
            </select>
        </div>
        <div id="input-group">
            <?php if (isset($_GET['mode']) && $_GET['mode'] == 'search'): ?>
                <div class="form-group"><label>Ключевое слово:</label><input type="text" name="keyword" value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>"></div>
            <?php elseif (isset($_GET['mode']) && $_GET['mode'] == 'add'): ?>
                <div class="form-group"><label>Текст:</label><input type="text" name="text" required></div>
                <div class="form-group"><label>Автор:</label><input type="text" name="author"></div>
                <div class="form-group"><label>Категория:</label><input type="text" name="category" value="общее"></div>
            <?php elseif (isset($_GET['mode']) && $_GET['mode'] == 'category'): ?>
                <div class="form-group"><label>Категория:</label>
                    <select name="cat">
                        <?php
                        $cats = getCategories(loadQuotes());
                        foreach ($cats as $c) {
                            echo "<option value=\"$c\">$c</option>";
                        }
                        ?>
                    </select>
                </div>
            <?php endif; ?>
        </div>
        <button type="submit">Выполнить</button>
        <a href="?favorites=1">⭐ Избранное</a>
        <a href="?export=1">📥 Экспорт</a>
    </form>

    <?php
    if (isset($_GET['favorites'])) {
        $favs = loadFavorites();
        if (empty($favs)) {
            echo "<div class='quote'>Избранное пусто.</div>";
        } else {
            echo "<div class='history'><h3>⭐ Избранное</h3>";
            foreach ($favs as $q) {
                echo "<div style='border-bottom:1px solid #eee; padding:5px 0;'>\"{$q['text']}\" — {$q['author']}</div>";
            }
            echo "</div>";
        }
    }
    if (isset($_GET['export'])) {
        $q = getRandomQuote(loadQuotes());
        if ($q) {
            $fname = exportQuote($q);
            echo "<div class='quote'>✅ Экспортировано в $fname</div>";
        }
    }
    if (isset($_GET['mode'])) {
        $quotes = loadQuotes();
        if ($_GET['mode'] == 'random') {
            $q = getRandomQuote($quotes);
            if ($q) {
                echo "<div class='quote'>\"{$q['text']}\"<br>— {$q['author']} (Категория: {$q['category']})</div>";
            }
        } elseif ($_GET['mode'] == 'search' && isset($_GET['keyword'])) {
            $results = searchQuotes($quotes, $_GET['keyword']);
            if (empty($results)) {
                echo "<div class='quote'>Ничего не найдено.</div>";
            } else {
                echo "<div class='history'><h3>Результаты поиска</h3>";
                foreach ($results as $q) {
                    echo "<div style='border-bottom:1px solid #eee; padding:5px 0;'>\"{$q['text']}\" — {$q['author']}</div>";
                }
                echo "</div>";
            }
        } elseif ($_GET['mode'] == 'add' && isset($_GET['text'])) {
            $author = $_GET['author'] ?? 'Неизвестен';
            $cat = $_GET['category'] ?? 'общее';
            $q = addQuote($quotes, $_GET['text'], $author, $cat);
            echo "<div class='quote'>✅ Добавлено: \"{$q['text']}\" — {$q['author']}</div>";
        } elseif ($_GET['mode'] == 'category' && isset($_GET['cat'])) {
            $filtered = filterByCategory($quotes, $_GET['cat']);
            if (empty($filtered)) {
                echo "<div class='quote'>В этой категории нет цитат.</div>";
            } else {
                $q = $filtered[array_rand($filtered)];
                echo "<div class='quote'>\"{$q['text']}\"<br>— {$q['author']} (Категория: {$q['category']})</div>";
            }
        }
    }
    ?>
</div>
</body>
</html>
