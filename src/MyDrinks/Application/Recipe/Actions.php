<?php

namespace MyDrinks\Application\Recipe;

final class Actions 
{
    const PREPARE_GLASS = 'prepareGlass';
    const POUR_INTO_GLASS = 'pourIntoGlass';
    const STRAIN_INTO_GLASS_FROM_SHAKER = 'strainIntoGlassFromShaker';
    const ADD_INGREDIENT_INTO_GLASS = 'addIngredientIntoGlass';
    const STIR_GLASS_CONTENT = 'stirGlassContent';
    const FILL_GLASS = 'fillGlass';
    const IGNITE_GLASS_CONTENT = 'igniteGlassContent';
    const GARNISH_GLASS = 'garnishGlass';
    const EMPTY_GLASS_CONTENT = 'emptyGlassContent';
    const MUDDLE_GLASS_CONTENT = 'muddleGlassContent';
    const TOP_UP_GLASS = 'topUpGlass';
    
    const PREPARE_SHAKER = 'prepareShaker';
    const POUR_INTO_SHAKER = 'pourIntoShaker';
    const SHAKE_SHAKER_CONTENT = 'shakeShakerContent';
    const FILL_SHAKER = 'fillShaker';
    const ADD_INGREDIENT_INTO_SHAKER = 'addIngredientIntoShaker';
}