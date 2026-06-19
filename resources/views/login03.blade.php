<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ConnexionO1</title>
  <style>
    * {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: sans-serif;
}

body, html {
  height: 100%;
}

.container {
  display: flex;
  height: 100vh;
}

.left {
  flex: 1;
}

.left img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.right {
  flex: 1;
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: white;
}

.login-form {
  width: 80%;
  max-width: 350px;
}

.login-form h3 {
  margin-bottom: 20px;
  text-align: center;
  font-size: 24px;
}

.login-form label {
  display: block;
  margin: 10px 0 5px;
  font-weight: bold;
}

.login-form input[type="text"],
.login-form input[type="password"] {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 5px;
}

.options {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin: 15px 0;
  font-size: 14px;
}

.options a {
  color: #007bff;
  text-decoration: none;
}

.login-form button {
  width: 100%;
  padding: 12px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 5px;
  font-weight: bold;
  cursor: pointer;
  margin-top: 10px;
}

.login-form p {
  text-align: center;
  margin-top: 20px;
  font-size: 14px;
}

.login-form a {
  color: #007bff;
  text-decoration: none;
}

  </style>
</head>
<body>
  <div class="container">
    <div class="left">
        <img src="img/connect.jpg" alt="Illustration" />
    </div>
    <div class="right">
      <form class="login-form" action="{{ route("au")}}">
        <h3>Connexion</h3>
        <label for="username">Nom d'utilisateur</label>
        <input type="text" id="username" name="username" required />

        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" required />

        <div class="options">
          <label><input type="checkbox" /> Rester connecté</label>
          <a href="#">Mot de passe oublié ?</a>
        </div>

        <button >Se connecter</button>
        <p>Vous n'avez pas de compte ? <a href="#">S'inscrire</a></p>
      </form>
    </div>
  </div>
</body>
</html>
