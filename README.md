# VCC Web Project Repository

This repository contains a collection of web projects developed by Vahid Can Culfa, including a PHP-based Admin Panel and two interactive browser games: Chess and Sudoku.

## 📁 Project Structure

The repository is structured as follows, grouping related files for the Admin Panel and the games:

| File Name | Description | Category |
| :--- | :--- | :--- |
| `index.php` | Login page for the Admin Panel. Handles user authentication. | Admin Panel |
| `home.php` | Main page of the Admin Panel. Includes dynamic content, map integration (Leaflet), and a comments section. | Admin Panel |
| `logout.php` | Handles session termination and redirects to the login page. | Admin Panel |
| `chess.html` | The main HTML file for the VCC Chess game. | Chess Game |
| `chess.js` | The JavaScript logic for the Chess game, including game modes (2-player and AI) and move calculations. | Chess Game |
| `sudoku.html` | The main HTML file for the VCC Sudoku game. | Sudoku Game |
| `sudoku.js` | The JavaScript logic for the Sudoku game, handling generation, solving, and user interaction. | Sudoku Game |

## 1. 🖥️ Admin Panel

The Admin Panel is a simple web application developed with **PHP** for backend functionality and **MySQL/MariaDB** (via PDO in `home.php`) for data management.

### Key Features:
* **Secure Login:** Basic session-based authentication using predefined credentials in `index.php`.
* **Dynamic Content:** Displays comments and markers fetched from a database.
* **Map Integration:** Utilizes **Leaflet.js** to display markers on a map within the dashboard.
* **Comments Management:** Allows users to post and delete comments.
* **Theme Toggle:** Includes a local storage-based dark/light theme switch.

### Setup (Admin Panel)

1.  **Database:** A MySQL/MariaDB database is required with the name `????`.
2.  **Credentials:** Update the database connection details in `home.php`:
    ```php
    $db = new PDO('mysql:host=localhost;dbname=????????', '??????', '??????');
    ```
3.  **Authentication:** The current hardcoded login credentials are:
    * **Username:** `admin`
    * **Password:** `123456`

## 2. ♟️ VCC Chess Game

A functional Chess game implemented purely with **HTML, CSS, and JavaScript**.

### Key Features:
* **Two Game Modes:** Play against a local opponent or an AI (with adjustable difficulty).
* **Move Validation:** Standard chess move rules are enforced.
* **Game History:** Tracks and displays a list of moves made during the game.

## 3. 🔢 VCC Sudoku Game

A browser-based Sudoku puzzle game, also implemented using **HTML, CSS, and JavaScript**.

### Key Features:
* **Difficulty Levels:** Generates puzzles based on difficulty (easy, medium, hard).
* **Interactive Input:** Allows number input via keyboard and cell selection.
* **Validation:** Checks the user's solution against the correct solution.
* **Responsive Design:** Styled to be playable on different screen sizes.

---

## 🛠️ Technologies Used

* **Backend:** PHP (Native), PDO
* **Database:** MySQL / MariaDB
* **Frontend:** HTML5, CSS3, JavaScript
* **Libraries/Frameworks:**
    * **Leaflet.js:** For map display in the Admin Panel.
    * **VanillaTilt.js:** Used for subtle 3D hover effects on the login page (`index.php`).
    * **particles.js:** For the background particle effect on the login page (`index.php`).
