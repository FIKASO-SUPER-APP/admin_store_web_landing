# Modifications des Attributs pour la Section Restaurants

## 🎯 Objectif
Réajuster la gestion des attributs et variantes des items dans la section **Restaurants uniquement**, sans génération automatique de variantes.

## ✅ Changements Implémentés

### 1. **Structure des Attributs pour Restaurants**

#### Nouvelle Structure de Données (Firebase)
```javascript
item_attribute: {
    type: 'restaurant',  // Ou 'ecommerce' pour l'ancien système
    attributes: [
        {
            attribute_id: 'attr_123',
            select_type: 'single',  // ou 'multiple'
            min_select: 1,          // requis si multiple
            max_select: 1,          // requis si multiple
            options: [
                { name: '1L', price: 10.00 },
                { name: '2L', price: 18.00 },
                { name: '5L', price: 40.00 }
            ]
        }
    ],
    constraints: [
        {
            source_attribute: 'attr_taille',
            source_value: '1L',
            target_attribute: 'attr_parfum',
            max_select: 1
        }
    ]
}
```

### 2. **Fichiers Modifiés**

#### A. `Admin Panel/resources/views/items/create.blade.php`

**Modifications principales :**
- Nouvelle fonction `selectAttribute()` avec détection du type de section
- Interface avec cartes Bootstrap pour chaque attribut
- Champs **Select Type** (single/multiple)
- Champs **Min Select** et **Max Select** (conditionnels)
- Bouton "Add Option" pour ajouter des options de variantes avec prix
- Section "Attribute Constraints" pour définir les règles entre attributs
- Fonctions JavaScript ajoutées :
  - `toggleMinMaxFields(attrId)` - Afficher/masquer min/max
  - `addVariantOption(attrId)` - Ajouter une option de variante
  - `removeVariantOption(attrId, optionIndex)` - Supprimer une option
  - `collectRestaurantAttributes()` - Collecter les données des attributs
  - `addConstraint()` - Ajouter une contrainte
  - `removeConstraint(constraintId)` - Supprimer une contrainte
  - `loadSourceOptions(constraintId)` - Charger les valeurs source
  - `collectAttributeConstraints()` - Collecter les contraintes

**Interface Utilisateur :**
- Chaque attribut est affiché dans une carte Bootstrap
- Type de sélection : Dropdown (single/multiple)
- Options : Tableau dynamique avec nom et prix
- Suppression de la génération automatique de variantes

#### B. `Admin Panel/resources/views/items/edit.blade.php`

**Modifications principales :**
- Adaptation de `selectAttribute()` pour charger les données existantes
- Même interface que create.blade.php
- Chargement des options existantes lors de l'édition
- Fonction `loadExistingConstraints()` pour restaurer les contraintes
- Mêmes fonctions utilitaires que create.blade.php

**Gestion des données existantes :**
- Détection du type d'attribut (restaurant vs ecommerce)
- Restauration des valeurs select_type, min_select, max_select
- Chargement des options avec nom et prix
- Affichage des contraintes existantes

#### C. `Admin Panel/resources/views/items/view.blade.php`

**Modifications principales :**
- Nouvelle section "Attributes" pour afficher les attributs
- Affichage structuré en tableau des attributs restaurants
- Affichage des contraintes si présentes
- Code JavaScript pour formater l'affichage

**Interface d'affichage :**
```
Attribute 1
┌─────────────────┬──────────────────────┐
│ Select Type     │ Single Choice        │
│ Options         │ • 1L: 10.00 CFA      │
│                 │ • 2L: 18.00 CFA      │
│                 │ • 5L: 40.00 CFA      │
└─────────────────┴──────────────────────┘

Constraints
┌─────────────┬────────┬─────────────┬────────────┐
│ If Attribute│ Equals │ Then Attr.  │ Max Select │
│ Taille      │ 1L     │ Parfum      │ 1          │
└─────────────┴────────┴─────────────┴────────────┘
```

### 3. **Système de Contraintes**

#### Fonctionnement
Les contraintes permettent de définir des règles dynamiques entre attributs :

**Exemple : Crème Glacée**
```
Attribut Taille (single) : 1L, 2L, 5L
Attribut Parfum (multiple, min=1, max=5) : Vanille, Chocolat, Fraise, Orange

Contraintes :
- Si Taille = 1L → Parfum max_select = 1
- Si Taille = 2L → Parfum max_select = 2
- Si Taille = 5L → Parfum max_select = 4
```

#### Interface des Contraintes
- Bouton "Add Constraint" pour créer une nouvelle règle
- 4 champs par contrainte :
  1. **If Attribute** - Attribut source (dropdown)
  2. **Equals** - Valeur de l'attribut source (dropdown dynamique)
  3. **Then Attribute** - Attribut cible (dropdown)
  4. **Set Max Select To** - Nouvelle valeur max_select (number)

### 4. **Compatibilité E-commerce**

Les modifications n'affectent **PAS** la section E-commerce :
- Détection automatique via `sectionData.serviceTypeFlag`
- Si `serviceTypeFlag == "delivery-service"` → Logique Restaurants
- Sinon → Logique E-commerce (ancienne)
- Les deux systèmes coexistent dans le même fichier

### 5. **Validation des Données**

#### Validations implémentées :
- ✅ Au moins une option par attribut
- ✅ Max select ≥ Min select
- ✅ Max select ≤ Nombre d'options
- ✅ Attribut avec options vide = Erreur
- ✅ Min select minimum = 1
- ✅ Prix obligatoire pour chaque option

### 6. **Points Clés**

#### ✅ Ce qui a été supprimé pour Restaurants :
- ❌ Génération automatique de variantes (fonction `variants_update()`)
- ❌ Champ "Variant Quantity"
- ❌ Tableaux de combinaisons automatiques
- ❌ Fonction `getCombinations()` pour restaurants

#### ✅ Ce qui a été ajouté pour Restaurants :
- ✅ Champ Select Type (single/multiple)
- ✅ Champs Min Select / Max Select
- ✅ Options avec nom + prix
- ✅ Système de contraintes entre attributs
- ✅ Interface utilisateur moderne avec Bootstrap cards

## 📝 Utilisation

### Créer un Item avec Attributs (Restaurant)

1. Sélectionner la section "Restaurants"
2. Remplir les informations de base
3. Dans "Attributes", sélectionner les attributs souhaités
4. Pour chaque attribut :
   - Choisir le type : Single ou Multiple
   - Si Multiple : définir min/max
   - Cliquer sur "Add Option" pour chaque variante
   - Entrer le nom et le prix de chaque option
5. (Optionnel) Ajouter des contraintes entre attributs
6. Sauvegarder

### Exemple Concret : Pizza

```
Attribut 1 : Taille (Single)
  Options:
  - Petite : 8.00 €
  - Moyenne : 12.00 €
  - Grande : 16.00 €

Attribut 2 : Suppléments (Multiple, min=0, max=3)
  Options:
  - Fromage Extra : 2.00 €
  - Olives : 1.50 €
  - Champignons : 1.50 €
  - Jambon : 2.50 €

Contrainte :
  Si Taille = Petite → Suppléments max_select = 1
```

## 🔍 Tests Recommandés

1. ✅ Créer un item dans la section Restaurants avec attributs
2. ✅ Vérifier que les options s'affichent correctement
3. ✅ Modifier un item existant et vérifier la restauration des données
4. ✅ Ajouter des contraintes et vérifier leur sauvegarde
5. ✅ Visualiser un item et confirmer l'affichage des attributs
6. ✅ Créer un item dans la section E-commerce pour vérifier la compatibilité

## 🎨 Améliorations Futures Possibles

- [ ] Traduction des labels en français
- [ ] Validation côté client en temps réel
- [ ] Prévisualisation des contraintes
- [ ] Import/Export d'attributs
- [ ] Templates d'attributs prédéfinis
- [ ] Recherche dans les attributs

## 📌 Notes Importantes

1. **Les données sont sauvegardées dans Firebase avec le champ `type`** pour distinguer restaurants/ecommerce
2. **La structure est extensible** pour ajouter d'autres types de contraintes
3. **Pas de migration de données nécessaire** - les anciens items continuent de fonctionner
4. **Les modifications sont isolées à la section Restaurants**

## 🐛 Debugging

Si un problème survient :
1. Vérifier `sectionData.serviceTypeFlag` dans la console
2. Vérifier la structure de `item_attribute` dans Firebase
3. Vérifier les messages d'alerte de validation
4. Consulter la console JavaScript pour les erreurs

---

**Implémentation terminée le :** {{ date }}
**Portée :** Section Restaurants uniquement
**Compatibilité :** Conservée avec E-commerce

