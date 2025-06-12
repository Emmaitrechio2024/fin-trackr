
# 💸 Expense Tracker Web App

A full-featured, user-friendly Expense Tracker built with PHP, MySQL, HTML, and CSS. It allows users to register, log in, and track their income and expenses, with a clean dashboard and detailed transaction history.

---

## 🔧 Features

- 🔐 User Authentication (Register, Login, Logout)
- 💰 Add and manage Income and Expenses
- 📊 Displays total balance, income, and expenses
- 🧾 Full transaction history (separate page)
- 🗃️ Pagination, Search, and Date Filtering for transaction records
- 🟢 Green for income / 🔴 Red for expenses
- 🗑️ Delete individual transactions
- 🧠 Secure input handling with prepared statements
- 📱 Responsive and clean UI design

---

## 🧑‍💻 Tech Stack

- **Frontend:** HTML, CSS
- **Backend:** PHP (Core PHP, no frameworks)
- **Database:** MySQL
- **Server:** XAMPP / LAMP / WAMP (local development)

---

## 📂 Folder Structure

```

Expense Tracker/
├── style.css
├── connect.php
├── index.php
├── register.php
├── login.php
├── logout.php
├── transactions.php
├── README.md
└── expense_tracker.sql 

````

---

## 🚀 Getting Started

1. **Clone the Repository**

```bash
git clone https://github.com/Emmaitrechio2024/fin-trackr.git
````

2. **Import the Database**

* Open `phpMyAdmin`
* Create a new database: `expense_tracker`
* Import the `expense_tracker.sql` file

3. **Configure Database**

Edit `includes/db_connect.php` to match your database credentials:

```php
$host = 'localhost';
$user = 'root';
$password = '';
$db = 'expense_tracker';
```

4. **Start Server**

* Start Apache and MySQL via XAMPP/WAMP
* Open `http://localhost/Expense%20Tracker/` in your browser

---

## 📅 Future Enhancements

* Monthly/Yearly reports
* CSV export of transaction history
* Edit transaction support
* Dark mode toggle

---

## 👨‍💻 Author

**Emmanuel Itrechio**

---

## ⚖️ License
This project is free to use and modify. No license required.

---

## 🔗 Live Link : fin-trackr.infinityfreeapp.com
