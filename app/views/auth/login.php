<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= SITE_NAME ?></title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: sans-serif; margin: 0; padding: 20px; background: #f4f4f4; }
        .container { max-width: 400px; margin: 5vh auto; background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 100%; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input { width: 100%; padding: 8px; box-sizing: border-box; }
        .btn { display: inline-block; padding: 10px 15px; background: #333; color: #fff; border: none; cursor: pointer; width: 100%; text-align: center; text-decoration: none; }
        .btn-google { background: #db4437; margin-top: 10px; }
        .error { color: red; font-size: 0.9em; margin-top: 5px; display: block; }
        .text-center { text-align: center; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <div id="general_error" class="error" style="display:none; text-align:center; margin-bottom: 10px;"></div>
        <form id="loginForm" action="<?= URL_ROOT ?>/auth/login" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="<?= Security::escape($data['email'] ?? '') ?>">
                <span class="error" id="email_err"><?= $data['email_err'] ?? '' ?></span>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password">
                <span class="error" id="password_err"><?= $data['password_err'] ?? '' ?></span>
            </div>

            <button type="submit" class="btn">Login</button>
            <a href="<?= URL_ROOT ?>/auth/googleLogin" class="btn btn-google">Login with Google</a>

            <div class="text-center">
                <a href="<?= URL_ROOT ?>/auth/register">Don't have an account? Register</a>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Clear previous errors
            document.querySelectorAll('.error').forEach(el => el.textContent = '');
            document.getElementById('general_error').style.display = 'none';

            let formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = data.redirect;
                } else {
                    if (data.errors) {
                        for (let key in data.errors) {
                            let errEl = document.getElementById(key);
                            if (errEl) {
                                errEl.textContent = data.errors[key];
                            }
                        }
                    } else if (data.error) {
                        let genErr = document.getElementById('general_error');
                        genErr.textContent = data.error;
                        genErr.style.display = 'block';
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                let genErr = document.getElementById('general_error');
                genErr.textContent = 'An unexpected error occurred.';
                genErr.style.display = 'block';
            });
        });
    </script>
</body>
</html>
