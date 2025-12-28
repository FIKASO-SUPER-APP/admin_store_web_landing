# Traductions Françaises à Ajouter

## Fichier : `Admin Panel/resources/lang/fr/lang.php`

Ajoutez les traductions suivantes dans le fichier de langue :

```php
// ========== ATTRIBUTS RESTAURANTS ==========
'attribute_select_type' => 'Type de Sélection',
'attribute_select_type_help' => 'Définir si l\'attribut permet une seule sélection ou plusieurs',
'attribute_single' => 'Sélection Unique',
'attribute_multiple' => 'Sélection Multiple',
'attribute_min_select' => 'Sélection Minimum',
'attribute_min_select_help' => 'Nombre minimum d\'options que le client doit sélectionner',
'attribute_max_select' => 'Sélection Maximum',
'attribute_max_select_help' => 'Nombre maximum d\'options que le client peut sélectionner',
'attribute_options' => 'Options de Variantes',
'attribute_options_help' => 'Ajouter les différentes options disponibles pour cet attribut',
'attribute_option_name' => 'Nom de l\'Option',
'attribute_option_name_help' => 'Ex: Petit, Moyen, Grand',
'attribute_option_price' => 'Prix de l\'Option',
'attribute_option_price_help' => 'Prix supplémentaire pour cette option',
'add_option' => 'Ajouter une Option',
'add_attribute_option' => 'Ajouter une Option d\'Attribut',
'remove_option' => 'Supprimer l\'Option',

// Contraintes
'attribute_constraints' => 'Contraintes entre Attributs',
'attribute_constraints_help' => 'Définir des règles qui modifient le comportement d\'un attribut en fonction d\'un autre',
'attribute_constraints_advanced' => 'Contraintes d\'Attributs (Avancé)',
'constraint_add' => 'Ajouter une Contrainte',
'constraint_remove' => 'Supprimer la Contrainte',
'constraint_if_attribute' => 'Si l\'Attribut',
'constraint_equals' => 'Est Égal à',
'constraint_then_attribute' => 'Alors l\'Attribut',
'constraint_set_max' => 'Définir Max Sélection à',
'constraint_rule' => 'Règle de Contrainte',
'constraint_example' => 'Ex: Si Taille = 1L alors Parfum max = 1',

// Validations
'validate_min_one_option' => 'Veuillez ajouter au moins une option pour chaque attribut',
'validate_min_less_than_max' => 'La sélection minimum doit être inférieure ou égale à la sélection maximum',
'validate_max_greater_than_options' => 'La sélection maximum ne peut pas être supérieure au nombre d\'options',
'validate_option_name_required' => 'Le nom de l\'option est requis',
'validate_option_price_required' => 'Le prix de l\'option est requis',
'validate_two_attributes_min' => 'Au moins 2 attributs sont nécessaires pour créer des contraintes',
'validate_constraint_complete' => 'Veuillez compléter tous les champs de la contrainte',

// Messages
'attribute_saved_success' => 'Attributs sauvegardés avec succès',
'attribute_updated_success' => 'Attributs mis à jour avec succès',
'attribute_deleted_success' => 'Attribut supprimé avec succès',
'option_added_success' => 'Option ajoutée avec succès',
'option_removed_success' => 'Option supprimée avec succès',
'constraint_added_success' => 'Contrainte ajoutée avec succès',
'constraint_removed_success' => 'Contrainte supprimée avec succès',

// Instructions
'attribute_instructions' => 'Instructions',
'attribute_instructions_single' => 'Sélection Unique : Le client ne peut choisir qu\'une seule option (Ex: Taille : Petit, Moyen, Grand)',
'attribute_instructions_multiple' => 'Sélection Multiple : Le client peut choisir plusieurs options (Ex: Suppléments : Fromage, Olives, Jambon)',
'attribute_instructions_min_max' => 'Min/Max : Contrôle combien d\'options le client doit/peut sélectionner',
'attribute_instructions_constraints' => 'Contraintes : Permet de modifier dynamiquement les règles d\'un attribut en fonction de la sélection d\'un autre',

// Labels
'select_attribute_type' => 'Sélectionner le type d\'attribut',
'option_name_placeholder' => 'Ex: Petit, Moyen, Grand',
'option_price_placeholder' => 'Prix',
'constraint_source_attribute' => 'Attribut Source',
'constraint_source_value' => 'Valeur Source',
'constraint_target_attribute' => 'Attribut Cible',
'constraint_max_value' => 'Valeur Maximum',

// Notes
'note_attributes_restaurant' => 'Note : Ces attributs s\'appliquent uniquement aux items de la section Restaurants',
'note_no_variant_generation' => 'Note : Aucune génération automatique de variantes pour les restaurants',
'note_price_calculation' => 'Le prix total = Prix de base + Prix des options sélectionnées',
'note_constraints_optional' => 'Les contraintes sont optionnelles et ne sont nécessaires que si vous avez des règles métier spécifiques',

// Affichage
'display_attributes' => 'Attributs',
'display_select_type' => 'Type de Sélection',
'display_single_choice' => 'Choix Unique',
'display_multiple_choices' => 'Choix Multiples',
'display_min_selection' => 'Sélection Minimum',
'display_max_selection' => 'Sélection Maximum',
'display_options' => 'Options',
'display_constraints' => 'Contraintes',
'display_no_constraints' => 'Aucune contrainte définie',
```

## Utilisation dans les Vues

### Exemple dans create.blade.php

```blade
<label>{{ trans('lang.attribute_select_type') }} <span class="text-danger">*</span></label>
<div class="form-text text-muted">
    {{ trans('lang.attribute_select_type_help') }}
</div>

<select class="form-control">
    <option value="single">{{ trans('lang.attribute_single') }}</option>
    <option value="multiple">{{ trans('lang.attribute_multiple') }}</option>
</select>
```

### Messages d'Erreur

```javascript
// JavaScript
alert("{{ trans('lang.validate_min_one_option') }}");
alert("{{ trans('lang.validate_min_less_than_max') }}");
```

### Instructions

```blade
<div class="alert alert-info">
    <strong>{{ trans('lang.attribute_instructions') }}</strong>
    <ul>
        <li>{{ trans('lang.attribute_instructions_single') }}</li>
        <li>{{ trans('lang.attribute_instructions_multiple') }}</li>
        <li>{{ trans('lang.attribute_instructions_min_max') }}</li>
        <li>{{ trans('lang.attribute_instructions_constraints') }}</li>
    </ul>
</div>
```

## Ordre d'Ajout

Pour maintenir l'organisation du fichier `lang.php`, ajoutez ces traductions :

1. **Après la section `item_`** (ligne ~260)
2. **Avant la section `ingredients`** (ligne ~268)
3. Ou créez une nouvelle section dédiée :

```php
//---------------------------------------ATTRIBUTS---------------------------------------
'attribute_select_type' => 'Type de Sélection',
// ... toutes les traductions ci-dessus ...
```

## Notes Importantes

1. **Cohérence** : Les clés suivent le pattern `attribute_*`, `constraint_*`, `validate_*`
2. **Hiérarchie** : Utilisez des suffixes comme `_help`, `_error`, `_success` pour les variantes
3. **Contexte** : Les traductions sont spécifiques au contexte Restaurant
4. **Extensibilité** : La structure permet d'ajouter facilement de nouvelles traductions

---

**Fichier créé le :** {{ date }}
**À intégrer dans :** `Admin Panel/resources/lang/fr/lang.php`

