<?php
session_start();

// Kalau dah login, terus masuk dashboard
if (isset($_SESSION['admin_logged_in'])) {
    header("Location: admin.php");
    exit();
}

// Logic check password
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // USERNAME & PASSWORD ADMIN (Boleh tukar kat sini)
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin.php");
        exit();
    } else {
        $error = "Username atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | SafeCampus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --uitm-blue: #003366;
            --uitm-yellow: #F7C948;
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
            --shadow-soft: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }

        body {
            background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
            overflow: hidden;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            box-shadow: var(--shadow-soft);
            padding: 40px;
            color: white;
            transition: transform 0.3s ease;
        }

        .glass-card:hover {
            transform: translateY(-5px);
        }

        .logo-img {
            width: 100px;
            height: auto;
            margin-bottom: 15px;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.3));
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .logo-img:hover {
            transform: scale(1.1) rotate(5deg);
        }

        .form-control {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: white;
            height: 50px;
            padding-left: 20px;
            transition: all 0.3s ease;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.3);
            border-color: var(--uitm-yellow);
            box-shadow: 0 0 15px rgba(247, 201, 72, 0.3);
            color: white;
        }

        .btn-warning {
            background: linear-gradient(135deg, #F7C948 0%, #e0b020 100%);
            border: none;
            border-radius: 10px;
            font-weight: 700;
            color: #003366;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 12px;
            box-shadow: 0 4px 15px rgba(247, 201, 72, 0.4);
            transition: all 0.3s ease;
        }

        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(247, 201, 72, 0.6);
            background: linear-gradient(135deg, #ffdb70 0%, #f0c040 100%);
            color: #002244;
        }

        .alert-glass {
            background: rgba(220, 53, 69, 0.2);
            border: 1px solid rgba(220, 53, 69, 0.5);
            color: #ffb3b3;
            backdrop-filter: blur(5px);
            border-radius: 10px;
        }

        .badge-custom {
            background: rgba(247, 201, 72, 0.2);
            border: 1px solid var(--uitm-yellow);
            color: var(--uitm-yellow);
            letter-spacing: 2px;
            padding: 5px 12px;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="glass-card text-center">
                    <div class="mb-4">
                        <img src="logo.png" alt="SafeCampus Logo" class="logo-img rounded-circle">
                        <h3 class="fw-bold mt-2 mb-1" style="color: white; text-shadow: 0 2px 4px rgba(0,0,0,0.5);">
                            SafeCampus</h3>
                        <span class="badge badge-custom rounded-pill">ADMIN PORTAL</span>
                    </div>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-glass text-center p-2 small mb-4">
                            <i class="fas fa-exclamation-circle me-1"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="text-start">
                        <div class="mb-3">
                            <label class="form-label small text-white-50 fw-bold ms-1">USERNAME</label>
                            <input type="text" name="username" class="form-control" placeholder="Key in username"
                                required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small text-white-50 fw-bold ms-1">PASSWORD</label>
                            <input type="password" name="password" class="form-control" placeholder="Key in password"
                                required>
                        </div>
                        <button type="submit" name="login" class="btn btn-warning w-100 py-3 mb-2">
                            LOGIN
                        </button>
                    </form>

                    <div class="mt-4">
                        <small class="text-white-50" style="font-size: 0.75rem;">© 2026 Universiti Teknologi
                            MARA</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>