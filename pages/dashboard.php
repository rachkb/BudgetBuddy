<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <link href="https://cdn.tailwindcss.com" rel="stylesheet" />
</head>
<body class="bg-gray-100 flex flex-col items-center justify-center h-screen space-y-4">
  <h1 class="text-3xl font-bold">Welcome, <?php echo $_SESSION['name']; ?></h1>
  <a href="../auth/logout.php" class="bg-red-500 text-white px-4 py-2 rounded">Logout</a>
</body>
</html>