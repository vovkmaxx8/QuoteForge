// quote_generator.rs - Генератор случайных цитат на Rust (CLI)
use serde::{Serialize, Deserialize};
use std::collections::HashSet;
use std::fs;
use std::io::{self, Write, BufRead};
use std::path::Path;
use std::str::FromStr;
use rand::seq::SliceRandom;
use rand::thread_rng;
use chrono::Local;

#[derive(Serialize, Deserialize, Clone)]
struct Quote {
    text: String,
    author: String,
    category: String,
}

const DATA_FILE: &str = "quotes.json";
const FAV_FILE: &str = "favorites.json";

fn default_quotes() -> Vec<Quote> {
    vec![
        Quote { text: "Жизнь — это то, что происходит с вами, пока вы строите планы.".to_string(), author: "Джон Леннон".to_string(), category: "мудрость".to_string() },
        Quote { text: "Будьте тем изменением, которое хотите видеть в мире.".to_string(), author: "Махатма Ганди".to_string(), category: "мотивация".to_string() },
        Quote { text: "Единственный способ делать отличную работу — любить то, что вы делаете.".to_string(), author: "Стив Джобс".to_string(), category: "успех".to_string() },
        Quote { text: "Счастье — это не цель, а способ путешествия.".to_string(), author: "Зигмунд Фрейд".to_string(), category: "жизнь".to_string() },
        Quote { text: "Тот, кто не рискует, тот не пьет шампанское.".to_string(), author: "Петр I".to_string(), category: "юмор".to_string() },
        Quote { text: "Лучший способ предсказать будущее — создать его.".to_string(), author: "Питер Друкер".to_string(), category: "успех".to_string() },
        Quote { text: "Тьма не может изгнать тьму: только свет может сделать это.".to_string(), author: "Мартин Лютер Кинг".to_string(), category: "мудрость".to_string() },
        Quote { text: "Не судите о книге по обложке.".to_string(), author: "Мигель де Сервантес".to_string(), category: "мудрость".to_string() },
        Quote { text: "Все, что мы есть, — это результат того, что мы думали.".to_string(), author: "Будда".to_string(), category: "мудрость".to_string() },
        Quote { text: "Самый трудный шаг — первый.".to_string(), author: "Китайская пословица".to_string(), category: "мотивация".to_string() },
        Quote { text: "Не бойтесь делать то, что не умеете. Помните, ковчег построил любитель, профессионалы построили «Титаник».".to_string(), author: "Дейв Барри".to_string(), category: "юмор".to_string() },
        Quote { text: "Всё, что вы можете вообразить, реально.".to_string(), author: "Пабло Пикассо".to_string(), category: "мотивация".to_string() },
        Quote { text: "Секрет успеха — в искренности. Как только вы сможете притвориться искренним, успех придёт.".to_string(), author: "Оскар Уайльд".to_string(), category: "юмор".to_string() },
        Quote { text: "Человек — это то, что он читает.".to_string(), author: "Джозеф Бродский".to_string(), category: "мудрость".to_string() },
        Quote { text: "Чтобы дойти до цели, надо прежде всего идти.".to_string(), author: "Оноре де Бальзак".to_string(), category: "мотивация".to_string() },
        Quote { text: "Учитесь правилам, чтобы нарушать их правильно.".to_string(), author: "Пабло Пикассо".to_string(), category: "мудрость".to_string() },
        Quote { text: "Судьба — это не вопрос случая, а вопрос выбора.".to_string(), author: "Уильям Дженнингс Брайан".to_string(), category: "жизнь".to_string() },
        Quote { text: "Никогда не сдавайтесь, потому что это просто место и время, когда течение повернётся в вашу пользу.".to_string(), author: "Гарриет Бичер-Стоу".to_string(), category: "мотивация".to_string() },
        Quote { text: "Счастье — это не отсутствие проблем, а умение с ними справляться.".to_string(), author: "Стив Мараболи".to_string(), category: "жизнь".to_string() },
        Quote { text: "Ваше время ограничено, не тратьте его на чужую жизнь.".to_string(), author: "Стив Джобс".to_string(), category: "успех".to_string() },
    ]
}

fn load_quotes() -> Vec<Quote> {
    if Path::new(DATA_FILE).exists() {
        if let Ok(data) = fs::read_to_string(DATA_FILE) {
            if let Ok(quotes) = serde_json::from_str(&data) {
                return quotes;
            }
        }
    }
    let quotes = default_quotes();
    save_quotes(&quotes);
    quotes
}

fn save_quotes(quotes: &[Quote]) {
    let data = serde_json::to_string_pretty(quotes).unwrap();
    fs::write(DATA_FILE, data).unwrap();
}

fn load_favorites() -> Vec<Quote> {
    if Path::new(FAV_FILE).exists() {
        if let Ok(data) = fs::read_to_string(FAV_FILE) {
            if let Ok(favs) = serde_json::from_str(&data) {
                return favs;
            }
        }
    }
    Vec::new()
}

fn save_favorites(favs: &[Quote]) {
    let data = serde_json::to_string_pretty(favs).unwrap();
    fs::write(FAV_FILE, data).unwrap();
}

fn get_random_quote(quotes: &[Quote]) -> Option<Quote> {
    if quotes.is_empty() {
        None
    } else {
        let mut rng = thread_rng();
        quotes.choose(&mut rng).cloned()
    }
}

fn search_quotes(quotes: &[Quote], keyword: &str) -> Vec<Quote> {
    let keyword = keyword.to_lowercase();
    quotes.iter()
        .filter(|q| q.text.to_lowercase().contains(&keyword) || q.author.to_lowercase().contains(&keyword))
        .cloned()
        .collect()
}

fn add_quote(quotes: &mut Vec<Quote>, text: &str, author: &str, category: &str) -> Quote {
    let q = Quote {
        text: text.to_string(),
        author: if author.is_empty() { "Неизвестен".to_string() } else { author.to_string() },
        category: if category.is_empty() { "общее".to_string() } else { category.to_string() },
    };
    quotes.push(q.clone());
    save_quotes(quotes);
    q
}

fn export_quote(q: &Quote, filename: Option<&str>) -> String {
    let filename = match filename {
        Some(f) => f.to_string(),
        None => format!("quote_{}.txt", Local::now().format("%Y%m%d_%H%M%S")),
    };
    let content = format!("\"{}\"\n— {}\nКатегория: {}", q.text, q.author, q.category);
    fs::write(&filename, content).unwrap();
    filename
}

fn get_categories(quotes: &[Quote]) -> Vec<String> {
    let mut set = HashSet::new();
    for q in quotes {
        set.insert(q.category.clone());
    }
    let mut cats: Vec<_> = set.into_iter().collect();
    cats.sort();
    cats
}

fn filter_by_category(quotes: &[Quote], category: &str) -> Vec<Quote> {
    quotes.iter().filter(|q| q.category == category).cloned().collect()
}

fn contains(slice: &[String], item: &str) -> bool {
    slice.iter().any(|s| s == item)
}

fn read_line(prompt: &str) -> String {
    print!("{}", prompt);
    io::stdout().flush().unwrap();
    let mut input = String::new();
    io::stdin().read_line(&mut input).unwrap();
    input.trim().to_string()
}

fn interactive() {
    println!("💬 ГЕНЕРАТОР СЛУЧАЙНЫХ ЦИТАТ");
    let mut quotes = load_quotes();
    let mut favorites = load_favorites();
    let stdin = io::stdin();
    loop {
        println!("\nВыберите действие:");
        println!("1. Показать случайную цитату");
        println!("2. Поиск цитат");
        println!("3. Добавить цитату");
        println!("4. Избранное");
        println!("5. Экспорт цитаты");
        println!("6. Категории");
        println!("7. Случайная из категории");
        println!("0. Выход");
        let choice = read_line("Ваш выбор: ");
        match choice.as_str() {
            "0" => break,
            "1" => {
                if let Some(q) = get_random_quote(&quotes) {
                    println!("\n\"{}\"\n— {} (Категория: {})", q.text, q.author, q.category);
                    let ans = read_line("Добавить в избранное? (y/n): ");
                    if ans.to_lowercase() == "y" {
                        if !favorites.iter().any(|f| f.text == q.text && f.author == q.author) {
                            favorites.push(q);
                            save_favorites(&favorites);
                            println!("✅ Добавлено в избранное!");
                        } else {
                            println!("Уже в избранном.");
                        }
                    }
                } else {
                    println!("Нет цитат.");
                }
            }
            "2" => {
                let keyword = read_line("Введите ключевое слово или автора: ");
                if keyword.is_empty() {
                    println!("Введите запрос.");
                    continue;
                }
                let results = search_quotes(&quotes, &keyword);
                if results.is_empty() {
                    println!("Ничего не найдено.");
                } else {
                    println!("\nНайдено {} цитат:", results.len());
                    for (i, q) in results.iter().enumerate() {
                        println!("{}. \"{}\" — {}", i+1, q.text, q.author);
                    }
                    let idx_str = read_line("Введите номер, чтобы сохранить в избранное (или Enter для пропуска): ");
                    if let Ok(idx) = idx_str.parse::<usize>() {
                        if idx >= 1 && idx <= results.len() {
                            let q = results[idx-1].clone();
                            if !favorites.iter().any(|f| f.text == q.text && f.author == q.author) {
                                favorites.push(q);
                                save_favorites(&favorites);
                                println!("✅ Добавлено в избранное!");
                            } else {
                                println!("Уже в избранном.");
                            }
                        }
                    }
                }
            }
            "3" => {
                let text = read_line("Введите текст цитаты: ");
                if text.is_empty() {
                    println!("Текст обязателен.");
                    continue;
                }
                let author = read_line("Введите автора: ");
                let author = if author.is_empty() { "Неизвестен".to_string() } else { author };
                let category = read_line("Введите категорию (по умолчанию общее): ");
                let category = if category.is_empty() { "общее".to_string() } else { category };
                let q = add_quote(&mut quotes, &text, &author, &category);
                println!("✅ Цитата добавлена: \"{}\" — {}", q.text, q.author);
            }
            "4" => {
                if favorites.is_empty() {
                    println!("Избранное пусто.");
                } else {
                    println!("\n⭐ ИЗБРАННОЕ:");
                    for (i, q) in favorites.iter().enumerate() {
                        println!("{}. \"{}\" — {}", i+1, q.text, q.author);
                    }
                    let idx_str = read_line("Введите номер, чтобы удалить из избранного (или Enter для пропуска): ");
                    if let Ok(idx) = idx_str.parse::<usize>() {
                        if idx >= 1 && idx <= favorites.len() {
                            let removed = favorites.remove(idx-1);
                            save_favorites(&favorites);
                            println!("Удалено: \"{}\"", removed.text);
                        }
                    }
                }
            }
            "5" => {
                if let Some(q) = get_random_quote(&quotes) {
                    let filename = read_line("Имя файла (по умолчанию создастся автоматически): ");
                    let fname = if filename.is_empty() {
                        export_quote(&q, None)
                    } else {
                        export_quote(&q, Some(&filename))
                    };
                    println!("Цитата экспортирована в {}", fname);
                } else {
                    println!("Нет цитат для экспорта.");
                }
            }
            "6" => {
                let cats = get_categories(&quotes);
                if cats.is_empty() {
                    println!("Нет категорий.");
                } else {
                    println!("\n📂 ДОСТУПНЫЕ КАТЕГОРИИ:");
                    for cat in &cats {
                        let count = quotes.iter().filter(|q| q.category == *cat).count();
                        println!("  {} ({} цитат)", cat, count);
                    }
                    let cat_choice = read_line("Введите категорию для просмотра цитат (или Enter для пропуска): ");
                    if !cat_choice.is_empty() && contains(&cats, &cat_choice) {
                        let filtered = filter_by_category(&quotes, &cat_choice);
                        println!("\nЦитаты в категории '{}':", cat_choice);
                        for (i, q) in filtered.iter().enumerate().take(10) {
                            println!("{}. \"{}\" — {}", i+1, q.text, q.author);
                        }
                        if filtered.len() > 10 {
                            println!("... и ещё {} цитат.", filtered.len() - 10);
                        }
                    }
                }
            }
            "7" => {
                let cats = get_categories(&quotes);
                if cats.is_empty() {
                    println!("Нет категорий.");
                    continue;
                }
                println!("Доступные категории: {}", cats.join(", "));
                let cat = read_line("Введите категорию: ");
                if cat.is_empty() || !contains(&cats, &cat) {
                    println!("Категория не найдена.");
                    continue;
                }
                let filtered = filter_by_category(&quotes, &cat);
                if filtered.is_empty() {
                    println!("В этой категории нет цитат.");
                } else {
                    if let Some(q) = filtered.choose(&mut thread_rng()) {
                        println!("\n\"{}\"\n— {} (Категория: {})", q.text, q.author, q.category);
                    }
                }
            }
            _ => println!("Неверный выбор."),
        }
    }
}

fn main() {
    let args: Vec<String> = std::env::args().collect();
    if args.len() > 1 {
        let mut random = false;
        let mut search = String::new();
        let mut add_text = String::new();
        let mut add_author = String::new();
        let mut category = String::new();
        let mut export = false;
        let mut favorites = false;
        let mut i = 1;
        while i < args.len() {
            match args[i].as_str() {
                "--random" => random = true,
                "--search" => { search = args[i+1].clone(); i += 1; }
                "--add" => { add_text = args[i+1].clone(); i += 1; }
                "--author" => { add_author = args[i+1].clone(); i += 1; }
                "--category" => { category = args[i+1].clone(); i += 1; }
                "--export" => export = true,
                "--favorites" => favorites = true,
                _ => {}
            }
            i += 1;
        }
        let quotes = load_quotes();
        if random {
            if let Some(q) = get_random_quote(&quotes) {
                println!("\"{}\" — {}", q.text, q.author);
            }
        } else if !search.is_empty() {
            let results = search_quotes(&quotes, &search);
            for q in results {
                println!("\"{}\" — {}", q.text, q.author);
            }
        } else if !add_text.is_empty() && !add_author.is_empty() {
            let mut qs = quotes;
            let q = add_quote(&mut qs, &add_text, &add_author, if category.is_empty() { "общее" } else { &category });
            println!("Добавлено: \"{}\" — {}", q.text, q.author);
        } else if export {
            if let Some(q) = get_random_quote(&quotes) {
                let fname = export_quote(&q, None);
                println!("Экспортировано в {}", fname);
            }
        } else if favorites {
            let favs = load_favorites();
            for q in favs {
                println!("\"{}\" — {}", q.text, q.author);
            }
        } else {
            interactive();
        }
    } else {
        interactive();
    }
}
