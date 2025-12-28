# Plan de Tests - Nouveaux Attributs Restaurants

## 🎯 Objectif des Tests
Valider que le nouveau système d'attributs fonctionne correctement dans la section Restaurants sans impacter la section E-commerce.

---

## ✅ Checklist des Tests

### Phase 1 : Tests de Création (create.blade.php)

#### Test 1.1 : Création Simple
- [ ] Ouvrir la page de création d'item
- [ ] Sélectionner un magasin de type "Restaurant"
- [ ] Remplir les informations de base
- [ ] Sélectionner 1 attribut
- [ ] Vérifier que l'interface restaurant s'affiche (carte Bootstrap)
- [ ] Ajouter 2-3 options avec noms et prix
- [ ] Sauvegarder
- [ ] **Résultat attendu** : Item créé avec succès

#### Test 1.2 : Type de Sélection Single
- [ ] Créer un nouvel item
- [ ] Ajouter un attribut "Taille"
- [ ] Laisser le type sur "Single"
- [ ] Vérifier que Min/Max sont masqués
- [ ] Ajouter 3 options : Petit (5€), Moyen (8€), Grand (12€)
- [ ] Sauvegarder
- [ ] **Résultat attendu** : Item créé, min_select=1, max_select=1

#### Test 1.3 : Type de Sélection Multiple
- [ ] Créer un nouvel item
- [ ] Ajouter un attribut "Suppléments"
- [ ] Changer le type sur "Multiple"
- [ ] Vérifier que Min/Max s'affichent
- [ ] Définir Min=0, Max=3
- [ ] Ajouter 5 options avec prix différents
- [ ] Sauvegarder
- [ ] **Résultat attendu** : Item créé avec min_select=0, max_select=3

#### Test 1.4 : Attributs Multiples
- [ ] Créer un nouvel item
- [ ] Ajouter 3 attributs différents
- [ ] Configurer chaque attribut avec des options
- [ ] Vérifier que les 3 cartes s'affichent correctement
- [ ] Sauvegarder
- [ ] **Résultat attendu** : 3 attributs sauvegardés correctement

#### Test 1.5 : Contraintes
- [ ] Créer un item avec 2 attributs
- [ ] Ajouter des options à chaque attribut
- [ ] Cliquer sur "Add Constraint"
- [ ] Définir : Si Attribut1 = OptionA → Attribut2 max=2
- [ ] Sauvegarder
- [ ] **Résultat attendu** : Contrainte sauvegardée

#### Test 1.6 : Validations
- [ ] Essayer de sauvegarder un attribut sans options
- [ ] **Résultat attendu** : Message d'erreur
- [ ] Définir max_select < min_select
- [ ] **Résultat attendu** : Message d'erreur
- [ ] Définir max_select > nombre d'options
- [ ] **Résultat attendu** : Message d'erreur

### Phase 2 : Tests d'Édition (edit.blade.php)

#### Test 2.1 : Édition Item Existant (Restaurant)
- [ ] Ouvrir un item créé en Phase 1
- [ ] Vérifier que les attributs sont restaurés
- [ ] Vérifier que select_type est correct
- [ ] Vérifier que min/max sont affichés correctement
- [ ] Vérifier que les options sont chargées avec noms et prix
- [ ] **Résultat attendu** : Toutes les données sont présentes

#### Test 2.2 : Modification d'Attributs
- [ ] Ouvrir un item en édition
- [ ] Modifier le type Single → Multiple
- [ ] Vérifier que Min/Max s'affichent
- [ ] Modifier min/max
- [ ] Ajouter une nouvelle option
- [ ] Supprimer une option existante
- [ ] Modifier les prix
- [ ] Sauvegarder
- [ ] **Résultat attendu** : Modifications enregistrées

#### Test 2.3 : Ajout d'Attributs
- [ ] Ouvrir un item avec 1 attribut
- [ ] Ajouter un 2ème attribut
- [ ] Configurer le nouvel attribut
- [ ] Sauvegarder
- [ ] **Résultat attendu** : 2 attributs présents

#### Test 2.4 : Suppression d'Attributs
- [ ] Ouvrir un item avec 2 attributs
- [ ] Désélectionner un attribut
- [ ] Sauvegarder
- [ ] **Résultat attendu** : Attribut supprimé

#### Test 2.5 : Modification de Contraintes
- [ ] Ouvrir un item avec contraintes
- [ ] Vérifier que les contraintes sont affichées
- [ ] Modifier une contrainte
- [ ] Ajouter une nouvelle contrainte
- [ ] Supprimer une contrainte
- [ ] Sauvegarder
- [ ] **Résultat attendu** : Contraintes mises à jour

### Phase 3 : Tests d'Affichage (view.blade.php)

#### Test 3.1 : Affichage Simple
- [ ] Ouvrir la vue d'un item avec attributs
- [ ] Vérifier que la section "Attributes" s'affiche
- [ ] Vérifier que le type de sélection est affiché
- [ ] Vérifier que les options sont listées avec prix
- [ ] **Résultat attendu** : Affichage clair et structuré

#### Test 3.2 : Affichage avec Contraintes
- [ ] Ouvrir la vue d'un item avec contraintes
- [ ] Vérifier que la section "Constraints" s'affiche
- [ ] Vérifier que les règles sont lisibles
- [ ] **Résultat attendu** : Contraintes affichées en tableau

#### Test 3.3 : Affichage Multiple Attributs
- [ ] Ouvrir la vue d'un item avec 3+ attributs
- [ ] Vérifier que tous les attributs sont affichés
- [ ] Vérifier la lisibilité
- [ ] **Résultat attendu** : Tous les attributs visibles

### Phase 4 : Tests de Compatibilité E-commerce

#### Test 4.1 : Création E-commerce
- [ ] Sélectionner un magasin E-commerce
- [ ] Créer un nouvel item
- [ ] Ajouter des attributs
- [ ] Vérifier que l'ancienne interface s'affiche (tagsinput)
- [ ] Vérifier que les variantes se génèrent automatiquement
- [ ] Sauvegarder
- [ ] **Résultat attendu** : Logique E-commerce inchangée

#### Test 4.2 : Édition E-commerce
- [ ] Ouvrir un item E-commerce existant
- [ ] Vérifier que l'ancienne logique fonctionne
- [ ] Modifier les variantes
- [ ] Sauvegarder
- [ ] **Résultat attendu** : Modifications E-commerce OK

#### Test 4.3 : Affichage E-commerce
- [ ] Ouvrir la vue d'un item E-commerce
- [ ] Vérifier l'affichage des variantes
- [ ] **Résultat attendu** : Affichage E-commerce OK

### Phase 5 : Tests de Base de Données (Firebase)

#### Test 5.1 : Structure Restaurant
- [ ] Créer un item restaurant avec attributs
- [ ] Ouvrir Firebase Console
- [ ] Vérifier que `item_attribute.type = 'restaurant'`
- [ ] Vérifier que la structure attributes[] est correcte
- [ ] Vérifier que les options ont name et price
- [ ] Vérifier select_type, min_select, max_select
- [ ] **Résultat attendu** : Structure conforme

#### Test 5.2 : Contraintes Firebase
- [ ] Créer un item avec contraintes
- [ ] Ouvrir Firebase Console
- [ ] Vérifier que constraints[] existe
- [ ] Vérifier que chaque contrainte a les 4 champs
- [ ] **Résultat attendu** : Contraintes bien sauvegardées

#### Test 5.3 : Structure E-commerce
- [ ] Créer un item E-commerce
- [ ] Ouvrir Firebase Console
- [ ] Vérifier que `item_attribute.type = 'ecommerce'` OU que le champ type n'existe pas (ancien format)
- [ ] Vérifier que la structure variants[] est présente
- [ ] **Résultat attendu** : Structure E-commerce préservée

### Phase 6 : Tests d'Interface Utilisateur

#### Test 6.1 : Responsive
- [ ] Tester sur écran large (1920px)
- [ ] Tester sur écran moyen (1366px)
- [ ] Tester sur tablette (768px)
- [ ] **Résultat attendu** : Interface adaptative

#### Test 6.2 : Interactions
- [ ] Cliquer sur "Add Option" 10 fois
- [ ] Vérifier que toutes les options s'ajoutent
- [ ] Supprimer des options au milieu
- [ ] Vérifier que les IDs sont uniques
- [ ] **Résultat attendu** : Pas de bugs d'interface

#### Test 6.3 : Formulaire
- [ ] Remplir un formulaire complet
- [ ] Vérifier que Tab fonctionne
- [ ] Vérifier que Enter ne soumet pas le formulaire
- [ ] Vérifier l'autocomplétion
- [ ] **Résultat attendu** : Bonne UX de formulaire

### Phase 7 : Tests de Performance

#### Test 7.1 : Charge
- [ ] Créer un item avec 5 attributs
- [ ] Chaque attribut a 10 options
- [ ] Ajouter 5 contraintes
- [ ] Mesurer le temps de sauvegarde
- [ ] **Résultat attendu** : < 3 secondes

#### Test 7.2 : Édition Lourde
- [ ] Ouvrir un item avec beaucoup de données
- [ ] Mesurer le temps de chargement
- [ ] **Résultat attendu** : < 2 secondes

### Phase 8 : Tests de Régression

#### Test 8.1 : Items Existants
- [ ] Ouvrir 5 anciens items restaurants
- [ ] Vérifier qu'ils s'ouvrent sans erreur
- [ ] Vérifier l'affichage
- [ ] **Résultat attendu** : Pas de régression

#### Test 8.2 : Autres Sections
- [ ] Tester la section Categories
- [ ] Tester la section Vendors
- [ ] Tester la section Orders
- [ ] **Résultat attendu** : Aucun impact

---

## 🐛 Rapport de Bugs

Utilisez ce template pour signaler les bugs :

```
### Bug #X : [Titre court]

**Priorité :** Haute / Moyenne / Basse
**Étape :** Phase X, Test X.X
**Type :** Restaurant / E-commerce / Les deux

**Description :**
[Description détaillée du problème]

**Étapes pour reproduire :**
1. 
2. 
3. 

**Résultat attendu :**
[Ce qui devrait se passer]

**Résultat obtenu :**
[Ce qui se passe réellement]

**Captures d'écran :**
[Si applicable]

**Console JavaScript :**
```
[Erreurs JS]
```

**Firebase Data :**
```json
{
  // Structure de données problématique
}
```
```

---

## ✅ Validation Finale

### Critères d'Acceptation

- [ ] **Fonctionnalité** : Tous les tests Phase 1-3 passent
- [ ] **Compatibilité** : Tous les tests Phase 4 passent
- [ ] **Données** : Tous les tests Phase 5 passent
- [ ] **UX** : Tous les tests Phase 6 passent
- [ ] **Performance** : Tous les tests Phase 7 passent
- [ ] **Stabilité** : Tous les tests Phase 8 passent
- [ ] **Zéro bug critique** détecté
- [ ] **Documentation** complète fournie

### Checklist de Déploiement

- [ ] Tests locaux terminés
- [ ] Code review effectué
- [ ] Documentation mise à jour
- [ ] Traductions ajoutées
- [ ] Backup Firebase effectué
- [ ] Plan de rollback préparé
- [ ] Tests en staging OK
- [ ] Formation équipe effectuée
- [ ] Migration de données (si nécessaire)
- [ ] Déploiement production

---

## 📊 Résultats des Tests

| Phase | Tests Passés | Tests Échoués | Taux de Réussite |
|-------|--------------|---------------|------------------|
| 1     | / 6          | / 6           | % |
| 2     | / 5          | / 5           | % |
| 3     | / 3          | / 3           | % |
| 4     | / 3          | / 3           | % |
| 5     | / 3          | / 3           | % |
| 6     | / 3          | / 3           | % |
| 7     | / 2          | / 2           | % |
| 8     | / 2          | / 2           | % |
| **TOTAL** | **/ 27** | **/ 27**     | **%** |

---

**Document créé le :** {{ date }}
**Testeur :** [Nom]
**Environnement :** Dev / Staging / Production
**Version :** 1.0

