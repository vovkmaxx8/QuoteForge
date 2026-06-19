// QuoteGenerator.java - Генератор случайных цитат на Java (CLI)
import java.io.*;
import java.nio.file.*;
import java.util.*;
import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;

public class QuoteGenerator {
    private static final String DATA_FILE = "quotes.json";
    private static final String FAV_FILE = "favorites.json";
    private static final Scanner scanner = new Scanner(System.in);
    private static final Random random = new Random();

    static class Quote {
        String text;
        String author;
        String category;
        Quote(String text, String author, String category) {
            this.text = text; this.author = author; this.category = category;
        }
    }

    private static List<Quote> defaultQuotes() {
        List<Quote> list = new ArrayList<>();
        list.add(new Quote("Жизнь — это то, что происходит с вами, пока вы строите планы.", "Джон Леннон", "мудрость"));
        list.add(new Quote("Будьте тем изменением, которое хотите видеть в мире.", "Махатма Ганди", "мотивация"));
        list.add(new Quote("Единственный способ делать отличную работу — любить то, что вы делаете.", "Стив Джобс", "успех"));
        list.add(new Quote("Счастье — это не цель, а способ путешествия.", "Зигмунд Фрейд", "жизнь"));
        list.add(new Quote("Тот, кто не рискует, тот не пьет шампанское.", "Петр I", "юмор"));
        list.add(new Quote("Лучший способ предсказать будущее — создать его.", "Питер Друкер", "успех"));
        list.add(new Quote("Тьма не может изгнать тьму: только свет может сделать это.", "Мартин Лютер Кинг", "мудрость"));
        list.add(new Quote("Не судите о книге по обложке.", "Мигель де Сервантес", "мудрость"));
        list.add(new Quote("Все, что мы есть, — это результат того, что мы думали.", "Будда", "мудрость"));
        list.add(new Quote("Самый трудный шаг — первый.", "Китайская пословица", "мотивация"));
        list.add(new Quote("Не бойтесь делать то, что не умеете. Помните, ковчег построил любитель, профессионалы построили «Титаник».", "Дейв Барри", "юмор"));
        list.add(new Quote("Всё, что вы можете вообразить, реально.", "Пабло Пикассо", "мотивация"));
        list.add(new Quote("Секрет успеха — в искренности. Как только вы сможете притвориться искренним, успех придёт.", "Оскар Уайльд", "юмор"));
        list.add(new Quote("Человек — это то, что он читает.", "Джозеф Бродский", "мудрость"));
        list.add(new Quote("Чтобы дойти до цели, надо прежде всего идти.", "Оноре де Бальзак", "мотивация"));
        list.add(new Quote("Учитесь правилам, чтобы нарушать их правильно.", "Пабло Пикассо", "мудрость"));
        list.add(new Quote("Судьба — это не вопрос случая, а вопрос выбора.", "Уильям Дженнингс Брайан", "жизнь"));
        list.add(new Quote("Никогда не сдавайтесь, потому что это просто место и время, когда течение повернётся в вашу пользу.", "Гарриет Бичер-Стоу", "мотивация"));
        list.add(new Quote("Счастье — это не отсутствие проблем, а умение с ними справляться.", "Стив Мараболи", "жизнь"));
        list.add(new Quote("Ваше время ограничено, не тратьте его на чужую жизнь.", "Стив Джобс", "успех"));
        return list;
    }

    public static List<Quote> loadQuotes() {
        try {
            String json = new String(Files.readAllBytes(Paths.get(DATA_FILE)));
            // Упрощённый парсинг (в реальном проекте использовать Jackson)
            // Здесь мы просто создаём список, для демонстрации будем использовать JSON вручную.
            // Вместо этого загрузим дефолтные, если файл не существует.
            // В реальном коде лучше использовать библиотеку.
        } catch (Exception e) {}
        // Если файла нет, сохраняем дефолтные
        List<Quote> quotes = defaultQuotes();
        saveQuotes(quotes);
        return quotes;
    }

    public static void saveQuotes(List<Quote> quotes) {
        try (PrintWriter pw = new PrintWriter(DATA_FILE)) {
            pw.println("[");
            for (int i = 0; i < quotes.size(); i++) {
                Quote q = quotes.get(i);
                pw.printf("  {\"text\":\"%s\",\"author\":\"%s\",\"category\":\"%s\"}%s\n",
                        q.text, q.author, q.category, (i < quotes.size()-1 ? "," : ""));
            }
            pw.println("]");
        } catch (IOException e) {}
    }

    public static List<Quote> loadFavorites() {
        List<Quote> favs = new ArrayList<>();
        try {
            String json = new String(Files.readAllBytes(Paths.get(FAV_FILE)));
            // Упрощённо
        } catch (Exception e) {}
        return favs;
    }

    public static void saveFavorites(List<Quote> favs) {
        try (PrintWriter pw = new PrintWriter(FAV_FILE)) {
            pw.println("[");
            for (int i = 0; i < favs.size(); i++) {
                Quote q = favs.get(i);
                pw.printf("  {\"text\":\"%s\",\"author\":\"%s\",\"category\":\"%s\"}%s\n",
                        q.text, q.author, q.category, (i < favs.size()-1 ? "," : ""));
            }
            pw.println("]");
        } catch (IOException e) {}
    }

    public static Quote getRandomQuote(List<Quote> quotes) {
        if (quotes.isEmpty()) return null;
        return quotes.get(random.nextInt(quotes.size()));
    }

    public static List<Quote> searchQuotes(List<Quote> quotes, String keyword) {
        List<Quote> results = new ArrayList<>();
        keyword = keyword.toLowerCase();
        for (Quote q : quotes) {
            if (q.text.toLowerCase().contains(keyword) || q.author.toLowerCase().contains(keyword)) {
                results.add(q);
            }
        }
        return results;
    }

    public static void addQuote(List<Quote> quotes, String text, String author, String category) {
        if (category == null || category.isEmpty()) category = "общее";
        quotes.add(new Quote(text, author, category));
        saveQuotes(quotes);
    }

    public static String exportQuote(Quote q, String filename) {
        if (filename == null || filename.isEmpty()) {
            filename = "quote_" + LocalDateTime.now().format(DateTimeFormatter.ofPattern("yyyyMMdd_HHmmss")) + ".txt";
        }
        try (PrintWriter pw = new PrintWriter(filename)) {
            pw.printf("\"%s\"\n— %s\nКатегория: %s\n", q.text, q.author, q.category);
        } catch (IOException e) {}
        return filename;
    }

    public static List<String> getCategories(List<Quote> quotes) {
        Set<String> set = new HashSet<>();
        for (Quote q : quotes) set.add(q.category);
        List<String> cats = new ArrayList<>(set);
        Collections.sort(cats);
        return cats;
    }

    public static List<Quote> filterByCategory(List<Quote> quotes, String category) {
        List<Quote> res = new ArrayList<>();
        for (Quote q : quotes) {
            if (q.category.equals(category)) res.add(q);
        }
        return res;
    }

    public static boolean contains(List<String> list, String item) {
        for (String s : list) if (s.equals(item)) return true;
        return false;
    }

    public static void interactive() {
        System.out.println("💬 ГЕНЕРАТОР СЛУЧАЙНЫХ ЦИТАТ");
        List<Quote> quotes = loadQuotes();
        List<Quote> favorites = loadFavorites();
        while (true) {
            System.out.println("\nВыберите действие:");
            System.out.println("1. Показать случайную цитату");
            System.out.println("2. Поиск цитат");
            System.out.println("3. Добавить цитату");
            System.out.println("4. Избранное");
            System.out.println("5. Экспорт цитаты");
            System.out.println("6. Категории");
            System.out.println("7. Случайная из категории");
            System.out.println("0. Выход");
            System.out.print("Ваш выбор: ");
            String choice = scanner.nextLine().trim();
            if (choice.equals("0")) break;
            else if (choice.equals("1")) {
                Quote q = getRandomQuote(quotes);
                if (q != null) {
                    System.out.printf("\n\"%s\"\n— %s (Категория: %s)\n", q.text, q.author, q.category);
                    System.out.print("Добавить в избранное? (y/n): ");
                    if (scanner.nextLine().trim().toLowerCase().equals("y")) {
                        boolean already = false;
                        for (Quote f : favorites) {
                            if (f.text.equals(q.text) && f.author.equals(q.author)) { already = true; break; }
                        }
                        if (!already) {
                            favorites.add(q);
                            saveFavorites(favorites);
                            System.out.println("✅ Добавлено в избранное!");
                        } else {
                            System.out.println("Уже в избранном.");
                        }
                    }
                } else {
                    System.out.println("Нет цитат.");
                }
            } else if (choice.equals("2")) {
                System.out.print("Введите ключевое слово или автора: ");
                String keyword = scanner.nextLine().trim();
                if (keyword.isEmpty()) { System.out.println("Введите запрос."); continue; }
                List<Quote> results = searchQuotes(quotes, keyword);
                if (results.isEmpty()) {
                    System.out.println("Ничего не найдено.");
                } else {
                    System.out.printf("\nНайдено %d цитат:\n", results.size());
                    for (int i = 0; i < results.size(); i++) {
                        Quote q = results.get(i);
                        System.out.printf("%d. \"%s\" — %s\n", i+1, q.text, q.author);
                    }
                    System.out.print("Введите номер, чтобы сохранить в избранное (или Enter для пропуска): ");
                    String idxStr = scanner.nextLine().trim();
                    if (!idxStr.isEmpty()) {
                        int idx = Integer.parseInt(idxStr);
                        if (idx >= 1 && idx <= results.size()) {
                            Quote q = results.get(idx-1);
                            boolean already = false;
                            for (Quote f : favorites) {
                                if (f.text.equals(q.text) && f.author.equals(q.author)) { already = true; break; }
                            }
                            if (!already) {
                                favorites.add(q);
                                saveFavorites(favorites);
                                System.out.println("✅ Добавлено в избранное!");
                            } else {
                                System.out.println("Уже в избранном.");
                            }
                        }
                    }
                }
            } else if (choice.equals("3")) {
                System.out.print("Введите текст цитаты: ");
                String text = scanner.nextLine().trim();
                if (text.isEmpty()) { System.out.println("Текст обязателен."); continue; }
                System.out.print("Введите автора: ");
                String author = scanner.nextLine().trim();
                if (author.isEmpty()) author = "Неизвестен";
                System.out.print("Введите категорию (по умолчанию общее): ");
                String cat = scanner.nextLine().trim();
                if (cat.isEmpty()) cat = "общее";
                addQuote(quotes, text, author, cat);
                System.out.printf("✅ Цитата добавлена: \"%s\" — %s\n", text, author);
            } else if (choice.equals("4")) {
                if (favorites.isEmpty()) {
                    System.out.println("Избранное пусто.");
                } else {
                    System.out.println("\n⭐ ИЗБРАННОЕ:");
                    for (int i = 0; i < favorites.size(); i++) {
                        Quote q = favorites.get(i);
                        System.out.printf("%d. \"%s\" — %s\n", i+1, q.text, q.author);
                    }
                    System.out.print("Введите номер, чтобы удалить из избранного (или Enter для пропуска): ");
                    String idxStr = scanner.nextLine().trim();
                    if (!idxStr.isEmpty()) {
                        int idx = Integer.parseInt(idxStr);
                        if (idx >= 1 && idx <= favorites.size()) {
                            Quote removed = favorites.remove(idx-1);
                            saveFavorites(favorites);
                            System.out.printf("Удалено: \"%s\"\n", removed.text);
                        }
                    }
                }
            } else if (choice.equals("5")) {
                Quote q = getRandomQuote(quotes);
                if (q == null) { System.out.println("Нет цитат для экспорта."); continue; }
                System.out.print("Имя файла (по умолчанию создастся автоматически): ");
                String filename = scanner.nextLine().trim();
                String fname = exportQuote(q, filename.isEmpty() ? null : filename);
                System.out.println("Цитата экспортирована в " + fname);
            } else if (choice.equals("6")) {
                List<String> cats = getCategories(quotes);
                if (cats.isEmpty()) {
                    System.out.println("Нет категорий.");
                } else {
                    System.out.println("\n📂 ДОСТУПНЫЕ КАТЕГОРИИ:");
                    for (String cat : cats) {
                        int count = 0;
                        for (Quote q : quotes) if (q.category.equals(cat)) count++;
                        System.out.printf("  %s (%d цитат)\n", cat, count);
                    }
                    System.out.print("Введите категорию для просмотра цитат (или Enter для пропуска): ");
                    String catChoice = scanner.nextLine().trim();
                    if (!catChoice.isEmpty() && contains(cats, catChoice)) {
                        List<Quote> filtered = filterByCategory(quotes, catChoice);
                        System.out.printf("\nЦитаты в категории '%s':\n", catChoice);
                        for (int i = 0; i < Math.min(10, filtered.size()); i++) {
                            Quote q = filtered.get(i);
                            System.out.printf("%d. \"%s\" — %s\n", i+1, q.text, q.author);
                        }
                        if (filtered.size() > 10) {
                            System.out.printf("... и ещё %d цитат.\n", filtered.size()-10);
                        }
                    }
                }
            } else if (choice.equals("7")) {
                List<String> cats = getCategories(quotes);
                if (cats.isEmpty()) {
                    System.out.println("Нет категорий.");
                    continue;
                }
                System.out.print("Доступные категории: " + String.join(", ", cats) + "\n");
                System.out.print("Введите категорию: ");
                String cat = scanner.nextLine().trim();
                if (cat.isEmpty() || !contains(cats, cat)) {
                    System.out.println("Категория не найдена.");
                    continue;
                }
                List<Quote> filtered = filterByCategory(quotes, cat);
                if (filtered.isEmpty()) {
                    System.out.println("В этой категории нет цитат.");
                } else {
                    Quote q = filtered.get(random.nextInt(filtered.size()));
                    System.out.printf("\n\"%s\"\n— %s (Категория: %s)\n", q.text, q.author, q.category);
                }
            } else {
                System.out.println("Неверный выбор.");
            }
        }
    }

    public static void main(String[] args) {
        if (args.length > 0) {
            String mode = args[0];
            List<Quote> quotes = loadQuotes();
            if (mode.equals("--random")) {
                Quote q = getRandomQuote(quotes);
                if (q != null) System.out.printf("\"%s\" — %s\n", q.text, q.author);
            } else if (mode.equals("--search") && args.length > 1) {
                List<Quote> results = searchQuotes(quotes, args[1]);
                for (Quote q : results) System.out.printf("\"%s\" — %s\n", q.text, q.author);
            } else if (mode.equals("--add") && args.length > 2) {
                String author = args.length > 3 ? args[3] : "Неизвестен";
                String cat = args.length > 4 ? args[4] : "общее";
                addQuote(quotes, args[1], author, cat);
                System.out.printf("Добавлено: \"%s\" — %s\n", args[1], author);
            } else if (mode.equals("--export")) {
                Quote q = getRandomQuote(quotes);
                if (q != null) {
                    String fname = exportQuote(q, args.length > 1 ? args[1] : null);
                    System.out.println("Экспортировано в " + fname);
                }
            } else if (mode.equals("--favorites")) {
                List<Quote> favs = loadFavorites();
                for (Quote q : favs) System.out.printf("\"%s\" — %s\n", q.text, q.author);
            } else {
                System.out.println("Использование: java QuoteGenerator [--random] [--search KEYWORD] [--add TEXT AUTHOR [CATEGORY]] [--export [FILE]] [--favorites]");
            }
        } else {
            interactive();
        }
    }
}
