# Notes App Backend
[![Ask DeepWiki](https://devin.ai/assets/askdeepwiki.png)](https://deepwiki.com/waleedghubara/Notes-App-Backend)

This repository contains the PHP backend for a simple notes application. It provides a RESTful API for user management and full CRUD (Create, Read, Update, Delete) functionality for notes, secured using a token-based authentication system.

## Features
*   User registration and login
*   Token-based authentication for secure endpoints
*   View user profile information
*   Create, view, edit, and delete notes
*   Image uploads for user profiles and notes
*   Input data sanitization

## Prerequisites
*   PHP 7.4 or higher
*   A web server (e.g., Apache, Nginx)
*   MySQL or MariaDB Database
*   PDO PHP Extension

## Setup

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/waleedghubara/notes-app-backend.git
    cd notes-app-backend
    ```

2.  **Database Configuration:**
    *   Create a new MySQL database named `api`.
    *   Update the database credentials in `connext.php` if they differ from the defaults:
        ```php
        $nameserversql = "mysql:host=localhost;dbname=api";
        $user = "root";
        $pass = "";
        ```

3.  **Database Schema:**
    *   Execute the following SQL queries in your `api` database to create the necessary tables.

    **`users` table:**
    ```sql
    CREATE TABLE `users` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `username` varchar(255) NOT NULL,
      `email` varchar(255) NOT NULL,
      `phone` varchar(50) NOT NULL,
      `age` int(11) NOT NULL,
      `password` varchar(255) NOT NULL,
      `profile` varchar(255) DEFAULT NULL,
      `token` varchar(255) DEFAULT NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ```
    **`notes` table:**
    ```sql
    CREATE TABLE `notes` (
      `notes_id` int(11) NOT NULL AUTO_INCREMENT,
      `titel` varchar(255) NOT NULL,
      `content` text NOT NULL,
      `notes_image` varchar(255) DEFAULT NULL,
      `users_id` int(11) NOT NULL,
      PRIMARY KEY (`notes_id`),
      KEY `users_id` (`users_id`),
      CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ```

4.  **Directory Permissions:**
    *   The application uploads images to an `upload/` directory. This directory will be created automatically at the root of the project. Ensure your web server has write permissions for this location.

## API Endpoints
All requests should be sent to the base URL of your deployed application (e.g., `http://localhost/notes-app-backend`). Protected endpoints require an `Authorization` header with the user's token: `Authorization: Bearer <YOUR_TOKEN>`.

---

### Authentication

#### Sign Up
*   **Endpoint:** `POST /auth/signup.php`
*   **Description:** Creates a new user account.
*   **Request Body:** `multipart/form-data`
    *   `username` (string, required)
    *   `email` (string, required)
    *   `phone` (string, required)
    *   `age` (integer, required)
    *   `password` (string, required)
    *   `profile` (file, optional): User's profile picture.
*   **Success Response:**
    ```json
    {
        "status": "create account successfully",
        "token": "generated_user_token"
    }
    ```

#### Login
*   **Endpoint:** `POST /auth/login.php`
*   **Description:** Authenticates a user and returns a token.
*   **Request Body:** `application/json` or `x-www-form-urlencoded`
    ```json
    {
        "email": "user@example.com",
        "password": "your_password"
    }
    ```
*   **Success Response:**
    ```json
    {
        "status": "success",
        "message": "login successfully",
        "token": "generated_user_token",
        "id": 1,
        "data": {
            "id": 1,
            "username": "testuser",
            "email": "user@example.com"
        }
    }
    ```

#### View Profile
*   **Endpoint:** `GET /auth/profile.php`
*   **Description:** Fetches the profile data for the authenticated user.
*   **Headers:** `Authorization: Bearer <YOUR_TOKEN>`
*   **Success Response:**
    ```json
    {
        "status": "success",
        "message": "تم جلب البيانات بنجاح",
        "data": {
            "id": 1,
            "username": "testuser",
            "phone": "1234567890",
            "email": "user@example.com",
            "age": 25,
            "profile": "profile.jpg"
        }
    }
    ```

---
### Notes

#### View Notes
*   **Endpoint:** `GET /notes/view.php`
*   **Description:** Retrieves all notes for a specific user.
*   **Headers:** `Authorization: Bearer <YOUR_TOKEN>`
*   **Query Parameters:**
    *   `id` (integer, required): The ID of the user whose notes you want to view.
*   **Success Response:**
    ```json
    {
        "status": "success",
        "message": "تم جلب الملاحظات بنجاح ",
        "count": 1,
        "data": [
            {
                "notes_id": 10,
                "titel": "My First Note",
                "content": "This is the content of the note.",
                "notes_image": "note_image.jpg",
                "users_id": 1
            }
        ]
    }
    ```

#### Add Note
*   **Endpoint:** `POST /notes/add.php`
*   **Description:** Creates a new note for the authenticated user.
*   **Headers:** `Authorization: Bearer <YOUR_TOKEN>`
*   **Request Body:** `multipart/form-data`
    *   `titel` (string, required)
    *   `content` (string, required)
    *   `image` (file, optional): An image to attach to the note.
*   **Success Response:**
    ```json
    {
        "status": "success",
        "message": "تم إضافة الملاحظة بنجاح"
    }
    ```

#### Edit Note
*   **Endpoint:** `POST /notes/edit.php`
*   **Description:** Updates an existing note.
*   **Headers:** `Authorization: Bearer <YOUR_TOKEN>`
*   **Request Body:** `application/json` or `x-www-form-urlencoded`
    ```json
    {
        "id": 10,
        "titel": "Updated Note Title",
        "content": "Updated content for the note."
    }
    ```
*   **Success Response:**
    ```json
    {
        "status": "success",
        "message": "تم تعديل الملاحظة بنجاح",
        "data": {
            "notes_id": "10",
            "titel": "Updated Note Title",
            "content": "Updated content for the note."
        }
    }
    ```

#### Delete Note
*   **Endpoint:** `POST /notes/delete.php`
*   **Description:** Deletes a specific note.
*   **Headers:** `Authorization: Bearer <YOUR_TOKEN>`
*   **Request Body:** `application/json` or `x-www-form-urlencoded`
    ```json
    {
        "id": 10,
        "imagename": "note_image.jpg"
    }
    ```
    *   `id` (integer, required): The ID of the note to delete.
    *   `imagename` (string, required if note has an image): The filename of the image to delete from the server.
*   **Success Response:**
    ```json
    {
        "status": "success",
        "message": "تم حذف الملاحظة بنجاح"
    }
