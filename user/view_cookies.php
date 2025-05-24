<?php
// The header already starts the session
require_once __DIR__ . '/../tools/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Cookies</title>
    <!-- You might want to link your main CSS file here if it's not included in the header -->
    <!-- <link rel="stylesheet" href="../css/templatemo_style.css"> -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }
        .main-content {
            /* Adjust padding to account for your fixed sidebar if necessary */
            padding: 20px;
            margin-left: 280px; /* Assuming sidebar width, adjust as needed */
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
        }
        .no-cookies {
            color: #777;
            font-style: italic;
        }
    </style>
</head>
<body>

    <!-- Sidebar is included by header.php -->
    <!-- <div class="col-md-4 col-sm-12"> ... sidebar html ... </div> -->

    <div class="col-md-8 col-sm-12"> <!-- This class is typical for main content next to a sidebar -->
        <div class="main-content">
            <div class="container">
                <h1>Current Cookie Data</h1>
                <?php if (!empty($_COOKIE)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Cookie Name</th>
                                <th>Cookie Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($_COOKIE as $name => $value): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($name); ?></td>
                                    <td><?php echo htmlspecialchars($value); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="no-cookies">No cookies are currently set for this domain.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

<?php
require_once __DIR__ . '/../tools/footer.php';
?>
</body>
</html>