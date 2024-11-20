<?php

require_once('./connection.php');

$id = $_GET['id'];

$stmt = $pdo->prepare('SELECT * FROM books WHERE id = :id');
$stmt->execute(['id' => $id]);
$book = $stmt->fetch();

$bookAuthorsStmt = $pdo->prepare('SELECT a.id, a.first_name, a.last_name FROM book_authors ba LEFT JOIN authors a ON ba.author_id = a.id WHERE ba.book_id = :id');
$bookAuthorsStmt->execute(['id' => $id]);

$availableAuthorsStmt = $pdo->prepare('SELECT * FROM authors WHERE id NOT IN(SELECT author_id FROM book_authors WHERE book_id = :book_id)');
$availableAuthorsStmt->execute(['book_id' => $id]); 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        nav {
            background-color: #333;
            padding: 10px;
        }

        nav a {
            color: white;
            text-decoration: none;
            font-size: 16px;
        }

        nav a:hover {
            text-decoration: underline;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-top: 20px;
        }

        form {
            width: 50%;
            margin: 20px auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        form label {
            display: block;
            font-size: 16px;
            margin-bottom: 8px;
        }

        form input[type="text"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        form input[type="submit"] {
            padding: 10px 20px;
            border: none;
            background-color: #4CAF50;
            color: white;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
        }

        form input[type="submit"]:hover {
            background-color: #45a049;
        }

        h3 {
            text-align: center;
            color: #333;
        }

        ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
            text-align: center;
        }

        li {
            display: inline-block;
            margin: 10px;
            background-color: #f9f9f9;
            padding: 10px 20px;
            border-radius: 5px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
        }

        li form {
            display: inline;
        }

        li button {
            background: none;
            border: none;
            color: red;
            cursor: pointer;
        }

        select {
            padding: 8px;
            font-size: 14px;
            margin-right: 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        button[type="submit"] {
            padding: 8px 16px;
            font-size: 14px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <nav>
        <a href="./book.php?id=<?= $id; ?>">Back</a>
    </nav>
    <h1>Edit Book</h1>
    
    <form action="./update_book.php?id=<?= $id; ?>" method="post">
        <label for="title">Title:</label>
        <input type="text" name="title" value="<?= $book['title']; ?>" id="title" required>
        
        <label for="price">Price:</label>
        <input type="text" name="price" value="<?= $book['price']; ?>" id="price" required>

        <input type="submit" name="action" value="Save">
    </form>

    <h3>Authors:</h3>
    <ul>
        <?php while ($author = $bookAuthorsStmt->fetch()) { ?>
            <li>
                <form action="./remove_author.php?id=<?= $id; ?>" method="post" style="display:inline;">
                    <?= $author['first_name']; ?>
                    <?= $author['last_name']; ?>
                    <button type="submit" name="action" value="remove_author">
                        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 30 30" style="vertical-align: middle; margin-left: 8px;">
                            <path d="M 14.984375 2.4863281 A 1.0001 1.0001 0 0 0 14 3.5 L 14 4 L 8.5 4 A 1.0001 1.0001 0 0 0 7.4863281 5 L 6 5 A 1.0001 1.0001 0 1 0 6 7 L 24 7 A 1.0001 1.0001 0 1 0 24 5 L 22.513672 5 A 1.0001 1.0001 0 0 0 21.5 4 L 16 4 L 16 3.5 A 1.0001 1.0001 0 0 0 14.984375 2.4863281 z M 6 9 L 7.7929688 24.234375 C 7.9109687 25.241375 8.7633438 26 9.7773438 26 L 20.222656 26 C 21.236656 26 22.088031 25.241375 22.207031 24.234375 L 24 9 L 6 9 z"></path>
                        </svg>
                    </button>
                    <input type="hidden" name="author_id" value="<?= $author['id']; ?>">
                </form>
            </li>
        <?php } ?>
    </ul>

    <form action="./add_author.php" method="post" style="text-align: center;">
        <input type="hidden" name="book_id" value="<?= $id; ?>">

        <select name="author_id">
            <option value=""></option>
            <?php while ($author = $availableAuthorsStmt->fetch()) { ?>
                <option value="<?= $author['id']; ?>">
                    <?= $author['first_name']; ?>
                    <?= $author['last_name']; ?>
                </option>
            <?php } ?>   
        </select>
        <button type="submit" name="action" value="add_author">
            Add Author
        </button>
    </form>
</body>
</html>
