define(
['jquery', 'app/component/translator', 'app/component/routing', 'bootstrapTypehead'], 
function($, Translator, routing) {
    var AddStepForm = function () {
        this.form = $('form[name="recipe_step"]');
        this.displayFields();
        
        var self = this;
        this.form.find('#recipe_step_type').change(function() {
            self.displayFields();
        });

        this.form.find('#recipe_step_name_autocomplete').typeahead({
            source: function(query, process) {
                return $.get(routing.generate('supply_autocomplete', {query: query}), function(data) {
                    return process(data);
                });
            },
            displayText: function(item) {
                return item.name + ' (' + Translator.trans(item.type) + ')';
            },
            matcher: function(item) {
                return true;
            },
            afterSelect: function(activeItem) {
                $('#recipe_step_name').val(activeItem.id);
            }
        });
    };

    AddStepForm.prototype.displayFields = function () {
        var type = this.form.find('#recipe_step_type').val();
        switch (type){
            // Glass
            case 'prepareGlass':
                $('label[for="recipe_step_name"]').text(Translator.trans('recipe.form.add_step.name.glass'));
                this.showGroup('#recipe_step_name');
                this.showGroup('#recipe_step_amount');
                this.showGroup('#recipe_step_capacity');
                break;
            case 'pourIntoGlass':
                $('label[for="recipe_step_name"]').text(Translator.trans('recipe.form.add_step.name.liquid'));
                this.showGroup('#recipe_step_capacity');
                this.showGroup('#recipe_step_name');
                this.hideGroup('#recipe_step_amount');
                break;
            case 'strainIntoGlassFromShaker':
                $('label[for="recipe_step_name"]').text(Translator.trans('recipe.form.add_step.name.liquid'));
                this.hideGroup('#recipe_step_name');
                this.hideGroup('#recipe_step_amount');
                this.hideGroup('#recipe_step_capacity');
                break;
            case 'addIngredientIntoGlass':
                $('label[for="recipe_step_name"]').text(Translator.trans('recipe.form.add_step.name.ingredient'));
                this.showGroup('#recipe_step_name');
                this.showGroup('#recipe_step_amount');
                this.hideGroup('#recipe_step_capacity');
                break;
            case 'stirGlassContent':
                $('label[for="recipe_step_name"]').text(Translator.trans('recipe.form.add_step.name.label'));
                this.hideGroup('#recipe_step_name');
                this.hideGroup('#recipe_step_amount');
                this.hideGroup('#recipe_step_capacity');
                break;
            case 'fillGlass':
                $('label[for="recipe_step_name"]').text(Translator.trans('recipe.form.add_step.name.ingredient'));
                this.showGroup('#recipe_step_name');
                this.hideGroup('#recipe_step_amount');
                this.hideGroup('#recipe_step_capacity');
                break;
            case 'igniteGlassContent':
                $('label[for="recipe_step_name"]').text(Translator.trans('recipe.form.add_step.name.label'));
                this.hideGroup('#recipe_step_name');
                this.hideGroup('#recipe_step_amount');
                this.hideGroup('#recipe_step_capacity');
                break;
            case 'garnishGlass':
                $('label[for="recipe_step_name"]').text(Translator.trans('recipe.form.add_step.name.garnish'));
                this.showGroup('#recipe_step_name');
                this.hideGroup('#recipe_step_amount');
                this.hideGroup('#recipe_step_capacity');
                break;
            case 'emptyGlassContent':
                $('label[for="recipe_step_name"]').text(Translator.trans('recipe.form.add_step.name.label'));
                this.hideGroup('#recipe_step_name');
                this.hideGroup('#recipe_step_amount');
                this.hideGroup('#recipe_step_capacity');
                break;
            case 'muddleGlassContent':
                $('label[for="recipe_step_name"]').text(Translator.trans('recipe.form.add_step.name.label'));
                this.hideGroup('#recipe_step_name');
                this.hideGroup('#recipe_step_amount');
                this.hideGroup('#recipe_step_capacity');
                break;
            case 'topUpGlass':
                $('label[for="recipe_step_name"]').text(Translator.trans('recipe.form.add_step.name.liquid'));
                this.showGroup('#recipe_step_name');
                this.hideGroup('#recipe_step_amount');
                this.hideGroup('#recipe_step_capacity');
                break;
            // Shaker
            case 'prepareShaker':
                $('label[for="recipe_step_name"]').text(Translator.trans('recipe.form.add_step.name.label'));
                this.showGroup('#recipe_step_capacity');
                this.hideGroup('#recipe_step_name');
                this.hideGroup('#recipe_step_amount');
                break;
            case 'pourIntoShaker':
                $('label[for="recipe_step_name"]').text(Translator.trans('recipe.form.add_step.name.liquid'));
                this.showGroup('#recipe_step_name');
                this.showGroup('#recipe_step_capacity');
                this.hideGroup('#recipe_step_amount');
                break;
            case 'shakeShakerContent':
                $('label[for="recipe_step_name"]').text(Translator.trans('recipe.form.add_step.name.label'));
                this.hideGroup('#recipe_step_name');
                this.hideGroup('#recipe_step_amount');
                this.hideGroup('#recipe_step_capacity');
                break;
            case 'fillShaker':
                $('label[for="recipe_step_name"]').text(Translator.trans('recipe.form.add_step.name.ingredient'));
                this.showGroup('#recipe_step_name');
                this.hideGroup('#recipe_step_amount');
                this.hideGroup('#recipe_step_capacity');
                break;
            case 'addIngredientIntoShaker':
                $('label[for="recipe_step_name"]').text(Translator.trans('recipe.form.add_step.name.ingredient'));
                this.showGroup('#recipe_step_name');
                this.showGroup('#recipe_step_amount');
                this.hideGroup('#recipe_step_capacity');
                break;
        }
    };

    AddStepForm.prototype.hideGroup = function (inputSelector) {
        this.form.find(inputSelector).closest(".form-group").hide();
        this.form.find(inputSelector).removeAttr('required');
        this.form.find(inputSelector).val("");
    };
    
    AddStepForm.prototype.showGroup = function (inputSelector) {
        this.form.find(inputSelector).closest(".form-group").show();
        this.form.find(inputSelector).attr('required', 'required');
        this.form.find(inputSelector).val("");
    };
    
    return AddStepForm;
});