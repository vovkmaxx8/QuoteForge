// quote_generator.go - Генератор случайных цитат на Go (CLI)
package main

import (
	"bufio"
	"encoding/json"
	"flag"
	"fmt"
	"math/rand"
	"os"
	"strings"
	"time"
)

type Quote struct {
	Text     string `json:"text"`
	Author   string `json:"author"`
	Category string `json:"category"`
}

var dataFile = "quotes.json"
var favFile = "favorites.json"

var defaultQuotes = []Quote{
	{"Жизнь — это то, что происходит с вами, пока вы строите планы.", "Джон Леннон", "мудрость"},
	{"Будьте тем изменением, которое хотите видеть в мире.", "Махатма Ганди", "мотивация"},
	{"Единственный способ делать отличную работу — любить то, что вы делаете.", "Стив Джобс", "успех"},
	{"Счастье — это не цель, а способ путешествия.", "Зигмунд Фрейд", "жизнь"},
	{"Тот, кто не рискует, тот не пьет шампанское.", "Петр I", "юмор"},
	{"Лучший способ предсказать будущее — создать его.", "Питер Друкер", "успех"},
	{"Тьма не может изгнать тьму: только свет может сделать это.", "Мартин Лютер Кинг", "мудрость"},
	{"Не судите о книге по обложке.", "Мигель де Сервантес", "мудрость"},
	{"Все, что мы есть, — это результат того, что мы думали.", "Будда", "мудрость"},
	{"Самый трудный шаг — первый.", "Китайская пословица", "мотивация"},
	{"Не бойтесь делать то, что не умеете. Помните, ковчег построил любитель, профессионалы построили «Титаник».", "Дейв Барри", "юмор"},
	{"Всё, что вы можете вообразить, реально.", "Пабло Пикассо", "мотивация"},
	{"Секрет успеха — в искренности. Как только вы сможете притвориться искренним, успех придёт.", "Оскар Уайльд", "юмор"},
	{"Человек — это то, что он читает.", "Джозеф Бродский", "мудрость"},
	{"Чтобы дойти до цели, надо прежде всего идти.", "Оноре де Бальзак", "мотивация"},
	{"Учитесь правилам, чтобы нарушать их правильно.", "Пабло Пикассо", "мудрость"},
	{"Судьба — это не вопрос случая, а вопрос выбора.", "Уильям Дженнингс Брайан", "жизнь"},
	{"Никогда не сдавайтесь, потому что это просто место и время, когда течение повернётся в вашу пользу.", "Гарриет Бичер-Стоу", "мотивация"},
	{"Счастье — это не отсутствие проблем, а умение с ними справляться.", "Стив Мараболи", "жизнь"},
	{"Ваше время ограничено, не тратьте его на чужую жизнь.", "Стив Джобс", "успех"},
}

func loadQuotes() []Quote {
	var quotes []Quote
	file, err := os.ReadFile(dataFile)
	if err != nil {
		saveQuotes(defaultQuotes)
		return defaultQuotes
	}
	err = json.Unmarshal(file, &quotes)
	if err != nil || len(quotes) == 0 {
		saveQuotes(defaultQuotes)
		return defaultQuotes
	}
	return quotes
}

func saveQuotes(quotes []Quote) {
	data, _ := json.MarshalIndent(quotes, "", "  ")
	os.WriteFile(dataFile, data, 0644)
}

func loadFavorites() []Quote {
	var favs []Quote
	file, err := os.ReadFile(favFile)
	if err != nil {
		return favs
	}
	json.Unmarshal(file, &favs)
	return favs
}

func saveFavorites(favs []Quote) {
	data, _ := json.MarshalIndent(favs, "", "  ")
	os.WriteFile(favFile, data, 0644)
}

func getRandomQuote(quotes []Quote) *Quote {
	if len(quotes) == 0 {
		return nil
	}
	return &quotes[rand.Intn(len(quotes))]
}

func searchQuotes(quotes []Quote, keyword string) []Quote {
	var results []Quote
	keyword = strings.ToLower(keyword)
	for _, q := range quotes {
		if strings.Contains(strings.ToLower(q.Text), keyword) || strings.Contains(strings.ToLower(q.Author), keyword) {
			results = append(results, q)
		}
	}
	return results
}

func addQuote(quotes []Quote, text, author, category string) []Quote {
	if category == "" {
		category = "общее"
	}
	q := Quote{Text: text, Author: author, Category: category}
	quotes = append(quotes, q)
	saveQuotes(quotes)
	return quotes
}

func exportQuote(q Quote, filename string) string {
	if filename == "" {
		filename = fmt.Sprintf("quote_%s.txt", time.Now().Format("20060102_150405"))
	}
	content := fmt.Sprintf("\"%s\"\n— %s\nКатегория: %s", q.Text, q.Author, q.Category)
	os.WriteFile(filename, []byte(content), 0644)
	return filename
}

func getCategories(quotes []Quote) []string {
	catMap := make(map[string]bool)
	for _, q := range quotes {
		catMap[q.Category] = true
	}
	var cats []string
	for c := range catMap {
		cats = append(cats, c)
	}
	sortStrings(cats)
	return cats
}

func sortStrings(s []string) {
	for i := 0; i < len(s); i++ {
		for j := i + 1; j < len(s); j++ {
			if s[i] > s[j] {
				s[i], s[j] = s[j], s[i]
			}
		}
	}
}

func filterByCategory(quotes []Quote, category string) []Quote {
	var res []Quote
	for _, q := range quotes {
		if q.Category == category {
			res = append(res, q)
		}
	}
	return res
}

func interactive() {
	rand.Seed(time.Now().UnixNano())
	scanner := bufio.NewScanner(os.Stdin)
	fmt.Println("💬 ГЕНЕРАТОР СЛУЧАЙНЫХ ЦИТАТ")
	quotes := loadQuotes()
	favorites := loadFavorites()
	for {
		fmt.Println("\nВыберите действие:")
		fmt.Println("1. Показать случайную цитату")
		fmt.Println("2. Поиск цитат")
		fmt.Println("3. Добавить цитату")
		fmt.Println("4. Избранное")
		fmt.Println("5. Экспорт цитаты")
		fmt.Println("6. Категории")
		fmt.Println("7. Случайная из категории")
		fmt.Println("0. Выход")
		fmt.Print("Ваш выбор: ")
		scanner.Scan()
		choice := scanner.Text()
		switch choice {
		case "0":
			return
		case "1":
			q := getRandomQuote(quotes)
			if q != nil {
				fmt.Printf("\n\"%s\"\n— %s (Категория: %s)\n", q.Text, q.Author, q.Category)
				fmt.Print("Добавить в избранное? (y/n): ")
				scanner.Scan()
				if strings.ToLower(scanner.Text()) == "y" {
					already := false
					for _, f := range favorites {
						if f.Text == q.Text && f.Author == q.Author {
							already = true
							break
						}
					}
					if !already {
						favorites = append(favorites, *q)
						saveFavorites(favorites)
						fmt.Println("✅ Добавлено в избранное!")
					} else {
						fmt.Println("Уже в избранном.")
					}
				}
			} else {
				fmt.Println("Нет цитат.")
			}
		case "2":
			fmt.Print("Введите ключевое слово или автора: ")
			scanner.Scan()
			keyword := scanner.Text()
			if keyword == "" {
				fmt.Println("Введите запрос.")
				continue
			}
			results := searchQuotes(quotes, keyword)
			if len(results) > 0 {
				fmt.Printf("\nНайдено %d цитат:\n", len(results))
				for i, q := range results {
					fmt.Printf("%d. \"%s\" — %s\n", i+1, q.Text, q.Author)
				}
				fmt.Print("Введите номер, чтобы сохранить в избранное (или Enter для пропуска): ")
				scanner.Scan()
				idxStr := scanner.Text()
				if idxStr != "" {
					var idx int
					fmt.Sscan(idxStr, &idx)
					if idx >= 1 && idx <= len(results) {
						q := results[idx-1]
						already := false
						for _, f := range favorites {
							if f.Text == q.Text && f.Author == q.Author {
								already = true
								break
							}
						}
						if !already {
							favorites = append(favorites, q)
							saveFavorites(favorites)
							fmt.Println("✅ Добавлено в избранное!")
						} else {
							fmt.Println("Уже в избранном.")
						}
					}
				}
			} else {
				fmt.Println("Ничего не найдено.")
			}
		case "3":
			fmt.Print("Введите текст цитаты: ")
			scanner.Scan()
			text := scanner.Text()
			if text == "" {
				fmt.Println("Текст обязателен.")
				continue
			}
			fmt.Print("Введите автора: ")
			scanner.Scan()
			author := scanner.Text()
			if author == "" {
				author = "Неизвестен"
			}
			fmt.Print("Введите категорию (по умолчанию общее): ")
			scanner.Scan()
			category := scanner.Text()
			if category == "" {
				category = "общее"
			}
			quotes = addQuote(quotes, text, author, category)
			fmt.Printf("✅ Цитата добавлена: \"%s\" — %s\n", text, author)
		case "4":
			if len(favorites) == 0 {
				fmt.Println("Избранное пусто.")
			} else {
				fmt.Println("\n⭐ ИЗБРАННОЕ:")
				for i, q := range favorites {
					fmt.Printf("%d. \"%s\" — %s\n", i+1, q.Text, q.Author)
				}
				fmt.Print("Введите номер, чтобы удалить из избранного (или Enter для пропуска): ")
				scanner.Scan()
				idxStr := scanner.Text()
				if idxStr != "" {
					var idx int
					fmt.Sscan(idxStr, &idx)
					if idx >= 1 && idx <= len(favorites) {
						removed := favorites[idx-1]
						favorites = append(favorites[:idx-1], favorites[idx:]...)
						saveFavorites(favorites)
						fmt.Printf("Удалено: \"%s\"\n", removed.Text)
					}
				}
			}
		case "5":
			q := getRandomQuote(quotes)
			if q == nil {
				fmt.Println("Нет цитат для экспорта.")
				continue
			}
			fmt.Print("Имя файла (по умолчанию создастся автоматически): ")
			scanner.Scan()
			filename := scanner.Text()
			fname := exportQuote(*q, filename)
			fmt.Printf("Цитата экспортирована в %s\n", fname)
		case "6":
			cats := getCategories(quotes)
			if len(cats) == 0 {
				fmt.Println("Нет категорий.")
			} else {
				fmt.Println("\n📂 ДОСТУПНЫЕ КАТЕГОРИИ:")
				for _, cat := range cats {
					count := 0
					for _, q := range quotes {
						if q.Category == cat {
							count++
						}
					}
					fmt.Printf("  %s (%d цитат)\n", cat, count)
				}
				fmt.Print("Введите категорию для просмотра цитат (или Enter для пропуска): ")
				scanner.Scan()
				catChoice := scanner.Text()
				if catChoice != "" && contains(cats, catChoice) {
					filtered := filterByCategory(quotes, catChoice)
					fmt.Printf("\nЦитаты в категории '%s':\n", catChoice)
					for i, q := range filtered {
						if i >= 10 {
							fmt.Printf("... и ещё %d цитат.\n", len(filtered)-10)
							break
						}
						fmt.Printf("%d. \"%s\" — %s\n", i+1, q.Text, q.Author)
					}
				}
			}
		case "7":
			cats := getCategories(quotes)
			if len(cats) == 0 {
				fmt.Println("Нет категорий.")
				continue
			}
			fmt.Print("Доступные категории: ", strings.Join(cats, ", "), "\n")
			fmt.Print("Введите категорию: ")
			scanner.Scan()
			cat := scanner.Text()
			if cat == "" || !contains(cats, cat) {
				fmt.Println("Категория не найдена.")
				continue
			}
			filtered := filterByCategory(quotes, cat)
			if len(filtered) > 0 {
				q := filtered[rand.Intn(len(filtered))]
				fmt.Printf("\n\"%s\"\n— %s (Категория: %s)\n", q.Text, q.Author, q.Category)
			} else {
				fmt.Println("В этой категории нет цитат.")
			}
		default:
			fmt.Println("Неверный выбор.")
		}
	}
}

func contains(slice []string, item string) bool {
	for _, s := range slice {
		if s == item {
			return true
		}
	}
	return false
}

func main() {
	rand.Seed(time.Now().UnixNano())
	var (
		random    bool
		search    string
		addText   string
		addAuthor string
		category  string
		export    bool
		favorites bool
	)
	flag.BoolVar(&random, "random", false, "Показать случайную цитату")
	flag.StringVar(&search, "search", "", "Поиск по ключевому слову")
	flag.StringVar(&addText, "add", "", "Текст цитаты (для добавления)")
	flag.StringVar(&addAuthor, "author", "", "Автор (для добавления)")
	flag.StringVar(&category, "category", "общее", "Категория для добавления")
	flag.BoolVar(&export, "export", false, "Экспортировать случайную цитату")
	flag.BoolVar(&favorites, "favorites", false, "Показать избранное")
	flag.Parse()

	quotes := loadQuotes()
	if random {
		q := getRandomQuote(quotes)
		if q != nil {
			fmt.Printf("\"%s\" — %s\n", q.Text, q.Author)
		}
	} else if search != "" {
		results := searchQuotes(quotes, search)
		for _, q := range results {
			fmt.Printf("\"%s\" — %s\n", q.Text, q.Author)
		}
	} else if addText != "" && addAuthor != "" {
		quotes = addQuote(quotes, addText, addAuthor, category)
		fmt.Printf("Добавлено: \"%s\" — %s\n", addText, addAuthor)
	} else if export {
		q := getRandomQuote(quotes)
		if q != nil {
			fname := exportQuote(*q, "")
			fmt.Printf("Экспортировано в %s\n", fname)
		}
	} else if favorites {
		favs := loadFavorites()
		for _, q := range favs {
			fmt.Printf("\"%s\" — %s\n", q.Text, q.Author)
		}
	} else {
		interactive()
	}
}
