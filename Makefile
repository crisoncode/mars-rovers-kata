.PHONY: help build up down test shell install

# Default target
help:
	@echo "Available commands:"
	@echo "  make build       - Build the Docker images"
	@echo "  make up          - Start the application"
	@echo "  make down        - Stop the application"
	@echo "  make test        - Run all tests"
	@echo "  make shell       - Open a shell in the app container"

# Build the Docker images
build:
	docker compose build

# Start the application
up:
	docker compose up -d

# Stop the application
down:
	docker compose down

# Run all tests
test:
	docker compose run --rm test

# Open a shell in the app container
shell:
	docker compose run --rm app bash
