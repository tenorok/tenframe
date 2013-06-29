BEM.DOM.decl('mod-shop-listcat', {

    onSetMod: {

        js: function() {

            var block = this;

            this.domElem
                .disableSelection()
                .sortable({
                    tolerance: 'pointer',                                   // Раздвигать элементы при наведении элемента
                    handle: this.buildSelector('draggable'),                // Элемент для зацепки
                    axis: 'y',                                              // Перемещение элементов допускается только по вертикали
                    containment: 'parent',                                  // Перемещение допускается только в родительском контейнере
                    start: function(e, ui) {
                        $(ui.placeholder).hide(300);                        // Добавление плавной анимации
                    },
                    change: function(e, ui) {
                        $(ui.placeholder).hide().show(300);                 // Добавление плавной анимации
                    },
                    update: function(data) {
                        block.serializeList(data);
                    }
                });
        }

    },

    /**
     * Сохранение новой последовательности категорий
     * @param {jQuery.Event} data Результат сортировки
     */
    serializeList: function(data) {

        var items = $(data.target).children(),                              // Все участвующие в сортировке категории
            order = [];                                                     // Массив с номерами категорий в отсортированном порядке

        items.each(function(i, item) {
            order.push($(item).attr('id').split('_')[1]);
        });

        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: '/mod/shop/app/ajax/categories.php',
            data: {
                event: 'sort',
                categories: order
            }
        }).fail(function(data) {
            console.log(data.responseText);
        });
    }

});