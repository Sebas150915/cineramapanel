<?php
require_once 'config/config.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['usuario']);
    $password = $_POST['password'];

    // Verify CSRF Token
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $error = "Error de seguridad (CSRF). Por favor recargue la página.";
    } elseif (empty($username) || empty($password)) {
        $error = "Por favor ingrese usuario y contraseña.";
    } else {
        try {
            $stmt = $db->prepare("SELECT * FROM tbl_usuarios WHERE usuario = ? AND estado = '1'");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Strict Role Check: 'cajero' NOT allowed in Panel
                if ($user['rol'] === 'cajero') {
                    $error = "Acceso denegado. Los cajeros deben usar el módulo POS.";
                } else {
                    // Login Success
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['usuario'] = $user['usuario'];
                    $_SESSION['nombre'] = $user['nombre'];
                    $_SESSION['rol'] = $user['rol'];
                    $_SESSION['id_local'] = $user['id_local'];

                    if ($user['id_local']) {
                        $lStmt = $db->prepare("SELECT nombre FROM tbl_locales WHERE id = ?");
                        $lStmt->execute([$user['id_local']]);
                        $_SESSION['local_nombre'] = $lStmt->fetchColumn();
                    }

                    $_SESSION['permiso_boleteria'] = $user['permiso_boleteria'];
                    $_SESSION['permiso_dulceria'] = $user['permiso_dulceria'];

                    // Regenerate session ID to prevent fixation
                    session_regenerate_id(true);

                    header("Location: " . BASE_URL . "index.php");
                    exit;
                }
            } else {
                $error = "Usuario o contraseña incorrectos.";
            }
        } catch (PDOException $e) {
            // General error message for security
            $error = "Error de sistema. Intente nuevamente.";
            error_log($e->getMessage()); // Log actual error
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinerama Administrador</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    *{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{

    font-family:'Poppins',sans-serif;
    min-height:100vh;

    background:
    linear-gradient(
        rgba(0,0,0,.78),
        rgba(0,0,0,.88)
    ),
    url('assets/img/login-bg.jpg');

    background-size:cover;
    background-position:center;
    background-repeat:no-repeat;

    display:flex;
    align-items:center;
    justify-content:space-between;

    padding:0 100px;

    overflow:hidden;
}

/* Overlay Oscuro */

.overlay{

    position:absolute;
    inset:0;

    background:
    radial-gradient(
        circle at center,
        transparent 0%,
        rgba(0,0,0,.45) 100%
    );
}

/* PANEL IZQUIERDO */

.left-panel{

    position:relative;
    z-index:10;

    max-width:700px;

    color:#fff;
}

.logo-main{

    width:320px;

    margin-bottom:30px;

    filter:
        drop-shadow(0 0 10px rgba(229,9,20,.35))
        drop-shadow(0 0 20px rgba(229,9,20,.20));
}

.left-panel h1{

    font-size:65px;
    font-weight:800;

    line-height:1.05;

    margin-bottom:25px;

    text-shadow:
        0 4px 20px rgba(0,0,0,.4);
}

.left-panel p{

    font-size:22px;

    line-height:1.6;

    color:#e5e7eb;

    max-width:600px;
}

/* LOGIN CARD */

.login-card{

    position:relative;
    z-index:10;

    width:500px;

    background:
    linear-gradient(
        180deg,
        rgba(3,7,18,.95),
        rgba(3,7,18,.88)
    );

    backdrop-filter:blur(18px);

    border:1px solid rgba(255,255,255,.06);

    border-radius:28px;

    padding:40px;

    box-shadow:
        0 40px 100px rgba(0,0,0,.65);

    animation:slideIn .8s ease;
}

.login-card::before{

    content:'';

    position:absolute;

    top:0;
    left:0;
    right:0;

    height:3px;

    background:
        linear-gradient(
            90deg,
            #E50914,
            #ff4b57
        );

    border-radius:28px 28px 0 0;
}

/* HEADER LOGIN */

.login-header{

    text-align:center;

    margin-bottom:30px;
}

.logo-login{

    width:220px;

    margin-bottom:15px;

    filter:
        drop-shadow(0 0 10px rgba(229,9,20,.35))
        drop-shadow(0 0 20px rgba(229,9,20,.20));
}

.login-header h2{

    color:#fff;

    font-size:22px;

    font-weight:700;
}

/* MENSAJES ERROR */

.alert-error{

    background:#7f1d1d;

    color:#fff;

    padding:12px;

    border-radius:10px;

    margin-bottom:20px;

    text-align:center;
}

/* INPUTS */

.form-group{

    position:relative;

    margin-bottom:18px;
}

.form-control{

    width:100%;

    height:58px;

    border:1px solid rgba(255,255,255,.08);

    border-radius:14px;

    background:
        rgba(255,255,255,.05);

    color:#fff;

    padding-left:50px;
    padding-right:50px;

    font-size:15px;

    outline:none;

    transition:.3s;
}

.form-control::placeholder{

    color:#9ca3af;
}

.form-control:focus{

    border:1px solid #E50914;

    box-shadow:
        0 0 15px rgba(229,9,20,.20);
}

.form-icon{

    position:absolute;

    left:18px;
    top:50%;

    transform:translateY(-50%);

    color:#9ca3af;
}

.password-toggle{

    position:absolute;

    right:18px;
    top:50%;

    transform:translateY(-50%);

    color:#9ca3af;

    cursor:pointer;

    transition:.3s;
}

.password-toggle:hover{

    color:#fff;
}

/* BOTON LOGIN */

.btn-login{

    width:100%;

    height:58px;

    border:none;

    border-radius:14px;

    background:
        linear-gradient(
            135deg,
            #E50914,
            #ff2f3d
        );

    color:white;

    font-size:16px;
    font-weight:700;

    cursor:pointer;

    transition:.3s;
}

.btn-login:hover{

    transform:translateY(-3px);

    box-shadow:
        0 15px 35px rgba(229,9,20,.35);
}

/* FOOTER */

.footer-login{

    margin-top:25px;

    text-align:center;

    color:#9ca3af;

    font-size:12px;

    letter-spacing:1px;
}

/* CHROME AUTOFILL */

input:-webkit-autofill,
input:-webkit-autofill:hover,
input:-webkit-autofill:focus{

    -webkit-box-shadow:
        0 0 0 1000px rgba(255,255,255,.05) inset !important;

    -webkit-text-fill-color:#fff !important;

    transition:
        background-color 5000s ease-in-out 0s;
}

/* ANIMACION */

@keyframes slideIn{

    from{

        opacity:0;

        transform:
            translateX(60px);
    }

    to{

        opacity:1;

        transform:
            translateX(0);
    }
}

/* RESPONSIVE */

@media(max-width:1200px){

    .left-panel{
        display:none;
    }

    body{

        justify-content:center;

        padding:20px;
    }

    .login-card{

        width:100%;

        max-width:500px;
    }
}

@media(max-width:576px){

    .login-card{

        padding:25px;
    }

    .logo-login{

        width:180px;
    }

    .login-header h2{

        font-size:20px;
    }
}
</style>
</head>

<body>


<div class="overlay"></div>

<!-- INFORMACION IZQUIERDA -->
<div class="left-panel">

    <img src="assets/img/logo.png" class="logo-main">

    <h1>
        Sistema Integral de Gestión Cinematográfica
    </h1>

    <p>
        Administración de boletería, dulcería,
        funciones, salas, promociones,
        reportes y facturación electrónica.
    </p>

</div>

<!-- LOGIN -->
<div class="login-card">

    <div class="login-header">
        <img src="assets/img/logo.png" class="logo-login">

        <h2>Acceso Administrativo</h2>
    </div>

    <?php if ($error): ?>
    <div class="alert-error">
        <i class="fas fa-exclamation-circle"></i>
        <?php echo $error; ?>
    </div>
    <?php endif; ?>

    <form method="POST">

        <input type="hidden"
               name="csrf_token"
               value="<?php echo generateCSRFToken(); ?>">

        <div class="form-group">

            <i class="fas fa-user form-icon"></i>

            <input
                type="text"
                name="usuario"
                class="form-control"
                placeholder="Usuario"
                autocomplete="off"
                required>

        </div>

        <div class="form-group">

            <i class="fas fa-lock form-icon"></i>

            <input
                type="password"
                id="password"
                name="password"
                class="form-control"
                placeholder="Contraseña"
                required>

            <i class="fas fa-eye password-toggle"
               onclick="togglePassword()"></i>

        </div>

        <button class="btn-login" type="submit">
            <i class="fas fa-right-to-bracket"></i>
            Iniciar Sesión
        </button>

    </form>

    <div class="footer-login">
        CINERAMA ERP v2.0
    </div>

</div>

<script>
function togglePassword() {
    const pass = document.getElementById('password');

    if(pass.type === 'password'){
        pass.type = 'text';
    }else{
        pass.type = 'password';
    }
}
</script>

</body>


</html>