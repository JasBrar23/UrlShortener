# URL Shortener

This project is a UrlShortener built with Laravel. It allows you to shorten long URLs and retrieve the original URLs from the
shortened versions. The service provides two endpoints: `/encode` for shortening URLs and `/decode` for retrieving the original URLs.

## Features

- **Encode URLs**: Convert long URLs into short URLs.
- **Decode URLs**: Retrieve original URLs from short URLs.
- **Memory or Database Storage**: Choose between in-memory or database storage for URL mappings.
- **JSON API**: Interact with the service via JSON-based API endpoints.

## Installation

Follow these steps to set up the project on your local machine:

1. **Clone the repository:**

   ```bash
   git clone https://github.com/JasBrar23/UrlShortener.git
   cd UrlShortener
   ```

2. **Install dependencies::**

    ```bash
   composer install
   ```

3. Copy the .env file:

   ```bash
    cp .env.example .env
   ```

4. Generate an application key:

    ```bash
   php artisan key:generate
   ```

5. Run migrations:

    ```bash
   php artisan migrate
   ```

6. Start the development server:

    ```bash
   php artisan serve
   ```

## Endpoints

### Encode URL

* URL: /encode
* Method: GET
* Parameters:
    * url (required): The original URL to be shortened.
* Response:
    * short_url: The shortened URL.

Example Request:

  ```bash
  GET /encode?url=https://www.example.com
  ```

Example Response:

 ```json
    {
      "short_url": "http://example.com/abc123"
    }
 ```

* URL: /decode
* Method: GET
* Parameters:
    * short_url (required): The original URL to be shortened.
* Response:
    * url: The original URL if found.
    * error: An error message if the short URL is not found.

Example Request:

  ```bash
  GET /decode?short_url=http://example.com/abc123
  ```

Example Response:

 ```json
    {
      "url": "https://www.example.com"
    }
 ```

## Running Tests

### To run the tests, use the following command:

  ```bash
  php artisan test
  ```
