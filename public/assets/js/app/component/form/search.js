define(
    ['jquery', 'app/component/translator', 'app/component/routing', 'bootstrapTypehead'],
    function($, Translator, routing) {
        var SearchForm = function () {
            var self = this;
            
            this.form = $('#search-form');
            this.ingredientsCount = $('.search-ingredient').length;
            this.ingredientAutocompleteField = this.form.find('#search_ingredient_name');

            this.ingredientAutocompleteField.typeahead({
                source: function(query, process) {
                    return $.get(routing.generate('supply_autocomplete_ingredient', {query: query}), function(data) {
                        return process(data);
                    });
                },
                displayText: function(item) {
                    return item.name;
                },
                matcher: function(item) {
                    return true;
                },
                afterSelect: function(activeItem) {
                    var li = $('<li class="search-ingredient">'),
                        button = $('<div>'),
                        prototype = this.$element.data('prototype'),
                        input = null;

                    prototype = prototype.replace(/__name__/g, self.ingredientsCount);
                    input = $(prototype);
                    input.val(activeItem.id);
                    button.addClass('btn btn-primary');
                    button.html(activeItem.name + ' <span aria-hidden="true">&times;</span>');
                    
                    li.append(input);
                    li.append(button);
                    
                    $('#search-ingredient-list').append(li);
                    this.$element.val('');

                    self.ingredientsCount++;
                    self.bindTokenEvents();
                }
            });
            
            this.bindTokenEvents();
            this.disableEmptyFieldsOnSubmit();            
        };
        
        SearchForm.prototype.bindTokenEvents = function() {
            $('.search-ingredient').on('click', function(event) {
                var self = $(this);
                event.preventDefault();

                self.remove();
            });
        };

        SearchForm.prototype.disableEmptyFieldsOnSubmit = function() {
            this.form.on('submit', function(event) {
                var inputsCount = $(this).find('input').length,
                    disabledInputsCount = 0;
                
                $(this).find('input').each(function(){
                    var self = $(this);
                    if (self.val().length === 0) {
                        self.attr("disabled", "disabled");
                        disabledInputsCount++;
                    }
                });
                
                if (disabledInputsCount === inputsCount) {
                    window.location.href = routing.generate('home');
                    return false;
                } else {
                    return true;
                }
            })
        };

        return SearchForm;
});