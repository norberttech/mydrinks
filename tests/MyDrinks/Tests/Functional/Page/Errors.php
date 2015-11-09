<?php

namespace MyDrinks\Tests\Functional\Page;

final class Errors 
{
    const RECIPE_NAME_NOT_UNIQUE_MSG = 'Drink o takiej nazwie już istnieje.';
    const EMPTY_VALUE_MSG = 'Ta wartość nie powinna być pusta.';
    const INVALID_FIRST_STEP = 'Najpierw należy wybrać krok "Przygotuj szkło".';
    const MISSING_SHAKER_ERROR = 'Najpierw należy wybrać krok "Przygotuj shaker".';
}