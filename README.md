# ✅ Checklist App (CodeIgniter 4)

Preprosta in učinkovita aplikacija za upravljanje opravil (todo-list), zgrajena z [CodeIgniter 4](https://codeigniter.com/). Podpira dodajanje, urejanje, brisanje ter izvoz/uvoz opravil. Vključuje tudi uporabniški sistem in osnovne teste.

---

## 📦 Zahteve

- PHP >= 8.1
- MySQL / MariaDB
- Composer
- [XAMPP](https://www.apachefriends.org/) (priporočeno za lokalni razvoj)

---

## 🚀 Namestitev

1. **Kloniraj repozitorij**
   ```bash
   git clone https://github.com/ime-uporabnika/checklist-app.git
   cd checklist-app
   ```

2. **Namesti odvisnosti**
   ```bash
   composer install
   ```

3. **Kopiraj `.env` datoteko in jo konfiguriraj**
   ```bash
   cp env .env
   ```

   Nato v `.env` nastavi povezavo do obeh baz:

   ```ini
   database.default.hostname = localhost
   database.default.database = checklist_db
   database.default.username = root
   database.default.password =

   database.tests.hostname = localhost
   database.tests.database = checklist_test_db
   database.tests.username = root
   database.tests.password =
   ```

4. **Uvozi bazi podatkov**

   V direktoriju `mysql/` se nahajata dve SQL datoteki:

   - `main.sql` – glavna baza
   - `test.sql` – testna baza

   Uvozi ju preko phpMyAdmin ali z ukazom:
   ```bash
   mysql -u root -p checklist_db < mysql/main.sql
   mysql -u root -p checklist_test_db < mysql/test.sql
   ```

5. **Zaženi aplikacijo**
   ```bash
   php spark serve
   ```

   Dostopaj do aplikacije na: [http://localhost:8080](http://localhost:8080)

---

## 📁 Struktura projekta (osnovno)

```
app/
│
├── Controllers/         # Checklist, Auth itd.
├── Models/              # ListItemModel, ChecklistModel, CustomModel
├── Views/               # Blade-style view datoteke
│
├── Config/              # Vključno z Routes.php
├── Database/
│   └── Migrations/      # (če uporabljaš migracije)
│
├── Tests/
│   └── app/             # Testne datoteke
│
├── mysql/               # .sql export za baze
├── .env                 # Okoljska konfiguracija
└── README.md
```

---

## 🪪 Licenca

Ta projekt je odprtokoden in na voljo pod [MIT licenco](LICENSE).

---

## 📫 Kontakt

Za vprašanja ali predloge: [ziga.obstetar@gmail.com](mailto:ziga.obstetar@gmail.com)
