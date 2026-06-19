#!/usr/bin/env node
/**
 * quote_generator.js - Генератор случайных цитат на JavaScript (Node.js CLI + веб)
 * CLI: node quote_generator.js --random
 * Веб: откройте как HTML
 */
const fs = require('fs');
const readline = require('readline');
const rl = readline.createInterface({
    input: process.stdin,
    output: process.stdout
});

const DATA_FILE = 'quotes.json';
const FAV_FILE = 'favorites.json';

const DEFAULT_QUOTES = [
    { text: "Жизнь — это то, что происходит с вами, пока вы строите планы.", author: "Джон Леннон", category: "мудрость" },
    { text: "Будьте тем изменением, которое хотите видеть в мире.", author: "Махатма Ганди", category: "мотивация" },
    { text: "Единственный способ делать отличную работу — любить то, что вы делаете.", author: "Стив Джобс", category: "успех" },
    { text: "Счастье — это не цель, а способ путешествия.", author: "Зигмунд Фрейд", category: "жизнь" },
    { text: "Тот, кто не рискует, тот не пьет шампанское.", author: "Петр I", category: "юмор" },
    { text: "Лучший способ предсказать будущее — создать его.", author: "Питер Друкер", category: "успех" },
    { text: "Тьма не может изгнать тьму: только свет может сделать это.", author: "Мартин Лютер Кинг", category: "мудрость" },
    { text: "Не судите о книге по обложке.", author: "Мигель де Сервантес", category: "мудрость" },
    { text: "Все, что мы есть, — это результат того, что мы думали.", author: "Будда", category: "мудрость" },
    { text: "Самый трудный шаг — первый.", author: "Китайская пословица", category: "мотивация" },
    { text: "Не бойтесь делать то, что не умеете. Помните, ковчег построил любитель, профессионалы построили «Титаник».", author: "Дейв Барри", category: "юмор" },
    { text: "Всё, что вы можете вообразить, реально.", author: "Пабло Пикассо", category: "мотивация" },
    { text: "Секрет успеха — в искренности. Как только вы сможете притвориться искренним, успех придёт.", author: "Оскар Уайльд", category: "юмор" },
    { text: "Человек — это то, что он читает.", author: "Джозеф Бродский", category: "мудрость" },
    { text: "Чтобы дойти до цели, надо прежде всего идти.", author: "Оноре де Бальзак", category: "мотивация" },
    { text: "Учитесь правилам, чтобы нарушать их правильно.", author: "Пабло Пикассо", category: "мудрость" },
    { text: "Судьба — это не вопрос случая, а вопрос выбора.", author: "Уильям Дженнингс Брайан", category: "жизнь" },
    { text: "Никогда не сдавайтесь, потому что это просто место и время, когда течение повернётся в вашу пользу.", author: "Гарриет Бичер-Стоу", category: "мотивация" },
    { text: "Счастье — это не отсутствие проблем, а умение с ними справляться.", author: "Стив Мараболи", category: "жизнь" },
    { text: "Ваше время ограничено, не тратьте его на чужую жизнь.", author: "Стив Джобс", category: "успех" },
];

function loadQuotes() {
    if (fs.existsSync(DATA_FILE)) {
        try {
            return JSON.parse(fs.readFileSync(DATA_FILE, 'utf8'));
        } catch {}
    }
    saveQuotes(DEFAULT_QUOTES);
    return DEFAULT_QUOTES;
}

function saveQuotes(quotes) {
    fs.writeFileSync(DATA_FILE, JSON.stringify(quotes, null, 2));
}

function loadFavorites() {
    if (fs.existsSync(FAV_FILE)) {
        try {
            return JSON.parse(fs.readFileSync(FAV_FILE, 'utf8'));
        } catch {}
    }
    return [];
}

function saveFavorites(favs) {
    fs.writeFileSync(FAV_FILE, JSON.stringify(favs, null, 2));
}

function getRandomQuote(quotes) {
    if (!quotes.length) return null;
    return quotes[Math.floor(Math.random() * quotes.length)];
}

function searchQuotes(quotes, keyword) {
    keyword = keyword.toLowerCase();
    return quotes.filter(q => 
        q.text.toLowerCase().includes(keyword) || 
        q.author.toLowerCase().includes(keyword)
    );
}

function addQuote(quotes, text, author, category = 'общее') {
    const q = { text, author, category };
    quotes.push(q);
    saveQuotes(quotes);
    return q;
}

function exportQuote(quote, filename) {
    if (!filename) {
        const now = new Date();
        filename = `quote_${now.getFullYear()}${String(now.getMonth()+1).padStart(2,'0')}${String(now.getDate()).padStart(2,'0')}_${String(now.getHours()).padStart(2,'0')}${String(now.getMinutes()).padStart(2,'0')}${String(now.getSeconds()).padStart(2,'0')}.txt`;
    }
    const content = `"${quote.text}"\n— ${quote.author}\nКатегория: ${quote.category || 'общее'}`;
    fs.writeFileSync(filename, content, 'utf8');
    return filename;
}

function getCategories(quotes) {
    const cats = new Set();
    quotes.forEach(q => cats.add(q.category || 'общее'));
    return Array.from(cats).sort();
}

function filterByCategory(quotes, category) {
    return quotes.filter(q => (q.category || 'общее') === category);
}

function prompt(query) {
    return new Promise(resolve => rl.question(query, resolve));
}

async function interactive() {
    console.log('💬 ГЕНЕРАТОР СЛУЧАЙНЫХ ЦИТАТ');
    let quotes = loadQuotes();
    let favorites = loadFavorites();
    while (true) {
        console.log('\nВыберите действие:');
        console.log('1. Показать случайную цитату');
        console.log('2. Поиск цитат');
        console.log('3. Добавить цитату');
        console.log('4. Избранное');
        console.log('5. Экспорт цитаты');
        console.log('6. Категории');
        console.log('7. Случайная из категории');
        console.log('0. Выход');
        const choice = await prompt('Ваш выбор: ');
        if (choice === '0') break;
        else if (choice === '1') {
            const q = getRandomQuote(quotes);
            if (q) {
                console.log(`\n"${q.text}"\n— ${q.author} (Категория: ${q.category || 'общее'})`);
                const fav = await prompt('Добавить в избранное? (y/n): ');
                if (fav.toLowerCase() === 'y') {
                    if (!favorites.some(f => f.text === q.text && f.author === q.author)) {
                        favorites.push(q);
                        saveFavorites(favorites);
                        console.log('✅ Добавлено в избранное!');
                    } else {
                        console.log('Уже в избранном.');
                    }
                }
            } else {
                console.log('Нет цитат.');
            }
        } else if (choice === '2') {
            const keyword = await prompt('Введите ключевое слово или автора: ');
            if (!keyword) { console.log('Введите запрос.'); continue; }
            const results = searchQuotes(quotes, keyword);
            if (results.length) {
                console.log(`\nНайдено ${results.length} цитат:`);
                results.forEach((q, i) => console.log(`${i+1}. "${q.text}" — ${q.author}`));
                const idx = await prompt('Введите номер, чтобы сохранить в избранное (или Enter для пропуска): ');
                if (idx && !isNaN(idx) && idx >= 1 && idx <= results.length) {
                    const q = results[idx-1];
                    if (!favorites.some(f => f.text === q.text && f.author === q.author)) {
                        favorites.push(q);
                        saveFavorites(favorites);
                        console.log('✅ Добавлено в избранное!');
                    } else {
                        console.log('Уже в избранном.');
                    }
                }
            } else {
                console.log('Ничего не найдено.');
            }
        } else if (choice === '3') {
            const text = await prompt('Введите текст цитаты: ');
            if (!text) { console.log('Текст обязателен.'); continue; }
            const author = await prompt('Введите автора: ') || 'Неизвестен';
            const category = await prompt('Введите категорию (по умолчанию общее): ') || 'общее';
            const q = addQuote(quotes, text, author, category);
            console.log(`✅ Цитата добавлена: "${q.text}" — ${q.author}`);
        } else if (choice === '4') {
            if (!favorites.length) {
                console.log('Избранное пусто.');
            } else {
                console.log('\n⭐ ИЗБРАННОЕ:');
                favorites.forEach((q, i) => console.log(`${i+1}. "${q.text}" — ${q.author}`));
                const idx = await prompt('Введите номер, чтобы удалить из избранного (или Enter для пропуска): ');
                if (idx && !isNaN(idx) && idx >= 1 && idx <= favorites.length) {
                    const removed = favorites.splice(idx-1, 1)[0];
                    saveFavorites(favorites);
                    console.log(`Удалено: "${removed.text}"`);
                }
            }
        } else if (choice === '5') {
            const q = getRandomQuote(quotes);
            if (!q) { console.log('Нет цитат для экспорта.'); continue; }
            const filename = await prompt('Имя файла (по умолчанию создастся автоматически): ');
            const fname = exportQuote(q, filename || undefined);
            console.log(`Цитата экспортирована в ${fname}`);
        } else if (choice === '6') {
            const cats = getCategories(quotes);
            if (!cats.length) { console.log('Нет категорий.'); continue; }
            console.log('\n📂 ДОСТУПНЫЕ КАТЕГОРИИ:');
            cats.forEach(cat => {
                const count = quotes.filter(q => (q.category || 'общее') === cat).length;
                console.log(`  ${cat} (${count} цитат)`);
            });
            const catChoice = await prompt('Введите категорию для просмотра цитат (или Enter для пропуска): ');
            if (catChoice && cats.includes(catChoice)) {
                const filtered = filterByCategory(quotes, catChoice);
                console.log(`\nЦитаты в категории '${catChoice}':`);
                filtered.slice(0, 10).forEach((q, i) => console.log(`${i+1}. "${q.text}" — ${q.author}`));
                if (filtered.length > 10) console.log(`... и ещё ${filtered.length-10} цитат.`);
            }
        } else if (choice === '7') {
            const cats = getCategories(quotes);
            if (!cats.length) { console.log('Нет категорий.'); continue; }
            console.log('Доступные категории:', cats.join(', '));
            const cat = await prompt('Введите категорию: ');
            if (!cat || !cats.includes(cat)) {
                console.log('Категория не найдена.');
                continue;
            }
            const filtered = filterByCategory(quotes, cat);
            if (filtered.length) {
                const q = filtered[Math.floor(Math.random() * filtered.length)];
                console.log(`\n"${q.text}"\n— ${q.author} (Категория: ${cat})`);
            } else {
                console.log('В этой категории нет цитат.');
            }
        } else {
            console.log('Неверный выбор.');
        }
    }
    rl.close();
}

function main() {
    const args = process.argv.slice(2);
    if (args.length > 0) {
        const parsed = {};
        for (let i = 0; i < args.length; i++) {
            if (args[i] === '--random') parsed.random = true;
            else if (args[i] === '--search') parsed.search = args[++i];
            else if (args[i] === '--add') { parsed.add = args[++i]; parsed.author = args[++i]; }
            else if (args[i] === '--category') parsed.category = args[++i];
            else if (args[i] === '--export') parsed.export = true;
            else if (args[i] === '--favorites') parsed.favorites = true;
        }
        const quotes = loadQuotes();
        if (parsed.random) {
            const q = getRandomQuote(quotes);
            if (q) console.log(`"${q.text}" — ${q.author}`);
        } else if (parsed.search) {
            const results = searchQuotes(quotes, parsed.search);
            results.forEach(q => console.log(`"${q.text}" — ${q.author}`));
        } else if (parsed.add) {
            const cat = parsed.category || 'общее';
            const q = addQuote(quotes, parsed.add, parsed.author, cat);
            console.log(`Добавлено: "${q.text}" — ${q.author}`);
        } else if (parsed.export) {
            const q = getRandomQuote(quotes);
            if (q) {
                const fname = exportQuote(q);
                console.log(`Экспортировано в ${fname}`);
            }
        } else if (parsed.favorites) {
            const favs = loadFavorites();
            favs.forEach(q => console.log(`"${q.text}" — ${q.author}`));
        } else {
            console.log('Использование: node quote_generator.js [--random] [--search KEYWORD] [--add TEXT AUTHOR] [--category CAT] [--export] [--favorites]');
        }
    } else {
        interactive().catch(console.error);
    }
}

if (require.main === module) {
    main();
}

// ========== Браузерная версия ==========
if (typeof window !== 'undefined') {
    window.quotes = loadQuotes();
    window.getRandomQuote = () => getRandomQuote(window.quotes);
    window.searchQuotes = (kw) => searchQuotes(window.quotes, kw);
    window.addQuote = (text, author, category) => addQuote(window.quotes, text, author, category);
    window.exportQuote = (quote) => exportQuote(quote);
    window.getCategories = () => getCategories(window.quotes);
    window.filterByCategory = (cat) => filterByCategory(window.quotes, cat);
}
