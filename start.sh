#!/bin/bash

# ──────────────────────────────────────────────
# ProximaJob — Script de lancement en développement
# ──────────────────────────────────────────────

set -e

cd "$(dirname "$0")"

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m'

step()  { echo -e "${CYAN}→${NC} $1"; }
ok()    { echo -e "  ${GREEN}✓${NC} $1"; }
warn()  { echo -e "  ${YELLOW}⚠${NC} $1"; }
err()   { echo -e "  ${RED}✗${NC} $1"; }

cleanup() {
    if [ -n "$VITE_PID" ]; then kill "$VITE_PID" 2>/dev/null; fi
    if [ -n "$ARTISAN_PID" ]; then kill "$ARTISAN_PID" 2>/dev/null; fi
}
trap cleanup EXIT

echo ""
echo -e "${CYAN}╔══════════════════════════════════════╗${NC}"
echo -e "${CYAN}║       ProximaJob — Démarrage        ║${NC}"
echo -e "${CYAN}╚══════════════════════════════════════╝${NC}"
echo ""

# 1. Vérifications
step "Vérification de l'environnement..."

if ! command -v php &>/dev/null; then
    err "PHP n'est pas installé"
    exit 1
fi
PHP_VERSION=$(php -r 'echo PHP_VERSION;')
ok "PHP $PHP_VERSION"

if ! command -v composer &>/dev/null; then
    err "Composer n'est pas installé"
    exit 1
fi
ok "Composer $(composer --version 2>/dev/null | grep -oP '\d+\.\d+\.\d+' | head -1)"

if ! command -v node &>/dev/null; then
    err "Node.js n'est pas installé"
    exit 1
fi
ok "Node $(node --version)"

# 2. Dépendances
step "Vérification des dépendances..."

if [ ! -d "vendor" ]; then
    warn "vendor/ absent → composer install..."
    composer install --no-interaction --prefer-dist
    ok "Dépendances PHP installées"
else
    ok "vendor/ présent"
fi

if [ ! -d "node_modules" ] || [ ! -f "node_modules/.package-lock.json" ]; then
    warn "node_modules/ incomplet → npm ci..."
    npm ci --silent
    ok "Dépendances Node installées"
else
    ok "node_modules/ présent"
fi

# 3. Base de données
step "Vérification de la base de données..."

if [ ! -f "database/database.sqlite" ]; then
    warn "SQLite absent → création..."
    touch database/database.sqlite
    ok "database/database.sqlite créé"
else
    ok "database/database.sqlite présent"
fi

php artisan migrate --force --quiet 2>/dev/null && ok "Migrations à jour" || warn "Migrations ignorées (peut nécessiter un rafraîchissement)"

# 4. Ports
step "Vérification des ports..."

VITE_PORT=${VITE_PORT:-5173}
LARAVEL_PORT=${LARAVEL_PORT:-8000}

if lsof -ti:"$VITE_PORT" &>/dev/null; then
    warn "Port $VITE_PORT occupé → libération..."
    kill "$(lsof -ti:$VITE_PORT)" 2>/dev/null
    sleep 1
fi
ok "Port Vite ($VITE_PORT) libre"

if lsof -ti:"$LARAVEL_PORT" &>/dev/null; then
    warn "Port $LARAVEL_PORT occupé → libération..."
    kill "$(lsof -ti:$LARAVEL_PORT)" 2>/dev/null
    sleep 1
fi
ok "Port Laravel ($LARAVEL_PORT) libre"

# 5. Lancement Vite
step "Démarrage de Vite (port $VITE_PORT)..."
npm run dev -- --port "$VITE_PORT" &
VITE_PID=$!
sleep 3

if ! kill -0 "$VITE_PID" 2>/dev/null; then
    err "Vite n'a pas démarré"
    exit 1
fi
ok "Vite lancé (PID $VITE_PID) → http://127.0.0.1:$VITE_PORT"

# 6. Lancement Laravel
step "Démarrage de Laravel (port $LARAVEL_PORT)..."
php artisan serve --host=0.0.0.0 --port="$LARAVEL_PORT" &
ARTISAN_PID=$!
sleep 2

if ! kill -0 "$ARTISAN_PID" 2>/dev/null; then
    err "Laravel n'a pas démarré"
    exit 1
fi
ok "Laravel lancé (PID $ARTISAN_PID) → http://localhost:$LARAVEL_PORT"

# 7. Ouverture navigateur
step "Ouverture du navigateur..."
sleep 1
open "http://localhost:$LARAVEL_PORT"
ok "Navigateur ouvert"

echo ""
echo -e "${GREEN}╔══════════════════════════════════════╗${NC}"
echo -e "${GREEN}║        ProximaJob est prêt !        ║${NC}"
echo -e "${GREEN}╠══════════════════════════════════════╣${NC}"
echo -e "${GREEN}║${NC}  App   → http://localhost:$LARAVEL_PORT      ${GREEN}║${NC}"
echo -e "${GREEN}║${NC}  Vite  → http://127.0.0.1:$VITE_PORT       ${GREEN}║${NC}"
echo -e "${GREEN}╚══════════════════════════════════════╝${NC}"
echo ""
echo "Appuyez sur Ctrl+C pour arrêter les serveurs."

# Attente
wait
