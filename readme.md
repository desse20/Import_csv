# Import-CSV Laravel

Ce projet Laravel permet d'importer des contacts depuis un fichier CSV et de les enregistrer dans la base de données. Il inclut la validation des données, la gestion des doublons et le retour d'un rapport d'importation structuré.

## Fonctionnalités

- Importation de fichiers CSV via une API REST (`POST /api/contacts/import`)
- Validation des champs obligatoires (prénom, nom, email)
- Vérification du format de l'email
- Détection des doublons (dans le fichier et en base)
- Rapport JSON détaillé : nombre d'inserts, lignes ignorées, erreurs

## Installation

1. Clone le dépôt :
   ```bash
   git clone https://github.com/ton-utilisateur/import-csv.git
   cd import-csv
   ```

2. Installe les dépendances :
   ```bash
   composer install
   npm install
   ```

3. Configure l'environnement :
   - Copie le fichier `.env.example` en `.env`
   - Modifie les paramètres de connexion à la base de données dans `.env`

4. Génère la clé d'application :
   ```bash
   php artisan key:generate
   ```

5. Exécute les migrations :
   ```bash
   php artisan migrate
   ```

## Utilisation

- Lance le serveur :
   ```bash
   php artisan serve
   ```

- Envoie une requête POST vers `/api/contacts/import` avec un fichier CSV (champ `file`).
- Exemple avec `curl` :
   ```bash
   curl -F "file=@contacts.csv" http://localhost:8000/api/contacts/import
   ```

## Format du fichier CSV attendu

| Prénom | Nom | Email | Téléphone (optionnel) |
|--------|-----|-------|-----------------------|

## Tests

Pour lancer les tests unitaires :
```bash
php artisan test
```

## Licence

Ce projet est sous licence MIT.
