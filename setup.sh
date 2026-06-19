#!/bin/bash

echo "🚀 Démarrage de l'installation du projet Laravel..."

# Vérifier si Homebrew est installé
if ! command -v brew &> /dev/null; then
    echo "❌ Homebrew n'est pas installé. Veuillez l'installer depuis https://brew.sh/"
    exit 1
fi

# Vérifier si PHP est installé
if ! command -v php &> /dev/null; then
    echo "⏳ PHP n'est pas installé. Installation via Homebrew..."
    brew install php
fi

# Vérifier si Composer est installé
if ! command -v composer &> /dev/null; then
    echo "⏳ Composer n'est pas installé. Installation via Homebrew..."
    brew install composer
fi

# Vérifier si Node/npm est installé
if ! command -v npm &> /dev/null; then
    echo "⏳ Node.js n'est pas installé. Installation via Homebrew..."
    brew install node
fi

# Copier le fichier d'environnement si nécessaire
if [ ! -f .env ]; then
    echo "⚙️ Copie de .env.example vers .env..."
    cp .env.example .env
fi

# Installer les dépendances PHP
echo "📦 Installation des dépendances Composer..."
composer install

# Installer les dépendances Node
echo "📦 Installation des dépendances NPM..."
npm install

# Générer la clé d'application
echo "🔑 Génération de la clé de l'application..."
php artisan key:generate

# Créer la base de données SQLite si nécessaire
if [ ! -f database/database.sqlite ]; then
    echo "🗄️ Création de la base de données SQLite..."
    touch database/database.sqlite
fi

# Exécuter les migrations
echo "🏗️ Exécution des migrations..."
php artisan migrate --force

echo "✅ Installation terminée !"
echo "🚀 Lancement du serveur de développement..."
echo "👉 Vous pouvez maintenant accéder à l'application."

# Lancer le serveur (ceci lancera Vite et le serveur PHP)
composer run dev
