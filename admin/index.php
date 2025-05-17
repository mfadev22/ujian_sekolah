<?php
session_start();
require 'config.php';

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    // Prepared statement untuk keamanan
    $stmt = $conn->prepare("SELECT * FROM admin_users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Username atau password salah!";
    }

    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<title>Login Admin</title>
	<script src="https://cdn.tailwindcss.com"></script>
		<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>

<body class="bg-gray-100 flex items-center justify-center h-screen font-['Poppins']">
	<div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
		<h2 class="text-2xl font-bold mb-6 text-center">Login Guru</h2> <?php if (isset($error)) : ?> <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4"> <?= $error ?> </div> <?php endif; ?> <form method="POST" class="space-y-4">
			<div>
				<label class="block text-gray-700">Username</label>
				<input type="text" name="username" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:border-blue-300">
			</div>
			<div>
				<label class="block text-gray-700">Password</label>
				<input type="password" name="password" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:border-blue-300">
			</div>
			<button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">Login</button>
		</form>
		<!-- Link kembali ke halaman murid -->
		<div class="mt-12 text-center">
			<a href="../" class="inline-block bg-gray-200 text-gray-800 px-5 py-2 rounded hover:bg-gray-300 hover:text-black transition"> ← Kembali Sebagai Murid </a>
		</div>
	</div>
</body>

</html>