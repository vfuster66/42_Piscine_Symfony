#!/bin/sh

# Vérifie qu'un argument est fourni
if [ $# -ne 1 ]; then
    echo "Usage: $0 <bit.ly_url>"
    exit 1
fi

# Récupère l'URL de redirection directement à partir du header Location
redirect_url=$(curl -sI "$1" | grep -i "^location:" | cut -d' ' -f2- | tr -d '\r')

# Vérifie si une redirection a été trouvée
if [ -z "$redirect_url" ]; then
    # Si l'URL fournie ne redirige pas, vérifie si c'est une URL existante
    if curl -sI "$1" > /dev/null; then
        echo "Erreur : aucune redirection trouvée."
    else
        echo "Erreur : URL invalide ou inaccessible."
    fi
    exit 1
fi

# Vérifie si la redirection aboutit à une URL inattendue
if [[ "$redirect_url" =~ "techlist.com" ]]; then
    echo "Erreur : aucune redirection trouvée."
    exit 1
fi

# Affiche l'URL redirigée
echo "$redirect_url"
