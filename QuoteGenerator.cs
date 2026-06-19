// QuoteGenerator.cs - Генератор случайных цитат на C# (CLI)
using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using System.Text.Json;

namespace QuoteGenerator
{
    class Quote
    {
        public string Text { get; set; }
        public string Author { get; set; }
        public string Category { get; set; }
    }

    class Program
    {
        private static readonly string DATA_FILE = "quotes.json";
        private static readonly string FAV_FILE = "favorites.json";
        private static readonly Random random = new Random();

        private static List<Quote> DefaultQuotes() => new List<Quote>
        {
            new Quote { Text = "Жизнь — это то, что происходит с вами, пока вы строите планы.", Author = "Джон Леннон", Category = "мудрость" },
            new Quote { Text = "Будьте тем изменением, которое хотите видеть в мире.", Author = "Махатма Ганди", Category = "мотивация" },
            new Quote { Text = "Единственный способ делать отличную работу — любить то, что вы делаете.", Author = "Стив Джобс", Category = "успех" },
            new Quote { Text = "Счастье — это не цель, а способ путешествия.", Author = "Зигмунд Фрейд", Category = "жизнь" },
            new Quote { Text = "Тот, кто не рискует, тот не пьет шампанское.", Author = "Петр I", Category = "юмор" },
            new Quote { Text = "Лучший способ предсказать будущее — создать его.", Author = "Питер Друкер", Category = "успех" },
            new Quote { Text = "Тьма не может изгнать тьму: только свет может сделать это.", Author = "Мартин Лютер Кинг", Category = "мудрость" },
            new Quote { Text = "Не судите о книге по обложке.", Author = "Мигель де Сервантес", Category = "мудрость" },
            new Quote { Text = "Все, что мы есть, — это результат того, что мы думали.", Author = "Будда", Category = "мудрость" },
            new Quote { Text = "Самый трудный шаг — первый.", Author = "Китайская пословица", Category = "мотивация" },
            new Quote { Text = "Не бойтесь делать то, что не умеете. Помните, ковчег построил любитель, профессионалы построили «Титаник».", Author = "Дейв Барри", Category = "юмор" },
            new Quote { Text = "Всё, что вы можете вообразить, реально.", Author = "Пабло Пикассо", Category = "мотивация" },
            new Quote { Text = "Секрет успеха — в искренности. Как только вы сможете притвориться искренним, успех придёт.", Author = "Оскар Уайльд", Category = "юмор" },
            new Quote { Text = "Человек — это то, что он читает.", Author = "Джозеф Бродский", Category = "мудрость" },
            new Quote { Text = "Чтобы дойти до цели, надо прежде всего идти.", Author = "Оноре де Бальзак", Category = "мотивация" },
            new Quote { Text = "Учитесь правилам, чтобы нарушать их правильно.", Author = "Пабло Пикассо", Category = "мудрость" },
            new Quote { Text = "Судьба — это не вопрос случая, а вопрос выбора.", Author = "Уильям Дженнингс Брайан", Category = "жизнь" },
            new Quote { Text = "Никогда не сдавайтесь, потому что это просто место и время, когда течение повернётся в вашу пользу.", Author = "Гарриет Бичер-Стоу", Category = "мотивация" },
            new Quote { Text = "Счастье — это не отсутствие проблем, а умение с ними справляться.", Author = "Стив Мараболи", Category = "жизнь" },
            new Quote { Text = "Ваше время ограничено, не тратьте его на чужую жизнь.", Author = "Стив Джобс", Category = "успех" },
        };

        private static List<Quote> LoadQuotes()
        {
            if (File.Exists(DATA_FILE))
            {
                try
                {
                    string json = File.ReadAllText(DATA_FILE);
                    return JsonSerializer.Deserialize<List<Quote>>(json) ?? DefaultQuotes();
                }
                catch { }
            }
            var quotes = DefaultQuotes();
            SaveQuotes(quotes);
            return quotes;
        }

        private static void SaveQuotes(List<Quote> quotes)
        {
            string json = JsonSerializer.Serialize(quotes, new JsonSerializerOptions { WriteIndented = true });
            File.WriteAllText(DATA_FILE, json);
        }

        private static List<Quote> LoadFavorites()
        {
            if (File.Exists(FAV_FILE))
            {
                try
                {
                    string json = File.ReadAllText(FAV_FILE);
                    return JsonSerializer.Deserialize<List<Quote>>(json) ?? new List<Quote>();
                }
                catch { }
            }
            return new List<Quote>();
        }

        private static void SaveFavorites(List<Quote> favs)
        {
            string json = JsonSerializer.Serialize(favs, new JsonSerializerOptions { WriteIndented = true });
            File.WriteAllText(FAV_FILE, json);
        }

        private static Quote GetRandomQuote(List<Quote> quotes)
        {
            if (quotes.Count == 0) return null;
            return quotes[random.Next(quotes.Count)];
        }

        private static List<Quote> SearchQuotes(List<Quote> quotes, string keyword)
        {
            keyword = keyword.ToLower();
            return quotes.Where(q => q.Text.ToLower().Contains(keyword) || q.Author.ToLower().Contains(keyword)).ToList();
        }

        private static Quote AddQuote(List<Quote> quotes, string text, string author, string category)
        {
            if (string.IsNullOrEmpty(category)) category = "общее";
            var q = new Quote { Text = text, Author = author, Category = category };
            quotes.Add(q);
            SaveQuotes(quotes);
            return q;
        }

        private static string ExportQuote(Quote q, string filename)
        {
            if (string.IsNullOrEmpty(filename))
                filename = $"quote_{DateTime.Now:yyyyMMdd_HHmmss}.txt";
            File.WriteAllText(filename, $"\"{q.Text}\"\n— {q.Author}\nКатегория: {q.Category}");
            return filename;
        }

        private static List<string> GetCategories(List<Quote> quotes)
        {
            return quotes.Select(q => q.Category).Distinct().OrderBy(c => c).ToList();
        }

        private static List<Quote> FilterByCategory(List<Quote> quotes, string category)
        {
            return quotes.Where(q => q.Category == category).ToList();
        }

        private static bool Contains(List<string> list, string item)
        {
            return list.Contains(item);
        }

        private static void Interactive()
        {
            Console.WriteLine("💬 ГЕНЕРАТОР СЛУЧАЙНЫХ ЦИТАТ");
            var quotes = LoadQuotes();
            var favorites = LoadFavorites();
            while (true)
            {
                Console.WriteLine("\nВыберите действие:");
                Console.WriteLine("1. Показать случайную цитату");
                Console.WriteLine("2. Поиск цитат");
                Console.WriteLine("3. Добавить цитату");
                Console.WriteLine("4. Избранное");
                Console.WriteLine("5. Экспорт цитаты");
                Console.WriteLine("6. Категории");
                Console.WriteLine("7. Случайная из категории");
                Console.WriteLine("0. Выход");
                Console.Write("Ваш выбор: ");
                string choice = Console.ReadLine();
                if (choice == "0") break;
                else if (choice == "1")
                {
                    var q = GetRandomQuote(quotes);
                    if (q != null)
                    {
                        Console.WriteLine($"\n\"{q.Text}\"\n— {q.Author} (Категория: {q.Category})");
                        Console.Write("Добавить в избранное? (y/n): ");
                        if (Console.ReadLine()?.ToLower() == "y")
                        {
                            if (!favorites.Any(f => f.Text == q.Text && f.Author == q.Author))
                            {
                                favorites.Add(q);
                                SaveFavorites(favorites);
                                Console.WriteLine("✅ Добавлено в избранное!");
                            }
                            else Console.WriteLine("Уже в избранном.");
                        }
                    }
                    else Console.WriteLine("Нет цитат.");
                }
                else if (choice == "2")
                {
                    Console.Write("Введите ключевое слово или автора: ");
                    string keyword = Console.ReadLine();
                    if (string.IsNullOrEmpty(keyword)) { Console.WriteLine("Введите запрос."); continue; }
                    var results = SearchQuotes(quotes, keyword);
                    if (results.Count == 0)
                    {
                        Console.WriteLine("Ничего не найдено.");
                    }
                    else
                    {
                        Console.WriteLine($"\nНайдено {results.Count} цитат:");
                        for (int i = 0; i < results.Count; i++)
                        {
                            var q = results[i];
                            Console.WriteLine($"{i+1}. \"{q.Text}\" — {q.Author}");
                        }
                        Console.Write("Введите номер, чтобы сохранить в избранное (или Enter для пропуска): ");
                        string idxStr = Console.ReadLine();
                        if (!string.IsNullOrEmpty(idxStr) && int.TryParse(idxStr, out int idx) && idx >= 1 && idx <= results.Count)
                        {
                            var q = results[idx-1];
                            if (!favorites.Any(f => f.Text == q.Text && f.Author == q.Author))
                            {
                                favorites.Add(q);
                                SaveFavorites(favorites);
                                Console.WriteLine("✅ Добавлено в избранное!");
                            }
                            else Console.WriteLine("Уже в избранном.");
                        }
                    }
                }
                else if (choice == "3")
                {
                    Console.Write("Введите текст цитаты: ");
                    string text = Console.ReadLine();
                    if (string.IsNullOrEmpty(text)) { Console.WriteLine("Текст обязателен."); continue; }
                    Console.Write("Введите автора: ");
                    string author = Console.ReadLine();
                    if (string.IsNullOrEmpty(author)) author = "Неизвестен";
                    Console.Write("Введите категорию (по умолчанию общее): ");
                    string cat = Console.ReadLine();
                    if (string.IsNullOrEmpty(cat)) cat = "общее";
                    var q = AddQuote(quotes, text, author, cat);
                    Console.WriteLine($"✅ Цитата добавлена: \"{q.Text}\" — {q.Author}");
                }
                else if (choice == "4")
                {
                    if (favorites.Count == 0)
                    {
                        Console.WriteLine("Избранное пусто.");
                    }
                    else
                    {
                        Console.WriteLine("\n⭐ ИЗБРАННОЕ:");
                        for (int i = 0; i < favorites.Count; i++)
                        {
                            var q = favorites[i];
                            Console.WriteLine($"{i+1}. \"{q.Text}\" — {q.Author}");
                        }
                        Console.Write("Введите номер, чтобы удалить из избранного (или Enter для пропуска): ");
                        string idxStr = Console.ReadLine();
                        if (!string.IsNullOrEmpty(idxStr) && int.TryParse(idxStr, out int idx) && idx >= 1 && idx <= favorites.Count)
                        {
                            var removed = favorites[idx-1];
                            favorites.RemoveAt(idx-1);
                            SaveFavorites(favorites);
                            Console.WriteLine($"Удалено: \"{removed.Text}\"");
                        }
                    }
                }
                else if (choice == "5")
                {
                    var q = GetRandomQuote(quotes);
                    if (q == null) { Console.WriteLine("Нет цитат для экспорта."); continue; }
                    Console.Write("Имя файла (по умолчанию создастся автоматически): ");
                    string filename = Console.ReadLine();
                    string fname = ExportQuote(q, string.IsNullOrEmpty(filename) ? null : filename);
                    Console.WriteLine($"Цитата экспортирована в {fname}");
                }
                else if (choice == "6")
                {
                    var cats = GetCategories(quotes);
                    if (cats.Count == 0)
                    {
                        Console.WriteLine("Нет категорий.");
                    }
                    else
                    {
                        Console.WriteLine("\n📂 ДОСТУПНЫЕ КАТЕГОРИИ:");
                        foreach (var cat in cats)
                        {
                            int count = quotes.Count(q => q.Category == cat);
                            Console.WriteLine($"  {cat} ({count} цитат)");
                        }
                        Console.Write("Введите категорию для просмотра цитат (или Enter для пропуска): ");
                        string catChoice = Console.ReadLine();
                        if (!string.IsNullOrEmpty(catChoice) && Contains(cats, catChoice))
                        {
                            var filtered = FilterByCategory(quotes, catChoice);
                            Console.WriteLine($"\nЦитаты в категории '{catChoice}':");
                            for (int i = 0; i < Math.Min(10, filtered.Count); i++)
                            {
                                var q = filtered[i];
                                Console.WriteLine($"{i+1}. \"{q.Text}\" — {q.Author}");
                            }
                            if (filtered.Count > 10)
                                Console.WriteLine($"... и ещё {filtered.Count-10} цитат.");
                        }
                    }
                }
                else if (choice == "7")
                {
                    var cats = GetCategories(quotes);
                    if (cats.Count == 0)
                    {
                        Console.WriteLine("Нет категорий.");
                        continue;
                    }
                    Console.WriteLine("Доступные категории: " + string.Join(", ", cats));
                    Console.Write("Введите категорию: ");
                    string cat = Console.ReadLine();
                    if (string.IsNullOrEmpty(cat) || !Contains(cats, cat))
                    {
                        Console.WriteLine("Категория не найдена.");
                        continue;
                    }
                    var filtered = FilterByCategory(quotes, cat);
                    if (filtered.Count == 0)
                    {
                        Console.WriteLine("В этой категории нет цитат.");
                    }
                    else
                    {
                        var q = filtered[random.Next(filtered.Count)];
                        Console.WriteLine($"\n\"{q.Text}\"\n— {q.Author} (Категория: {cat})");
                    }
                }
                else
                {
                    Console.WriteLine("Неверный выбор.");
                }
            }
        }

        static void Main(string[] args)
        {
            if (args.Length > 0)
            {
                string mode = args[0];
                var quotes = LoadQuotes();
                if (mode == "--random")
                {
                    var q = GetRandomQuote(quotes);
                    if (q != null) Console.WriteLine($"\"{q.Text}\" — {q.Author}");
                }
                else if (mode == "--search" && args.Length > 1)
                {
                    var results = SearchQuotes(quotes, args[1]);
                    foreach (var q in results) Console.WriteLine($"\"{q.Text}\" — {q.Author}");
                }
                else if (mode == "--add" && args.Length > 2)
                {
                    string author = args.Length > 3 ? args[3] : "Неизвестен";
                    string cat = args.Length > 4 ? args[4] : "общее";
                    var q = AddQuote(quotes, args[1], author, cat);
                    Console.WriteLine($"Добавлено: \"{q.Text}\" — {q.Author}");
                }
                else if (mode == "--export")
                {
                    var q = GetRandomQuote(quotes);
                    if (q != null)
                    {
                        string fname = ExportQuote(q, args.Length > 1 ? args[1] : null);
                        Console.WriteLine($"Экспортировано в {fname}");
                    }
                }
                else if (mode == "--favorites")
                {
                    var favs = LoadFavorites();
                    foreach (var q in favs) Console.WriteLine($"\"{q.Text}\" — {q.Author}");
                }
                else
                {
                    Console.WriteLine("Использование: QuoteGenerator [--random] [--search KEYWORD] [--add TEXT AUTHOR [CATEGORY]] [--export [FILE]] [--favorites]");
                }
            }
            else
            {
                Interactive();
            }
        }
    }
}
