# FlyLog

## Przegląd projektu
FlyLog to aplikacja webowa dla pilotów do zarządzania i rejestrowania lotów. Platforma oferuje:

- Rejestrację i logowanie użytkowników
- Dodawanie i usuwanie wpisów do logbooka
- Funkcje społecznościowe takie jak posty, komentarze, polubienia, powiadomienia i czat prywatny między użytkownikami
- Zarządzanie profilem z możliwością przesyłania awatara i wyświetlania osobistych statystyk lotów
- Panel administratora do zarządzania użytkownikami
- Responsywny interfejs na komputery i urządzenia mobilne

Zbudowana przy użyciu PHP 8+, PostgreSQL oraz Dockera.

---

## Kluczowe funkcje
- **Uwierzytelnianie:** Bezpieczna rejestracja, logowanie i zarządzanie sesją
- **Dziennik lotów:** Dodawanie, edycja i przeglądanie własnych lotów
- **Posty i komentarze:** Dodawanie nowych postów, komentowanie i lajkowanie
- **Powiadomienia:** Powiadomienia o nowych postach i pomyslnej rejestracji
- **Czat:** Wiadomości bezpośrednie między użytkownikami
- **Profil:** Przesyłanie awatara, nick, dostęp do statystyk lotów
- **Panel administratora:** Zarządzanie użytkownikami
- **Responsywny design:** Optymalizacja na każde urządzenie

---

## Bezpieczeństwo
- Hashowanie za pomocą bcrypt: `password_hash()` i `password_verify()`
- Sesje PHP: `session_start()`, `session_unset()`, `session_destroy()`
- Ochrona endpointów: sprawdzenie zalogowania i ról użytkownika
- Parametryzowane zapytania PDO (zapobieganie SQL Injection)
- Wyjście danych przez `htmlspecialchars()` (ochrona przed XSS)
- Podstawowa walidacja plików przy uploadzie

---

## Scenariusze użycia
1. **Dodaj nowy lot:** Wprowadź szczegóły lotu, samolot, lotnisko, czas startu i lądowania
2. **Udostępnij post:** Publikuj posty, pytania, zaproszenia na wspólny lot
3. **Komentuj i lajkuj:** Wchodź w interakcje z postami innych użytkowników
4. **Odczytuj powiadomienia:** Otrzymuj powiadomienia o nowych komentarzach i wiadomościach
5. **Czat:** Wysyłaj wiadomości do innych użytkowników
6. **Zarządzaj profilem:** Zmień awatar i analizuj statystyki
7. **Działania administratora:** Wyświetlaj i zarządzaj użytkownikami

---

## Typy modułów
- **Profil:** Wpisy dziennika z samolotem, lotniskami i nalotem, Informacje o użytkowniku i awatar
- **Posty:** Tablica społeczności do dzielenia się i dyskusji
- **Komentarze:** Dyskusje pod postami
- **Powiadomienia:** Alerty o nowej aktywności
- **Wiadomości:** Prywatny czat między użytkownikami
- **Admin:** Zarządzanie użytkownikami

---

## Struktura bazy danych
- **Tabele:** roles, users, auth, posts, posts_history, comments, likes, flights, messages, notifications, users_history
- **Widoki:** v_flights, v_global_flight_stats
- **Funkcje SQL:** get_total_flight_time, get_most_used_airport, get_most_used_aircraft, fn_archive_post, notify_on_register, fn_archive_user
- **Triggery:** trg_posts_archive, trg_notify_on_register, trg_archive_user
- **Dane testowe:** w `init.sql`

---

## Struktura projektu
```
FlyLog/
├─ docker/             # Pliki Dockerfile i konfiguracje
├─ public/             # Zasoby publiczne
│  ├─ js/              # Moduły JavaScript
│  ├─ styles/          # Style CSS
│  ├─ views/           # Szablony PHP
├─ src/                # MVC (Model, Widok, Kontroler)
│  ├─ controllers/     # Kontrolery
│  ├─ models/          # Encje
│  ├─ repository/      # Dostęp do danych
├─ uploads/avatars/    # Awatary użytkowników
├─ docker-compose.yaml # Orkiestracja Dockera
├─ Routing.php         # Logika routingu
├─ index.php           # Front controller
├─ init.sql/           # Schemat bazy i dane testowe
```

---

## Skrypty JavaScript i API
- `public/js/comments.js`: pokazywanie ukrytych komentarzy (front-end)
- `public/js/flights.js`: zarządzanie lotami (AJAX)
  • DELETE /delete-flight (JSON)
- `public/js/like.js`: lajki (AJAX)
  • POST /like-post (FormData)
- `public/js/messages.js`: czat prywatny (AJAX)
  • GET /get-messages-ajax?user={id}
  • POST /send-message (FormData)
- `public/js/navbar.js`: responsywne menu i toggle paska nawigacji
- `public/js/notifications.js`: paginacja listy powiadomień (front-end)
- `public/js/regnot.js`: automatyczne ukrywanie banera rejestracyjnego
- `public/js/toggleAddPost.js`: otwieranie/zamykanie formularza dodawania postu
- `public/js/editor.js`: formatowanie tekstu w edytorze (bold, italic, underline, link)

---

## Instrukcja uruchomienia
1. Zainstaluj Docker i Docker Compose
2. Sklonuj repozytorium:
   ```powershell
   git clone <repo-url>
   cd WDPAIFLYLOG
   ```
3. Zbuduj i uruchom kontenery:
   ```powershell
   docker-compose up --build
   ```
4. (Opcjonalnie) Załaduj `init.sql` do bazy danych, aby mieć dane testowe

Aplikacja będzie dostępna pod adresem [http://localhost:8080](http://localhost:8080)

---

## ERD bazy danych
![alt text](erd.png)

---

## Zrzuty ekranu
**Logowanie**
![Logowanie](image-1.png)
**Rejestracja**
![Rejestracja](image-2.png)
**Strona główna**
![Strona główna](image.png)
**Panel sterowania**
![Panel sterowania](image-3.png)
**Profil**
![Profil](image-4.png)
**Powiadomienia**
![Powiadomienia](image-5.png)
**Wiadomości**
![Wiadomości](image-6.png)

---

## Autor
*Daniel Gadzina*

**License:** [MIT](https://choosealicense.com/licenses/mit/)
