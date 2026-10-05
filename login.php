<?php
session_start();
require_once "config/db.php";

if (isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit();
}

$error = "";
$registered = isset($_GET["registered"]);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user["password"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];
            header("Location: dashboard.php");
            exit();
        }
    }

    $error = "Invalid email or password.";
}

$page_title = "Login | WaterWise";
include "includes/header.php";
include "includes/navbar.php";
?>
<div class="container py-5">
    <div class="form-card mx-auto">
        <h2>Login</h2>
        <?php if ($registered): ?>
            <div class="alert alert-success">Registration successful. Please login.</div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button class="btn btn-primary w-100">Login</button>
        </form>
    </div>
</div>
<?php include "includes/footer.php"; ?>
