# 🌱 Laravel API - Gestione Ordini e Prodotti Ecosostenibili

Un progetto Laravel che espone delle API REST per gestire **prodotti**, **ordini** e calcolare la **CO2 risparmiata** in base alle vendite. Include filtri avanzati per periodo, paese di destinazione e prodotto.

---

## 📦 Requisiti

- PHP >= 8.1
- Composer
- MySQL
- Laravel >= 10.x
- Node.js & NPM (per frontend opzionale o tool di build)

---

## 🚀 Setup del progetto

1. Clona il repository:

```bash
git clone https://github.com/tuo-utente/tuo-repo.git
cd tuo-repo
```

2. Installa le dipendenze:

```bash
composer install
```

3. Copia il file `.env.example` e rinominalo `.env`:

```bash
cp .env.example .env
```

4. Genera la chiave dell'app:

```bash
php artisan key:generate
```

5. Configura il database nel file `.env`:

```env
DB_DATABASE=nome_db
DB_USERNAME=tuo_utente
DB_PASSWORD=tua_password
```

6. Esegui le migration:

```bash
php artisan migrate
```

7. Avvia il server:

```bash
php artisan serve
```

---

## 📡 Endpoints principali

### 📁 Prodotti

| Metodo | Endpoint         | Azione                        |
|--------|------------------|-------------------------------|
| POST   | /api/products     | Crea un nuovo prodotto        |
| PUT    | /api/products/{id}| Modifica un prodotto          |
| DELETE | /api/products/{id}| Elimina un prodotto           |

---

### 📁 Ordini

| Metodo | Endpoint         | Azione                        |
|--------|------------------|-------------------------------|
| POST   | /api/orders       | Crea un nuovo ordine          |
| PUT    | /api/orders/{id}  | Aggiorna un ordine            |
| DELETE | /api/orders/{id}  | Elimina un ordine             |

---

### 🌍 Calcolo CO2 risparmiata

| Metodo | Endpoint              | Parametri query disponibili                       |
|--------|-----------------------|---------------------------------------------------|
| GET    | /api/co2-saved        | `start_date`, `end_date`, `paese_destinazione`, `product_id` |

#### Esempi:

- CO2 totale:
  ```
  GET /api/co2-saved
  ```

- CO2 per paese:
  ```
  GET /api/co2-saved?paese_destinazione=Italia
  ```

- CO2 per data:
  ```
  GET /api/co2-saved?start_date=2024-01-01&end_date=2024-12-31
  ```

- CO2 per prodotto:
  ```
  GET /api/co2-saved?product_id=2
  ```

---




## 📜 Licenza

Questo progetto è rilasciato sotto la licenza MIT.

