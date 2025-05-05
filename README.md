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

2. **Setup the project**

### With make
   ```bash
   Make build
   Make up
   ```
### With Docker
   ```bash
   docker compose build
   docker compose up
   ```

### In local
   ```bash
   composer install
   php artisan serve
   ```

3. **Use the application**
   Now you can use the built in swagger feature for use the API that moves the rover or for rebuild the map with more or less space and obstacles.
   http://127.0.0.1:8000/api/documentation

---

## 🧑‍💻 How to Use

- The main API endpoints are:
  - `GET /api/rover/state` – Get the current planet and rover state.
  - `POST /api/rover/commands` – Send a command sequence to the rover (e.g., `MRMMMLMR`).
  - `POST /api/mars/configure` – Configure the planet size and obstacles.

Values accepted for the plannet configuration:
  - Width: `200` – The width of the planet (int).
  - Height: `200` – The height of the planet (int).
  - `0.2` – 20% of probability to create obstacles (float).

---

## 🧪 Running Tests

### Using PHP Locally

If you have PHP installed locally, run all backend tests with:
```bash
#with make
make test

#with docker
docker compose run --rm test

#in Local machine
php artisan test
```
---

## 🏗️ Architecture Notes

- **Hexagonal (Ports & Adapters):** The domain logic is decoupled from Laravel and the HTTP layer.
- **Domain:** All business rules and state transitions are in the domain layer.
- **Application:** Command parsing and orchestration are handled in the application layer.
- **Adapters:** HTTP controllers expose the API.
- **Data persistence:** Data is persisted in memory instead of using a database engine (it's an overkill from my point of view). I tried to use the session that laravel provides but I had some issues with the session ids (Swagger does not save any cookie ID in the browser I guess) so for be quick I decided to use the cache instead.
- **Testing:** In this kata i decided to make unit testing for the domain layer and integration testing for the controller that is the orchestrator of the application layer.


## 🧠 Developer notes

- **Tests:** Unit and feature/integration tests in `tests/Unit/Mars` and `tests/Feature`
- **Swagger:** Built-in swagger feature for improve the usage.
- **Docker:** Docker is used for development and testing
---



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
