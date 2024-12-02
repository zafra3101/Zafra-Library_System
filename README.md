# Library Management System
---
## Description
This is a RESTful API for managing users, books, and authors in the library system. It includes user authentication via JWT tokens and endpoints for managing book and author data.

The API provides endpoints for registering users, logging them in, and managing book and author data, while utilizing JWT-based authentication for security.

## Features
- User Registration (`POST /user/register`)
- User Authentication (`POST /user/authenticate`)
- Add Book and Author (`POST /book-author/insert`)
- Retrieve Books and Authors (`GET /book-author/retrieve`)
- Update Book and Author Information (`PUT /book-author/update/{id}`)
- Delete Book and Author (`DELETE /book-author/delete/{id}`)
- 
## Requirements

- PHP 7.4 or later
- Composer for managing dependencies
- MySQL database
- Slim Framework
- Firebase JWT library
- ThunderClient (for API testing)

# Payloads

## Registration

Method: POST

URL: /user/register

Request:
```
{
  "username": "your_username",
  "password": "your_password"
}
```

Response:
---
  Success:
  ```
    {
    "status": "success"
    }
  ```
  Fail:
  ```
    {
    "status": "fail",
    "error": "error_message"
    }
  ```
---
## Authentication (Login)

Method: POST

URL: /user/authernticate

Request:
```
{
  "username": "your_username",
  "password": "your_password"
}
```

Response:
---
  Success:
  ```
    {
    "status": "success",
    "token": "jwt_token"
    }
  ```
  Fail:
  ```
    {
    "status": "fail",
    "error": "Authentication Failed"
    }
  ```
---
## Add Book & Author

Method: POST

URL: /book-author/insert

Request:
```
{
  "bookTitle": "Book Title",
  "authorName": "Author Name"
}
```

Response:
---
  Success:
  ```
    {
    "status": "success",
    "token": "new_jwt_token"
    }
  ```
  Fail:
  ```
    {
    "status": "fail",
    "data": {
      "title": "error_message"
      }
    }

  ```
  Fail (Expired/Repeated Token):
  ```
    {
    "status": "fail",
    "data": {
      "title": "Token is invalid or has been used already."
      }
    }
  ```
---
## Get Book List Info

Method: GET

URL: /book-author/retrieve

Response:
---
  Success:
  ```
      {
    "status": "success",
    "data": [
      {
        "book_id": 1,
        "title": "Book Title",
        "author_name": "Author Name"
      },
        ...
            ],
        "token": "new_jwt_token"
      }

  ```
  Fail:
  ```
    {
    "status": "fail",
    "data": {
      "title": "error_message"
      }
    }
  ```
  Fail (Expired/Repeated Token):
  ```
    {
    "status": "fail",
    "data": {
      "title": "Token is invalid or has been used already."
      }
    }
  ```
---
## Update Book and Author Info

Method: PUT

URL: /book-author/update/{id}

Request:
```
{
  "bookTitle": "Updated Book Title",
  "authorName": "Updated Author Name"
}
```

Response:
---
  Success:
  ```
     {
      "status": "success",
      "message": "Book and author updated successfully.",
      "token": "new_jwt_token"
    }

  ```
  Fail:
  ```
    {
    "status": "fail",
    "data": {
      "title": "error_message"
      }
    }
  ```
  Fail (Expired/Repeated Token):
  ```
    {
      "status": "fail",
      "data": {
      "title": "Token is invalid or has been used already."
      }
    }
  ```
---
## Delete Book Associated to the Author

Method: DELETE

URL: /book-author/delete/{id}

Response:
---
  Success:
  ```
  {
  "status": "success",
  "message": "Book and associated author deleted successfully.",
  "token": "new_jwt_token"
  }
  ```
  Fail:
  ```
    {
    "status": "fail",
    "data": {
    "title": "error_message"
      }
    }
  ```
  Fail (Expired/Repeated Token):
  ```
    {
    "status": "fail",
    "data": {
      "title": "Token is invalid or has been used already."
      }
    }
  ```
---
# Developer Contact

FaceBook: Marvee O. Zafra

Contact: zaframarvee32@gmail.com / mzafra31012@student.dmmmsu.edu.ph
