# Login | Register Form

This repository contains a login and registration form implemented using the Minichan gRPC server library (v1).

## Getting Started

To get started, follow these steps:

1. **Database Setup:**
   - Create a MySQL database to store user information.

2. **Start the gRPC Server:**
   - Ensure that the gRPC server provided by the Minichan library is running and accessible.

3. **Run the Application:**
   - Execute `php app/App.php` to start the application.

## Features

- **Registration Form:**
  - Users can register by providing their first name, last name, email, password, and an optional profile image.

- **Login Form:**
  - Registered users can log in using their email and password.

- **Session Management:**
  - Sessions are used to keep users logged in across multiple requests.

## Folder Structure

- **app/:** Contains the application code.
- **app/views/:** Stores the BladeOne views for rendering HTML templates.
- **app/cache/:** Stores cached BladeOne views for faster rendering.

## Dependencies

- **Minichan gRPC Server (v1):** Provides the backend server for user authentication and registration.
- **eftec/bladeone:** Used for rendering HTML templates.

## Usage

- Access the registration form at `/register`.
- Access the login form at `/login`.

## License

This project is licensed under the [MIT License](LICENSE).
