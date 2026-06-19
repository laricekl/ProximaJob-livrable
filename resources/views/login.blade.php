<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion</title>
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
  background-color: white;
  display: flex;
  align-items: center;
  justify-content: center;
}

.login-box {
  width: 80%;
  max-width: 350px;
  text-align: center;
}

.role-switch {
  display: flex;
  justify-content: center;
  margin-bottom: 20px;
}

.role-switch button {
  flex: 1;
  padding: 10px;
  border: 1px solid #ccc;
  background: none;
  cursor: pointer;
}

.role-switch .active {
  background-color: #007bff;
  color: white;
}

.login-btn {
  width: 100%;
  padding: 12px;
  margin: 10px 0;
  border: none;
  cursor: pointer;
  border-radius: 5px;
  font-weight: bold;
}

.login-btn.email {
  background-color: #f0f0f0;
}

.login-btn.google {
  background-color: #db4437;
  color: white;
}

.login-btn.facebook {
  background-color: #4267B2;
  color: white;
}

.login-box p {
  margin-top: 20px;
  font-size: 14px;
}

.login-box a {
  color: #007bff;
  text-decoration: none;
}

  </style>
</head>
<body>
  <div class="container">
    <div class="left">
       <img src="img/connect.png" alt="Illustration" />
    </div>
    <div class="right">
      <div class="login-box">
        <div class="role-switch">
          <button class="active">Chercheur d'emploi</button>
          <a href="login03.html">
            <button>Entreprise</button>
          </a>
        </div>
        <button class="login-btn email">Connectez-vous avec e-mail</button>
        <button class="login-btn google">Connectez-vous avec <span>Google</span></button>
        <button class="login-btn facebook">Connectez-vous avec <span>Facebook</span></button>
        <p>Vous n'avez pas de compte ? <a href="#">S'inscrire</a></p>
      </div>
    </div>
  </div>
</body>
</html>
