<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Bienvenue</title>
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

a {
  text-decoration: none;
}

.btn {
  display: block;
  width: 250px;
  margin: 10px auto;
  padding: 12px;
  border: none;
  border-radius: 5px;
  background-color: #007bff;
  color: white;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn:hover {
  background-color: #0056b3;
}

/* Bouton transparent avec bordure */
.btn-outline {
  background-color: transparent;
  color: #007bff;
  border: 2px solid #007bff;
}

.btn-outline:hover {
  background-color: #007bff;
  color: white;
}

h3{
    color: #007bff;
    justify-content: center;
    align-items: center;
}

  </style>
</head>
<body>
  <div class="container">
    <div class="left">
       <img src="img/connect.jpg" alt="Illustration" />
    </div>
    <div class="right">
      <div class="welcome-box">
        <h3>Bienvenu !</h3><br>
        <p>Pour continuer, vous devez :</p>
        <a href="connected_files/connected_services.html">
          <button class="btn btn-outline">Aller à la page d'accueil</button>
        </a>
        <button class="btn">Continuer la candidature</button>
      </div>
    </div>
  </div>
</body>
</html>
