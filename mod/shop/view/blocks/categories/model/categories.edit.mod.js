// Изменение категории

core.add({

    mod: {

        shop: {

            categories: {

                edit: {

                    init: function() {                                                                    // Инициализация изменения категории
                        
                        var that = this;
                        
                        this.block = core.mod.shop.categories.block;

                        $(this.block + '(link){change-parent}').click(function() {                        // Событие клика по заголовку "Изменить родительскую категорию"
                            return that.toggleParentForm.call(that, this);
                        });

                        $(this.block + '(cat){edit}').click(function() {                                  // Событие выбора родительской категории
                            return that.changeParent.call(that, this);
                        });
                    },

                    toggleParentForm: function(link) {                                                    // Скрытие/показ блока со списком для выбора родительской категории
                        
                        var arrow = $(link).children(this.block + '(harr)');                              // Элемент-стрелочка рядом с заголовком
                        
                        $(this.block + '(list){edit}')                                                    // Список категорий
                            .slideToggle(200, function() {                                                // Скрытие и отображение списка
                                
                                if($(this).is(':hidden'))                                                 // Изменение стрелочки
                                    arrow.html('&#9650;');
                                else
                                    arrow.html('&#9660;');
                            });

                        return false;                                                                     // Отмена перехода по ссылке
                    },

                    changeParent: function(category) {                                                    // Изменение родительской категории

                        var $category = $(category);
                        
                        if($category.bemGetMod('selected')) {                                             // Если клик по выбранной категории
                            
                            $category                                                                     // То у неё нужно
                                .bemDelMod('selected');                                                   // удалить модификатор выбранности

                            $(this.block + '(catparent)')                                                 // И удалить значение у скрытого поля
                                .attr('value', '');
                        }
                        else {                                                                            // Иначе клик по категории, не являвшейся родителем

                            $(this.block + '(cat){edit}')                                                 // У всех категорий
                                .bemDelMod('selected');                                                   // удаляется модификатор выбранности
                            
                            $category                                                                     // Выбранной категории
                                .bemSetMod('selected', 'yes');                                            // добавляется модификатор выбранности

                            $(this.block + '(catparent)')                                                 // Изменение значения скрытого поля
                                .attr('value', $category.attr('href'));
                        }
                        
                        return false;                                                                     // Отмена перехода по ссылке
                    }
                }
            }
        }
    }
});