# Guide d'Utilisation - Nouveaux Attributs Restaurants

## 📖 Table des Matières
1. [Vue d'ensemble](#vue-densemble)
2. [Exemple Complet](#exemple-complet)
3. [Cas d'Usage](#cas-dusage)
4. [FAQ](#faq)

---

## 🎯 Vue d'ensemble

### Avant (E-commerce)
❌ Génération automatique de variantes
❌ Quantité par variante
❌ Images par variante

```
Taille: S, M, L
Couleur: Rouge, Bleu

→ Génère automatiquement : S-Rouge, S-Bleu, M-Rouge, M-Bleu, L-Rouge, L-Bleu
```

### Après (Restaurants)
✅ Attributs avec options et prix
✅ Type de sélection (single/multiple)
✅ Min/Max pour sélection multiple
✅ Contraintes entre attributs

```
Taille (Single):
  - Petite: 8€
  - Moyenne: 12€
  - Grande: 16€

Suppléments (Multiple, min=0, max=3):
  - Fromage: 2€
  - Olives: 1.5€
```

---

## 🍕 Exemple Complet : Pizza Margherita

### Configuration

#### Attribut 1 : Taille
```
Type: Single (choix unique)
Options:
  - Petite (25cm) : 8.00 €
  - Moyenne (30cm) : 12.00 €
  - Grande (40cm) : 16.00 €
```

#### Attribut 2 : Base
```
Type: Single (choix unique)
Options:
  - Tomate : 0.00 €
  - Crème : 1.00 €
```

#### Attribut 3 : Suppléments
```
Type: Multiple
Min Select: 0
Max Select: 5
Options:
  - Fromage Extra : 2.00 €
  - Olives Noires : 1.50 €
  - Champignons : 1.50 €
  - Jambon : 2.50 €
  - Anchois : 2.00 €
  - Roquette : 1.00 €
```

#### Contraintes
```
Si Taille = Petite → Suppléments max_select = 2
Si Taille = Moyenne → Suppléments max_select = 4
Si Taille = Grande → Suppléments max_select = 5
```

### Calcul du Prix Total

**Scénario Client :**
- Taille : Moyenne (12€)
- Base : Crème (1€)
- Suppléments : Fromage Extra (2€) + Champignons (1.5€)

**Prix Total = 12 + 1 + 2 + 1.5 = 16.50 €**

---

## 🍦 Cas d'Usage : Crème Glacée

### Configuration

#### Attribut 1 : Contenant
```
Type: Single
Options:
  - Pot 1L : 10.00 €
  - Pot 2L : 18.00 €
  - Pot 5L : 40.00 €
```

#### Attribut 2 : Parfums
```
Type: Multiple
Min Select: 1
Max Select: 5
Options:
  - Vanille : 0.00 €
  - Chocolat : 0.00 €
  - Fraise : 0.00 €
  - Pistache : 0.50 €
  - Caramel : 0.50 €
  - Mangue : 0.75 €
```

#### Contraintes
```
Si Contenant = Pot 1L → Parfums max_select = 1
Si Contenant = Pot 2L → Parfums max_select = 2
Si Contenant = Pot 5L → Parfums max_select = 4
```

### Logique Métier

Un client qui commande un pot de 1L ne peut choisir qu'un seul parfum.
Un client qui commande un pot de 5L peut choisir jusqu'à 4 parfums différents.

---

## ☕ Cas d'Usage : Café

### Configuration

#### Attribut 1 : Taille
```
Type: Single
Options:
  - Petit : 2.50 €
  - Moyen : 3.50 €
  - Grand : 4.50 €
```

#### Attribut 2 : Type de Lait
```
Type: Single
Options:
  - Normal : 0.00 €
  - Écrémé : 0.00 €
  - Soja : 0.50 €
  - Amande : 0.50 €
```

#### Attribut 3 : Options
```
Type: Multiple
Min Select: 0
Max Select: 3
Options:
  - Sucre : 0.00 €
  - Caramel : 0.50 €
  - Vanille : 0.50 €
  - Chantilly : 0.75 €
```

**Pas de contraintes pour cet exemple**

---

## 🥗 Cas d'Usage : Salade Personnalisée

### Configuration

#### Attribut 1 : Base
```
Type: Single
Options:
  - Laitue : 5.00 €
  - Épinards : 5.50 €
  - Mélange : 6.00 €
```

#### Attribut 2 : Protéines
```
Type: Single
Options:
  - Poulet : 3.00 €
  - Thon : 3.50 €
  - Tofu : 2.50 €
  - Sans : 0.00 €
```

#### Attribut 3 : Légumes
```
Type: Multiple
Min Select: 2
Max Select: 6
Options:
  - Tomates : 0.50 €
  - Concombres : 0.50 €
  - Carottes : 0.50 €
  - Maïs : 0.75 €
  - Avocat : 1.50 €
  - Olives : 0.75 €
```

#### Attribut 4 : Sauce
```
Type: Single
Options:
  - Vinaigrette : 0.00 €
  - César : 0.50 €
  - Miel-Moutarde : 0.50 €
  - Sans sauce : 0.00 €
```

---

## ❓ FAQ

### Q1 : Puis-je avoir des attributs gratuits ?
**R :** Oui ! Mettez simplement le prix à `0.00`. Exemple : "Sauce Tomate : 0.00 €"

### Q2 : Comment forcer le client à choisir au moins une option ?
**R :** Pour un attribut Multiple, définissez `min_select = 1`

### Q3 : Puis-je avoir un attribut optionnel ?
**R :** Oui, pour un attribut Multiple, définissez `min_select = 0`

### Q4 : Les contraintes sont-elles obligatoires ?
**R :** Non, elles sont optionnelles. N'ajoutez des contraintes que si vous avez des règles métier spécifiques.

### Q5 : Combien d'attributs puis-je avoir ?
**R :** Il n'y a pas de limite technique, mais pour une bonne UX client, nous recommandons 3-5 attributs maximum.

### Q6 : Combien d'options par attribut ?
**R :** Pas de limite, mais pour la lisibilité, gardez-le raisonnable (< 10 options par attribut).

### Q7 : Puis-je modifier les attributs d'un item existant ?
**R :** Oui, utilisez la fonction Edit. Les données seront mises à jour.

### Q8 : Que se passe-t-il avec les anciens items ?
**R :** Ils continuent de fonctionner normalement. Le système détecte automatiquement l'ancien format.

### Q9 : Les contraintes s'appliquent-elles automatiquement ?
**R :** Les contraintes sont sauvegardées en base de données. L'application mobile/web doit les implémenter pour les appliquer lors de la commande.

### Q10 : Comment tester les modifications ?
**R :** 
1. Créez un nouvel item dans la section Restaurants
2. Ajoutez des attributs avec différentes configurations
3. Sauvegardez
4. Visualisez l'item pour vérifier l'affichage
5. Éditez l'item pour vérifier que les données sont bien restaurées

---

## 🎨 Bonnes Pratiques

### ✅ DO
- Utilisez des noms d'options clairs et descriptifs
- Organisez vos attributs par ordre logique
- Testez différentes combinaisons avant de publier
- Utilisez les contraintes pour simplifier l'expérience utilisateur
- Mettez des prix réalistes

### ❌ DON'T
- Ne créez pas trop d'attributs (max 5 recommandé)
- N'utilisez pas de noms d'options ambigus
- Ne mettez pas des max_select trop élevés
- N'oubliez pas de définir min_select pour les attributs obligatoires

---

## 📊 Structure de Données (Firebase)

```json
{
  "id": "item_123",
  "name": "Pizza Margherita",
  "price": "8.00",
  "item_attribute": {
    "type": "restaurant",
    "attributes": [
      {
        "attribute_id": "attr_size",
        "select_type": "single",
        "min_select": 1,
        "max_select": 1,
        "options": [
          { "name": "Petite", "price": 8.00 },
          { "name": "Moyenne", "price": 12.00 },
          { "name": "Grande", "price": 16.00 }
        ]
      },
      {
        "attribute_id": "attr_toppings",
        "select_type": "multiple",
        "min_select": 0,
        "max_select": 5,
        "options": [
          { "name": "Fromage Extra", "price": 2.00 },
          { "name": "Olives", "price": 1.50 }
        ]
      }
    ],
    "constraints": [
      {
        "source_attribute": "attr_size",
        "source_value": "Petite",
        "target_attribute": "attr_toppings",
        "max_select": 2
      }
    ]
  }
}
```

---

## 🚀 Pour Aller Plus Loin

### Intégration Mobile/Web
L'application client devra :
1. Lire `item_attribute.type` pour savoir quel format utiliser
2. Afficher les attributs selon `select_type`
3. Appliquer les contraintes pendant la sélection
4. Calculer le prix total : `prix_base + Σ(prix_options_selectionnées)`

### Exemple de Calcul
```javascript
let totalPrice = parseFloat(item.price);

item.item_attribute.attributes.forEach(attr => {
  selectedOptions.forEach(option => {
    totalPrice += parseFloat(option.price);
  });
});

console.log('Prix total :', totalPrice);
```

---

**Document créé le :** {{ date }}
**Version :** 1.0
**Auteur :** Assistant IA

