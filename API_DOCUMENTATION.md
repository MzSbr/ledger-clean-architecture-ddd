# API Documentation - Ledger App Clean Architecture

This document provides detailed information about the API endpoints available in the Ledger Clean Architecture library.

## Base URL

All API endpoints are relative to your application's base URL.

## Authentication

The API endpoints do not include authentication by default. You should implement authentication according to your application's requirements.

## Response Format

All responses are in JSON format. Successful responses typically include the requested data or a success message. Error responses include an error message and appropriate HTTP status code.

## Account Endpoints

### List Accounts

Retrieves a list of all accounts, optionally filtered by parent ID.

- **URL**: `/api/accounts`
- **Method**: `GET`
- **Query Parameters**:
  - `parent_id` (optional): UUID of the parent account to filter by
- **Success Response**:
  - **Code**: 200
  - **Content**:
    ```json
    [
      {
        "id": "f47ac10b-58cc-4372-a567-0e02b2c3d479",
        "code": "ASSET-1000",
        "type": "asset",
        "status": "active",
        "parentId": null,
        "isCategory": false,
        "taxCode": "TAX001",
        "extra": {"department": "sales"}
      },
      ...
    ]
    ```

### Create Account

Creates a new account.

- **URL**: `/api/accounts`
- **Method**: `POST`
- **Request Body**:
  ```json
  {
    "code": "ASSET-1000",
    "type": "asset",
    "parent_id": null,
    "is_category": false,
    "tax_code": "TAX001",
    "extra": {"department": "sales"}
  }
  ```
- **Required Fields**:
  - `code`: String (max 50 characters)
  - `type`: String (one of: asset, liability, equity, revenue, expense)
- **Optional Fields**:
  - `parent_id`: UUID of parent account
  - `is_category`: Boolean
  - `tax_code`: String (max 50 characters)
  - `extra`: Object
- **Success Response**:
  - **Code**: 201
  - **Content**:
    ```json
    {
      "id": "f47ac10b-58cc-4372-a567-0e02b2c3d479",
      "message": "Account created successfully"
    }
    ```
- **Error Responses**:
  - **Code**: 400
  - **Content**:
    ```json
    {
      "message": "The code field is required.",
      "errors": {
        "code": ["The code field is required."]
      }
    }
    ```

### Get Account

Retrieves details of a specific account.

- **URL**: `/api/accounts/{id}`
- **Method**: `GET`
- **URL Parameters**:
  - `id`: UUID of the account
- **Success Response**:
  - **Code**: 200
  - **Content**:
    ```json
    {
      "id": "f47ac10b-58cc-4372-a567-0e02b2c3d479",
      "code": "ASSET-1000",
      "type": "asset",
      "status": "active",
      "parentId": null,
      "isCategory": false,
      "taxCode": "TAX001",
      "extra": {"department": "sales"}
    }
    ```
- **Error Responses**:
  - **Code**: 404
  - **Content**:
    ```json
    {
      "message": "Account not found",
      "error": "account_not_found"
    }
    ```

### Update Account

Updates an existing account.

- **URL**: `/api/accounts/{id}`
- **Method**: `PUT`
- **URL Parameters**:
  - `id`: UUID of the account
- **Request Body**:
  ```json
  {
    "code": "ASSET-1001",
    "tax_code": "TAX002",
    "extra": {"department": "marketing"}
  }
  ```
- **Required Fields**:
  - `code`: String (max 50 characters)
- **Optional Fields**:
  - `tax_code`: String (max 50 characters)
  - `extra`: Object
- **Success Response**:
  - **Code**: 200
  - **Content**:
    ```json
    {
      "message": "Account updated successfully"
    }
    ```
- **Error Responses**:
  - **Code**: 404
  - **Content**:
    ```json
    {
      "message": "Account not found",
      "error": "account_not_found"
    }
    ```
  - **Code**: 400
  - **Content**:
    ```json
    {
      "message": "Account is closed",
      "error": "account_closed"
    }
    ```

### Close Account

Closes an existing account.

- **URL**: `/api/accounts/{id}/close`
- **Method**: `POST`
- **URL Parameters**:
  - `id`: UUID of the account
- **Success Response**:
  - **Code**: 200
  - **Content**:
    ```json
    {
      "message": "Account closed successfully"
    }
    ```
- **Error Responses**:
  - **Code**: 404
  - **Content**:
    ```json
    {
      "message": "Account not found",
      "error": "account_not_found"
    }
    ```

## Journal Entry Endpoints

### List Journal Entries

Retrieves a list of journal entries, optionally filtered by type, status, and date range.

- **URL**: `/api/journal-entries`
- **Method**: `GET`
- **Query Parameters**:
  - `type` (optional): String (one of: regular, adjustment, closing, opening, reversal)
  - `status` (optional): String (one of: draft, posted, reversed)
  - `from_date` (optional): Date (format: YYYY-MM-DD)
  - `to_date` (optional): Date (format: YYYY-MM-DD)
- **Success Response**:
  - **Code**: 200
  - **Content**:
    ```json
    [
      {
        "id": "f47ac10b-58cc-4372-a567-0e02b2c3d479",
        "type": "regular",
        "status": "draft",
        "description": "Purchase of office supplies",
        "date": "2025-04-18 00:00:00",
        "currency": "USD",
        "reversalOf": null
      },
      ...
    ]
    ```

### Create Journal Entry

Creates a new journal entry.

- **URL**: `/api/journal-entries`
- **Method**: `POST`
- **Request Body**:
  ```json
  {
    "type": "regular",
    "description": "Purchase of office supplies",
    "date": "2025-04-18",
    "currency": "USD",
    "reversal_of": null,
    "extra": {"reference": "INV-001"}
  }
  ```
- **Required Fields**:
  - `type`: String (one of: regular, adjustment, closing, opening, reversal)
  - `description`: String (max 255 characters)
  - `date`: Date (format: YYYY-MM-DD)
  - `currency`: String (3 characters)
- **Optional Fields**:
  - `reversal_of`: UUID of the journal entry being reversed
  - `extra`: Object
- **Success Response**:
  - **Code**: 201
  - **Content**:
    ```json
    {
      "id": "f47ac10b-58cc-4372-a567-0e02b2c3d479",
      "message": "Journal entry created successfully"
    }
    ```
- **Error Responses**:
  - **Code**: 400
  - **Content**:
    ```json
    {
      "message": "The description field is required.",
      "errors": {
        "description": ["The description field is required."]
      }
    }
    ```

### Get Journal Entry

Retrieves details of a specific journal entry, including its details.

- **URL**: `/api/journal-entries/{id}`
- **Method**: `GET`
- **URL Parameters**:
  - `id`: UUID of the journal entry
- **Success Response**:
  - **Code**: 200
  - **Content**:
    ```json
    {
      "id": "f47ac10b-58cc-4372-a567-0e02b2c3d479",
      "type": "regular",
      "status": "draft",
      "description": "Purchase of office supplies",
      "date": "2025-04-18 00:00:00",
      "currency": "USD",
      "reversalOf": null,
      "extra": {"reference": "INV-001"},
      "details": [
        {
          "id": "a47ac10b-58cc-4372-a567-0e02b2c3d479",
          "accountId": "b47ac10b-58cc-4372-a567-0e02b2c3d479",
          "amount": 1000,
          "currency": "USD",
          "memo": "Debit entry",
          "extra": null
        },
        {
          "id": "c47ac10b-58cc-4372-a567-0e02b2c3d479",
          "accountId": "d47ac10b-58cc-4372-a567-0e02b2c3d479",
          "amount": -1000,
          "currency": "USD",
          "memo": "Credit entry",
          "extra": null
        }
      ]
    }
    ```
- **Error Responses**:
  - **Code**: 404
  - **Content**:
    ```json
    {
      "message": "Journal entry not found",
      "error": "journal_entry_not_found"
    }
    ```

### Add Journal Detail

Adds a detail to an existing journal entry.

- **URL**: `/api/journal-entries/{id}/details`
- **Method**: `POST`
- **URL Parameters**:
  - `id`: UUID of the journal entry
- **Request Body**:
  ```json
  {
    "account_id": "b47ac10b-58cc-4372-a567-0e02b2c3d479",
    "amount": 1000,
    "currency": "USD",
    "memo": "Debit entry",
    "extra": null
  }
  ```
- **Required Fields**:
  - `account_id`: UUID of the account
  - `amount`: Integer (positive for debit, negative for credit)
  - `currency`: String (3 characters)
- **Optional Fields**:
  - `memo`: String
  - `extra`: Object
- **Success Response**:
  - **Code**: 201
  - **Content**:
    ```json
    {
      "id": "a47ac10b-58cc-4372-a567-0e02b2c3d479",
      "message": "Journal detail added successfully"
    }
    ```
- **Error Responses**:
  - **Code**: 404
  - **Content**:
    ```json
    {
      "message": "Journal entry not found",
      "error": "journal_entry_not_found"
    }
    ```
  - **Code**: 400
  - **Content**:
    ```json
    {
      "message": "Journal entry already posted",
      "error": "journal_entry_already_posted"
    }
    ```

### Post Journal Entry

Posts a journal entry, making it immutable and updating account balances.

- **URL**: `/api/journal-entries/{id}/post`
- **Method**: `POST`
- **URL Parameters**:
  - `id`: UUID of the journal entry
- **Success Response**:
  - **Code**: 200
  - **Content**:
    ```json
    {
      "message": "Journal entry posted successfully"
    }
    ```
- **Error Responses**:
  - **Code**: 404
  - **Content**:
    ```json
    {
      "message": "Journal entry not found",
      "error": "journal_entry_not_found"
    }
    ```
  - **Code**: 400
  - **Content**:
    ```json
    {
      "message": "Journal entry already posted",
      "error": "journal_entry_already_posted"
    }
    ```
  - **Code**: 400
  - **Content**:
    ```json
    {
      "message": "Journal entry not balanced",
      "error": "journal_entry_not_balanced"
    }
    ```

### Reverse Journal Entry

Creates a reversal of an existing journal entry.

- **URL**: `/api/journal-entries/{id}/reverse`
- **Method**: `POST`
- **URL Parameters**:
  - `id`: UUID of the journal entry to reverse
- **Request Body**:
  ```json
  {
    "description": "Reversal of journal entry",
    "date": "2025-04-19"
  }
  ```
- **Required Fields**:
  - `description`: String (max 255 characters)
  - `date`: Date (format: YYYY-MM-DD)
- **Success Response**:
  - **Code**: 200
  - **Content**:
    ```json
    {
      "id": "g47ac10b-58cc-4372-a567-0e02b2c3d479",
      "message": "Journal entry reversed successfully"
    }
    ```
- **Error Responses**:
  - **Code**: 404
  - **Content**:
    ```json
    {
      "message": "Journal entry not found",
      "error": "journal_entry_not_found"
    }
    ```
  - **Code**: 400
  - **Content**:
    ```json
    {
      "message": "Journal entry already reversed",
      "error": "journal_entry_already_reversed"
    }
    ```

## Error Handling

The API uses standard HTTP status codes to indicate the success or failure of a request:

- `200 OK`: The request was successful
- `201 Created`: A new resource was successfully created
- `400 Bad Request`: The request was invalid or cannot be served
- `404 Not Found`: The requested resource does not exist
- `500 Internal Server Error`: An error occurred on the server

Error responses include a message describing the error and an error code for programmatic handling.
