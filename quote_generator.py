#!/usr/bin/env python3
"""
quote_generator.py - Генератор случайных цитат на Python (CLI)
Поддерживает: базу цитат, поиск, добавление, избранное, экспорт, категории.
"""
import json
import os
import random
import sys
from datetime import datetime

DATA_FILE = "quotes.json"
FAV_FILE = "favorites.json"

# Встроенная база цитат (50+)
DEFAULT_QUOTES = [
    {"text": "Жизнь — это то, что происходит с вами, пока вы строите планы.", "author": "Джон Леннон", "category": "мудрость"},
    {"text": "В двух вещах нет смысла: в том, что вы можете изменить, но не меняете, и в том, что вы не можете изменить, но пытаетесь.", "author": "Альберт Эйнштейн", "category": "мудрость"},
    {"text": "Будьте тем изменением, которое хотите видеть в мире.", "author": "Махатма Ганди", "category": "мотивация"},
    {"text": "Счастье — это не цель, а способ путешествия.", "author": "Зигмунд Фрейд", "category": "жизнь"},
    {"text": "Единственный способ делать отличную работу — любить то, что вы делаете.", "author": "Стив Джобс", "category": "успех"},
    {"text": "Жизнь слишком коротка, чтобы пить плохое вино.", "author": "Иоганн Вольфганг фон Гёте", "category": "жизнь"},
    {"text": "Тот, кто не рискует, тот не пьет шампанское.", "author": "Петр I", "category": "юмор"},
    {"text": "Лучший способ предсказать будущее — создать его.", "author": "Питер Друкер", "category": "успех"},
    {"text": "Тьма не может изгнать тьму: только свет может сделать это.", "author": "Мартин Лютер Кинг", "category": "мудрость"},
    {"text": "Не судите о книге по обложке.", "author": "Мигель де Сервантес", "category": "мудрость"},
    {"text": "Время — самый мудрый советчик.", "author": "Перикл", "category": "мудрость"},
    {"text": "Все, что мы есть, — это результат того, что мы думали.", "author": "Будда", "category": "мудрость"},
    {"text": "Самый трудный шаг — первый.", "author": "Китайская пословица", "category": "мотивация"},
    {"text": "Не бойтесь делать то, что не умеете. Помните, ковчег построил любитель, профессионалы построили «Титаник».", "author": "Дейв Барри", "category": "юмор"},
    {"text": "Жизнь — это то, что происходит, пока вы заняты другими планами.", "author": "Джон Леннон", "category": "жизнь"},
    {"text": "Всё, что вы можете вообразить, реально.", "author": "Пабло Пикассо", "category": "мотивация"},
    {"text": "Секрет успеха — в искренности. Как только вы сможете притвориться искренним, успех придёт.", "author": "Оскар Уайльд", "category": "юмор"},
    {"text": "Жизнь — это то, что происходит, когда вы заняты строительством планов.", "author": "Джон Леннон", "category": "жизнь"},
    {"text": "Человек — это то, что он читает.", "author": "Джозеф Бродский", "category": "мудрость"},
    {"text": "Чтобы дойти до цели, надо прежде всего идти.", "author": "Оноре де Бальзак", "category": "мотивация"},
    {"text": "Учитесь правилам, чтобы нарушать их правильно.", "author": "Пабло Пикассо", "category": "мудрость"},
    {"text": "Судьба — это не вопрос случая, а вопрос выбора.", "author": "Уильям Дженнингс Брайан", "category": "жизнь"},
    {"text": "Никогда не сдавайтесь, потому что это просто место и время, когда течение повернётся в вашу пользу.", "author": "Гарриет Бичер-Стоу", "category": "мотивация"},
    {"text": "Счастье — это не отсутствие проблем, а умение с ними справляться.", "author": "Стив Мараболи", "category": "жизнь"},
    {"text": "Ваше время ограничено, не тратьте его на чужую жизнь.", "author": "Стив Джобс", "category": "успех"},
    {"text": "Не позволяйте шуму чужих мнений заглушить ваш внутренний голос.", "author": "Стив Джобс", "category": "успех"},
    {"text": "Все великие истины начинаются как кощунство.", "author": "Джордж Бернард Шоу", "category": "мудрость"},
    {"text": "Жизнь — это не поиск себя, а создание себя.", "author": "Джордж Бернард Шоу", "category": "жизнь"},
    {"text": "Только тот, кто рискует, может быть свободным.", "author": "Фридрих Ницше", "category": "мотивация"},
    {"text": "В любви и на войне все средства хороши.", "author": "Френсис Бэкон", "category": "любовь"},
    {"text": "Любовь — это единственная сила, способная превратить врага в друга.", "author": "Мартин Лютер Кинг", "category": "любовь"},
    {"text": "Искусство — это ложь, которая помогает нам понять правду.", "author": "Пабло Пикассо", "category": "мудрость"},
    {"text": "Творчество — это видение того, чего нет, и создание этого.", "author": "Джеймс Тёрбер", "category": "мотивация"},
    {"text": "Самый большой риск — не рисковать.", "author": "Марк Цукерберг", "category": "успех"},
    {"text": "Успех — это способность идти от неудачи к неудаче, не теряя энтузиазма.", "author": "Уинстон Черчилль", "category": "успех"},
    {"text": "Все, что мы слышим, — это мнение, а не факт. Все, что мы видим, — это перспектива, а не истина.", "author": "Марк Аврелий", "category": "мудрость"},
    {"text": "Лучшая месть — это огромный успех.", "author": "Фрэнк Синатра", "category": "успех"},
    {"text": "Счастье — это когда то, что вы думаете, говорите и делаете, находится в гармонии.", "author": "Махатма Ганди", "category": "жизнь"},
    {"text": "Жизнь измеряется не количеством вдохов, а моментами, от которых захватывает дух.", "author": "Майя Энджелоу", "category": "жизнь"},
    {"text": "Кто не хочет умереть, должен любить жизнь.", "author": "Сенека", "category": "мудрость"},
    {"text": "Самый надежный способ держать слово — никогда его не давать.", "author": "Наполеон Бонапарт", "category": "юмор"},
    {"text": "Победа — это не всё, желание победить — вот что важно.", "author": "Винс Ломбарди", "category": "мотивация"},
    {"text": "В каждом из нас сидит солнце, нужно только дать ему светить.", "author": "Сократ", "category": "мудрость"},
    {"text": "Умный человек создаёт больше возможностей, чем находит.", "author": "Фрэнсис Бэкон", "category": "успех"},
    {"text": "Жизнь — это не только поиск себя, но и создание себя.", "author": "Джордж Бернард Шоу", "category": "жизнь"},
    {"text": "Надежда — это сон с открытыми глазами.", "author": "Аристотель", "category": "мудрость"},
    {"text": "Человек, который никогда не ошибался, никогда не пробовал ничего нового.", "author": "Альберт Эйнштейн", "category": "мудрость"},
    {"text": "Жизнь слишком коротка, чтобы тратить её на ненависть.", "author": "Нельсон Мандела", "category": "жизнь"},
    {"text": "Лучший способ начать — перестать говорить и начать делать.", "author": "Уолт Дисней", "category": "мотивация"},
    {"text": "Секрет перемен — в том, чтобы сосредоточить всю свою энергию не на борьбе со старым, а на создании нового.", "author": "Сократ", "category": "мудрость"},
    {"text": "Не судите человека по его ответам, судите по его вопросам.", "author": "Вольтер", "category": "мудрость"},
    {"text": "Счастье — это не станция назначения, а способ путешествия.", "author": "Маргарет Ли Ранбек", "category": "жизнь"},
]

def load_quotes():
    if os.path.exists(DATA_FILE):
        try:
            with open(DATA_FILE, 'r', encoding='utf-8') as f:
                return json.load(f)
        except:
            pass
    # Если файла нет или он повреждён, сохраняем дефолтные
    save_quotes(DEFAULT_QUOTES)
    return DEFAULT_QUOTES

def save_quotes(quotes):
    with open(DATA_FILE, 'w', encoding='utf-8') as f:
        json.dump(quotes, f, indent=2, ensure_ascii=False)

def load_favorites():
    if os.path.exists(FAV_FILE):
        try:
            with open(FAV_FILE, 'r', encoding='utf-8') as f:
                return json.load(f)
        except:
            return []
    return []

def save_favorites(favs):
    with open(FAV_FILE, 'w', encoding='utf-8') as f:
        json.dump(favs, f, indent=2, ensure_ascii=False)

def get_random_quote(quotes):
    return random.choice(quotes) if quotes else None

def search_quotes(quotes, keyword):
    keyword = keyword.lower()
    results = []
    for q in quotes:
        if keyword in q['text'].lower() or keyword in q['author'].lower():
            results.append(q)
    return results

def add_quote(quotes, text, author, category="общее"):
    new_quote = {"text": text, "author": author, "category": category}
    quotes.append(new_quote)
    save_quotes(quotes)
    return new_quote

def export_quote(quote, filename=None):
    if not filename:
        filename = f"quote_{datetime.now().strftime('%Y%m%d_%H%M%S')}.txt"
    with open(filename, 'w', encoding='utf-8') as f:
        f.write(f'"{quote["text"]}"\n— {quote["author"]}\nКатегория: {quote.get("category", "общее")}')
    return filename

def get_categories(quotes):
    return sorted(set(q.get('category', 'общее') for q in quotes))

def filter_by_category(quotes, category):
    return [q for q in quotes if q.get('category', 'общее') == category]

def interactive():
    quotes = load_quotes()
    favorites = load_favorites()
    print("💬 ГЕНЕРАТОР СЛУЧАЙНЫХ ЦИТАТ")
    while True:
        print("\nВыберите действие:")
        print("1. Показать случайную цитату")
        print("2. Поиск цитат")
        print("3. Добавить цитату")
        print("4. Избранное")
        print("5. Экспорт цитаты")
        print("6. Категории")
        print("7. Случайная из категории")
        print("0. Выход")
        choice = input("Ваш выбор: ").strip()
        if choice == '0':
            break
        elif choice == '1':
            quote = get_random_quote(quotes)
            if quote:
                print(f"\n\"{quote['text']}\"\n— {quote['author']} (Категория: {quote.get('category', 'общее')})")
                fav = input("Добавить в избранное? (y/n): ").strip().lower()
                if fav == 'y':
                    if quote not in favorites:
                        favorites.append(quote)
                        save_favorites(favorites)
                        print("✅ Добавлено в избранное!")
                    else:
                        print("Уже в избранном.")
            else:
                print("Нет цитат.")
        elif choice == '2':
            keyword = input("Введите ключевое слово или автора: ").strip()
            if not keyword:
                print("Введите запрос.")
                continue
            results = search_quotes(quotes, keyword)
            if results:
                print(f"\nНайдено {len(results)} цитат:")
                for i, q in enumerate(results, 1):
                    print(f"{i}. \"{q['text']}\" — {q['author']}")
                choice = input("Введите номер, чтобы сохранить в избранное (или Enter для пропуска): ").strip()
                if choice.isdigit() and 1 <= int(choice) <= len(results):
                    q = results[int(choice)-1]
                    if q not in favorites:
                        favorites.append(q)
                        save_favorites(favorites)
                        print("✅ Добавлено в избранное!")
            else:
                print("Ничего не найдено.")
        elif choice == '3':
            text = input("Введите текст цитаты: ").strip()
            if not text:
                print("Текст обязателен.")
                continue
            author = input("Введите автора: ").strip()
            if not author:
                author = "Неизвестен"
            category = input("Введите категорию (по умолчанию общее): ").strip()
            if not category:
                category = "общее"
            q = add_quote(quotes, text, author, category)
            print(f"✅ Цитата добавлена: \"{q['text']}\" — {q['author']}")
        elif choice == '4':
            if not favorites:
                print("Избранное пусто.")
            else:
                print("\n⭐ ИЗБРАННОЕ:")
                for i, q in enumerate(favorites, 1):
                    print(f"{i}. \"{q['text']}\" — {q['author']}")
                choice = input("Введите номер, чтобы удалить из избранного (или Enter для пропуска): ").strip()
                if choice.isdigit() and 1 <= int(choice) <= len(favorites):
                    removed = favorites.pop(int(choice)-1)
                    save_favorites(favorites)
                    print(f"Удалено: \"{removed['text']}\"")
        elif choice == '5':
            quote = get_random_quote(quotes)
            if not quote:
                print("Нет цитат для экспорта.")
                continue
            filename = input("Имя файла (по умолчанию создастся автоматически): ").strip()
            if filename:
                filename = export_quote(quote, filename)
            else:
                filename = export_quote(quote)
            print(f"Цитата экспортирована в {filename}")
        elif choice == '6':
            categories = get_categories(quotes)
            if not categories:
                print("Нет категорий.")
            else:
                print("\n📂 ДОСТУПНЫЕ КАТЕГОРИИ:")
                for cat in categories:
                    count = len([q for q in quotes if q.get('category', 'общее') == cat])
                    print(f"  {cat} ({count} цитат)")
                cat_choice = input("Введите категорию для просмотра цитат (или Enter для пропуска): ").strip()
                if cat_choice and cat_choice in categories:
                    filtered = filter_by_category(quotes, cat_choice)
                    print(f"\nЦитаты в категории '{cat_choice}':")
                    for i, q in enumerate(filtered[:10], 1):
                        print(f"{i}. \"{q['text']}\" — {q['author']}")
                    if len(filtered) > 10:
                        print(f"... и ещё {len(filtered)-10} цитат.")
        elif choice == '7':
            categories = get_categories(quotes)
            if not categories:
                print("Нет категорий.")
                continue
            print("Доступные категории:", ", ".join(categories))
            cat = input("Введите категорию: ").strip()
            if not cat or cat not in categories:
                print("Категория не найдена.")
                continue
            filtered = filter_by_category(quotes, cat)
            if filtered:
                quote = random.choice(filtered)
                print(f"\n\"{quote['text']}\"\n— {quote['author']} (Категория: {cat})")
            else:
                print("В этой категории нет цитат.")
        else:
            print("Неверный выбор.")

if __name__ == "__main__":
    if len(sys.argv) > 1:
        # CLI с аргументами
        import argparse
        parser = argparse.ArgumentParser(description="Генератор цитат")
        parser.add_argument("--random", action="store_true", help="Показать случайную цитату")
        parser.add_argument("--search", help="Поиск по ключевому слову")
        parser.add_argument("--add", nargs=2, metavar=("TEXT", "AUTHOR"), help="Добавить цитату")
        parser.add_argument("--category", help="Категория для добавления")
        parser.add_argument("--export", action="store_true", help="Экспортировать случайную цитату")
        parser.add_argument("--favorites", action="store_true", help="Показать избранное")
        args = parser.parse_args()
        quotes = load_quotes()
        if args.random:
            q = get_random_quote(quotes)
            if q:
                print(f"\"{q['text']}\" — {q['author']}")
        elif args.search:
            results = search_quotes(quotes, args.search)
            for q in results:
                print(f"\"{q['text']}\" — {q['author']}")
        elif args.add:
            cat = args.category if args.category else "общее"
            q = add_quote(quotes, args.add[0], args.add[1], cat)
            print(f"Добавлено: \"{q['text']}\" — {q['author']}")
        elif args.export:
            q = get_random_quote(quotes)
            if q:
                fname = export_quote(q)
                print(f"Экспортировано в {fname}")
        elif args.favorites:
            favs = load_favorites()
            for q in favs:
                print(f"\"{q['text']}\" — {q['author']}")
        else:
            parser.print_help()
    else:
        interactive()
