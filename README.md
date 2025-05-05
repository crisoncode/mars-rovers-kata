# Mars Rovers Kata – Backend

A Laravel implementation of the Mars Rovers technical test, structured using hexagonal architecture (ports & adapters).

---

## 🚀 Project Overview

This backend simulates a Mars Rover navigating a planet, processing a sequence of movement and rotation commands. The codebase is organized to demonstrate clean architecture and testable domain logic.

---

## 🗂️ Project Structure

- **Domain:** Core business logic (Entities, Value Objects, Enums, Exceptions) in `app/Mars/Domain`
- **Application:** Use cases and services (e.g., command parsing) in `app/Mars/Application`
- **Adapters:** HTTP controllers in `app/Http/Controllers`
- **Tests:** Unit and feature tests in `tests/Unit/Mars` and `tests/Feature`

---

## 🛠️ Setup Instructions

1. **Clone the repository**
   ```bash
   git clone https://github.com/crisoncode/mars-rovers-kata.git
   cd mars-rovers-kata
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Run the application**
   ```bash
   php artisan serve
   ```
   The API will be available at [http://localhost:8000](http://localhost:8000).

---

## 🧑‍💻 How to Use

- The main API endpoints are:
  - `GET /api/mars/state` – Get the current planet and rover state.
  - `POST /api/mars/commands` – Send a command sequence to the rover (e.g., `FFRRFFFRL`).

- The rover accepts commands:
  - `F` – Move forward
  - `L` – Turn left
  - `R` – Turn right

---

## 🧪 Running Tests

Run all backend tests with:
```bash
php artisan test
```
This will execute all unit and feature tests, including domain logic and command parsing.

---

## 🏗️ Architecture Notes

- **Hexagonal (Ports & Adapters):** The domain logic is decoupled from Laravel and the HTTP layer.
- **Domain:** All business rules and state transitions are in the domain layer.
- **Application:** Command parsing and orchestration are handled in the application layer.
- **Adapters:** HTTP controllers expose the API.

---

## 📝 Example Use Case

- **Initial position:** (2, 3), facing North
- **Commands:** `FFRRFFFRL`
- **Expected final position:** (2, 2), facing South

---

## ❓ Troubleshooting

- If you encounter autoloading issues, run:
  ```bash
  composer dump-autoload
  ```
- For test failures, check the test output for assertion errors and review the domain logic.

---

## 👤 Author

- Cristian Estarlich
