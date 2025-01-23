#!/bin/bash

# Vérifie que le script principal est exécutable
if [[ ! -x ./myawesomescript.sh ]]; then
    echo "Erreur : le script myawesomescript.sh n'est pas exécutable."
    echo "Rends-le exécutable avec : chmod +x myawesomescript.sh"
    exit 1
fi

echo "===== Lancement des tests ====="

# Fonction pour afficher les détails d'un test
run_test() {
    test_name="$1"
    command="$2"
    expected="$3"

    echo "=== $test_name ==="
    echo "Commande exécutée : $command"
    output=$(eval "$command")
    echo "Résultat obtenu   : $output"
    echo "Résultat attendu  : $expected"

    if [[ "$output" == "$expected" ]]; then
        echo "✅ $test_name réussi"
    else
        echo "❌ $test_name échoué"
    fi
    echo ""
}

# Test 1 : URL valide
run_test "Test 1 : URL valide" \
    "./myawesomescript.sh bit.ly/1O72s3U" \
    "http://42.fr/"

# Test 2 : URL invalide
run_test "Test 2 : URL invalide" \
    "./myawesomescript.sh https://bit.ly/invalid" \
    "Erreur : aucune redirection trouvée."

# Test 3 : Aucun argument
echo "=== Test 3 : Aucun argument ==="
echo "Commande exécutée : ./myawesomescript.sh"
output=$(./myawesomescript.sh 2>&1) # Capture les erreurs
exit_code=$?
echo "Code de sortie    : $exit_code"
echo "Résultat attendu  : Code de sortie différent de 0"
if [[ $exit_code -ne 0 ]]; then
    echo "✅ Test 3 réussi (erreur attendue)"
else
    echo "❌ Test 3 échoué. Le script aurait dû retourner un code de sortie non nul."
fi
echo ""

# Test 4 : URL non raccourcie (pas de redirection)
run_test "Test 4 : URL non raccourcie" \
    "./myawesomescript.sh https://www.google.com/" \
    "Erreur : aucune redirection trouvée."

echo "===== Fin des tests ====="
